<?php
/**
 * 好站站企业建站引擎 - 一键安装向导
 * 纯 PHP 实现，不依赖 exec()，跨平台兼容
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', dirname(__DIR__));
define('ENV_PATH', ROOT_PATH . '/.env');
define('LOCK_FILE', ROOT_PATH . '/storage/install.lock');

// 如果已安装，跳转首页
if (file_exists(LOCK_FILE)) {
    header('Location: /');
    exit;
}

// 处理安装提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $db_host   = trim($_POST['db_host'] ?? '127.0.0.1');
    $db_port   = trim($_POST['db_port'] ?? '3306');
    $db_name   = trim($_POST['db_name'] ?? '');
    $db_user   = trim($_POST['db_user'] ?? '');
    $db_pass   = $_POST['db_pass'] ?? '';
    $admin_name  = trim($_POST['admin_name'] ?? '管理员');
    $admin_email = trim($_POST['admin_email'] ?? '');
    $admin_pass  = $_POST['admin_pass'] ?? '';

    if (empty($db_name)) $errors[] = '请填写数据库名';
    if (empty($db_user)) $errors[] = '请填写数据库用户名';
    if (empty($admin_email)) $errors[] = '请填写管理员邮箱';
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) $errors[] = '管理员邮箱格式不正确';
    if (strlen($admin_pass) < 6) $errors[] = '管理员密码至少6位';

    // 测试数据库连接
    if (empty($errors)) {
        try {
            $pdo = new PDO("mysql:host=$db_host;port=$db_port;charset=utf8mb4", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$db_name`");
        } catch (PDOException $e) {
            $errors[] = '数据库连接失败：' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        // 生成 APP_KEY
        $appKey = 'base64:' . base64_encode(random_bytes(32));

        // 写入 .env 文件
        $envContent = '';
        $envContent .= 'APP_NAME="好站站企业建站引擎"' . "\n";
        $envContent .= 'APP_ENV=production' . "\n";
        $envContent .= 'APP_KEY=' . $appKey . "\n";
        $envContent .= 'APP_DEBUG=false' . "\n";
        $envContent .= 'APP_URL=http://' . $_SERVER['HTTP_HOST'] . "\n";
        $envContent .= "\n";
        $envContent .= 'DB_CONNECTION=mysql' . "\n";
        $envContent .= 'DB_HOST=' . $db_host . "\n";
        $envContent .= 'DB_PORT=' . $db_port . "\n";
        $envContent .= 'DB_DATABASE=' . $db_name . "\n";
        $envContent .= 'DB_USERNAME=' . $db_user . "\n";
        $envContent .= 'DB_PASSWORD=' . $db_pass . "\n";
        $envContent .= "\n";
        $envContent .= 'SESSION_DRIVER=database' . "\n";
        $envContent .= 'QUEUE_CONNECTION=database' . "\n";
        $envContent .= 'CACHE_STORE=database' . "\n";

        file_put_contents(ENV_PATH, $envContent);

        // 执行数据库迁移（纯 SQL，不依赖 exec）
        $migrationFiles = glob(ROOT_PATH . '/database/migrations/*.php');
        $migrateSuccess = true;

        foreach ($migrationFiles as $file) {
            $content = file_get_contents($file);
            
            // 解析表名
            preg_match('/Schema::create\([\'"]([^\'"]+)[\'"]/', $content, $matches);
            if (!isset($matches[1])) {
                continue;
            }
            $table = $matches[1];
            
            // 检查表是否已存在
            $result = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($result->rowCount() > 0) {
                continue; // 表已存在，跳过
            }
            
            // 根据表名生成 SQL
            $sql = $pdo->prepare("SELECT 1");
            try {
                if ($table === 'sites') {
                    $pdo->exec("CREATE TABLE IF NOT EXISTS `sites` (
                        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `site_name` varchar(255) NOT NULL DEFAULT '好站站企业官网',
                        `site_logo` varchar(500) DEFAULT '/logo.png',
                        `site_keywords` varchar(500) DEFAULT '企业建站,可视化建站,拖拽建站,好站站',
                        `site_description` text,
                        `created_at` timestamp NULL,
                        `updated_at` timestamp NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                } elseif ($table === 'pages') {
                    $pdo->exec("CREATE TABLE IF NOT EXISTS `pages` (
                        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `site_id` bigint unsigned NOT NULL,
                        `title` varchar(255) NOT NULL DEFAULT '新页面',
                        `slug` varchar(255) NOT NULL,
                        `is_home` tinyint(1) NOT NULL DEFAULT '0',
                        `status` tinyint(1) NOT NULL DEFAULT '0',
                        `created_at` timestamp NULL,
                        `updated_at` timestamp NULL,
                        FOREIGN KEY (`site_id`) REFERENCES `sites`(`id`) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                } elseif ($table === 'page_components') {
                    $pdo->exec("CREATE TABLE IF NOT EXISTS `page_components` (
                        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `page_id` bigint unsigned NOT NULL,
                        `component_type` varchar(50) NOT NULL,
                        `content` json DEFAULT NULL,
                        `settings` json DEFAULT NULL,
                        `sort_order` int NOT NULL DEFAULT '0',
                        `created_at` timestamp NULL,
                        `updated_at` timestamp NULL,
                        FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                }
            } catch (PDOException $e) {
                $migrateSuccess = false;
                $errors[] = "创建表 `$table` 失败：" . $e->getMessage();
                break;
            }
        }

        // 运行 Laravel 默认的迁移（users、sessions 等）
        if ($migrateSuccess) {
            try {
                // 创建 users 表（如果不存在）
                $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `name` varchar(255) NOT NULL,
                    `email` varchar(255) NOT NULL UNIQUE,
                    `email_verified_at` timestamp NULL,
                    `password` varchar(255) NOT NULL,
                    `remember_token` varchar(100) DEFAULT NULL,
                    `created_at` timestamp NULL,
                    `updated_at` timestamp NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // 创建 password_reset_tokens 表
                $pdo->exec("CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
                    `email` varchar(255) NOT NULL PRIMARY KEY,
                    `token` varchar(255) NOT NULL,
                    `created_at` timestamp NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // 创建 sessions 表
                $pdo->exec("CREATE TABLE IF NOT EXISTS `sessions` (
                    `id` varchar(255) NOT NULL PRIMARY KEY,
                    `user_id` bigint unsigned DEFAULT NULL,
                    `ip_address` varchar(45) DEFAULT NULL,
                    `user_agent` text,
                    `payload` longtext NOT NULL,
                    `last_activity` int NOT NULL,
                    INDEX `sessions_user_id_index` (`user_id`),
                    INDEX `sessions_last_activity_index` (`last_activity`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // 创建 cache 表
                $pdo->exec("CREATE TABLE IF NOT EXISTS `cache` (
                    `key` varchar(255) NOT NULL PRIMARY KEY,
                    `value` mediumtext NOT NULL,
                    `expiration` int NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                // 创建 jobs 表
                $pdo->exec("CREATE TABLE IF NOT EXISTS `jobs` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `queue` varchar(255) NOT NULL,
                    `payload` longtext NOT NULL,
                    `attempts` tinyint unsigned NOT NULL,
                    `reserved_at` int unsigned DEFAULT NULL,
                    `available_at` int unsigned NOT NULL,
                    `created_at` int unsigned NOT NULL,
                    INDEX `jobs_queue_index` (`queue`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            } catch (PDOException $e) {
                $migrateSuccess = false;
                $errors[] = '创建系统表失败：' . $e->getMessage();
            }
        }

        if ($migrateSuccess) {
            // 创建管理员用户
            $hashed = password_hash($admin_pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([$admin_name, $admin_email, $hashed]);

            // 获取 site_id（刚创建的第一个站点）
            $siteId = 1;

            // 创建默认页面
            $stmt = $pdo->prepare("INSERT INTO pages (site_id, title, slug, is_home, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$siteId, '首页', 'index', 1, 1]);
            $stmt->execute([$siteId, '关于我们', 'about', 0, 1]);

            // 生成安装锁文件
            file_put_contents(LOCK_FILE, date('Y-m-d H:i:s'));

            // 安装完成，跳转首页
            header('Location: /');
            exit;
        }
    }

    if (!empty($errors)) {
        // 安装失败，删除 .env 回滚
        if (file_exists(ENV_PATH)) {
            unlink(ENV_PATH);
        }
        $errorMsg = implode('<br>', $errors);
        echo "<script>alert('安装失败：\\n$errorMsg'); window.history.back();</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>好站站企业建站引擎 - 一键安装</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f0f2f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: #3b82f6; color: #fff; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; margin-bottom: 8px; }
        .header p { opacity: 0.9; }
        .body { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; }
        input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .row { display: flex; gap: 15px; }
        .row .form-group { flex: 1; }
        hr { margin: 20px 0; border: none; border-top: 1px solid #eee; }
        button { width: 100%; padding: 12px; background: #3b82f6; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; }
        button:hover { background: #2563eb; }
        .footer { text-align: center; padding: 20px; color: #888; font-size: 12px; border-top: 1px solid #eee; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <h1>好站站企业建站引擎</h1>
            <p>一键安装，轻松建站</p>
        </div>
        <form method="post">
            <div class="body">
                <h3>📁 数据库配置</h3>
                <div class="row">
                    <div class="form-group">
                        <label>数据库主机</label>
                        <input type="text" name="db_host" value="127.0.0.1" required>
                    </div>
                    <div class="form-group">
                        <label>端口</label>
                        <input type="text" name="db_port" value="3306" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>数据库名</label>
                    <input type="text" name="db_name" placeholder="例如: haozhanzhan" required>
                </div>
                <div class="form-group">
                    <label>数据库用户名</label>
                    <input type="text" name="db_user" value="root" required>
                </div>
                <div class="form-group">
                    <label>数据库密码</label>
                    <input type="password" name="db_pass" placeholder="留空如果无密码">
                </div>

                <hr>

                <h3>👤 管理员账号</h3>
                <div class="form-group">
                    <label>姓名</label>
                    <input type="text" name="admin_name" value="管理员" required>
                </div>
                <div class="form-group">
                    <label>邮箱</label>
                    <input type="email" name="admin_email" placeholder="admin@example.com" required>
                </div>
                <div class="form-group">
                    <label>密码（至少6位）</label>
                    <input type="password" name="admin_pass" required>
                </div>

                <button type="submit">开始安装</button>
            </div>
        </form>
        <div class="footer">
            © 2026 南京可道有思科技有限公司
        </div>
    </div>
</div>
</body>
</html>