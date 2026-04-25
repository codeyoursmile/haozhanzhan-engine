<?php
/**
 * 好站站企业建站引擎 - 一键安装向导
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', dirname(__DIR__));
define('ENV_PATH', ROOT_PATH . '/.env');
define('LOCK_FILE', ROOT_PATH . '/storage/install.lock');

if (file_exists(LOCK_FILE)) {
    header('Location: /');
    exit;
}

// ========== 环境监测 ==========
$envErrors = [];

if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    $envErrors[] = 'PHP 版本需要 >= 8.2，当前版本：' . PHP_VERSION;
}

$requiredExtensions = ['pdo', 'pdo_mysql', 'openssl', 'json', 'fileinfo', 'mbstring', 'tokenizer'];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $envErrors[] = 'PHP 扩展未启用：' . $ext;
    }
}

$writablePaths = [
    ROOT_PATH . '/storage',
    ROOT_PATH . '/storage/logs',
    ROOT_PATH . '/storage/framework',
    ROOT_PATH . '/bootstrap/cache',
];
foreach ($writablePaths as $path) {
    if (!is_writable($path)) {
        $envErrors[] = '目录不可写：' . $path;
    }
}

if (!is_writable(ROOT_PATH)) {
    $envErrors[] = '项目根目录不可写（无法创建 .env 文件）';
}

if (!empty($envErrors)) {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>环境检测失败</title><style>';
    echo 'body{font-family:sans-serif;background:#f0f2f5;display:flex;justify-content:center;align-items:center;height:100vh;}';
    echo '.card{background:#fff;border-radius:12px;padding:30px;max-width:600px;box-shadow:0 2px 12px rgba(0,0,0,0.1);}';
    echo 'h1{color:#e74c3c;margin-bottom:20px;}ul{color:#666;line-height:1.8;}li{margin:10px 0;}</style></head><body>';
    echo '<div class="card"><h1>⚠️ 环境检测失败</h1><p>您的服务器环境不满足安装要求：</p><ul>';
    foreach ($envErrors as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul><p>请解决以上问题后刷新页面重新安装。</p></div></body></html>';
    exit;
}
// ========== 环境监测结束 ==========

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
        $appKey = 'base64:' . base64_encode(random_bytes(32));
        $appUrl = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';

        $envContent = '';
        $envContent .= 'APP_NAME="好站站企业建站引擎"' . "\n";
        $envContent .= 'APP_ENV=production' . "\n";
        $envContent .= 'APP_KEY=' . $appKey . "\n";
        $envContent .= 'APP_DEBUG=false' . "\n";
        $envContent .= 'APP_URL=http://' . $appUrl . "\n";
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

        // ========== 创建所有表 ==========
        $pdo->exec("CREATE TABLE IF NOT EXISTS `sites` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `site_name` varchar(255) NOT NULL DEFAULT '好站站企业官网',
            `site_logo` varchar(500) DEFAULT '/logo.png',
            `site_keywords` varchar(500) DEFAULT '企业建站,可视化建站,拖拽建站,好站站',
            `site_description` text,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `pages` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `site_id` bigint unsigned NOT NULL,
            `title` varchar(255) NOT NULL DEFAULT '新页面',
            `slug` varchar(255) NOT NULL,
            `is_home` tinyint(1) NOT NULL DEFAULT '0',
            `status` tinyint(1) NOT NULL DEFAULT '0',
            `sort_order` int NOT NULL DEFAULT '0',
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `page_components` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `page_id` bigint unsigned NOT NULL,
            `component_type` varchar(50) NOT NULL,
            `content` json DEFAULT NULL,
            `settings` json DEFAULT NULL,
            `sort_order` int NOT NULL DEFAULT '0',
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

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

        $pdo->exec("CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `tokenable_type` varchar(255) NOT NULL,
            `tokenable_id` bigint unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `token` varchar(64) NOT NULL UNIQUE,
            `abilities` text,
            `last_used_at` timestamp NULL,
            `expires_at` timestamp NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
            `email` varchar(255) NOT NULL PRIMARY KEY,
            `token` varchar(255) NOT NULL,
            `created_at` timestamp NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

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

        $pdo->exec("CREATE TABLE IF NOT EXISTS `cache` (
            `key` varchar(255) NOT NULL PRIMARY KEY,
            `value` mediumtext NOT NULL,
            `expiration` int NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

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

        // 添加外键
        try {
            $pdo->exec("ALTER TABLE `pages` ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`site_id`) REFERENCES `sites`(`id`) ON DELETE CASCADE");
        } catch (PDOException $e) {}
        try {
            $pdo->exec("ALTER TABLE `page_components` ADD CONSTRAINT `page_components_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE");
        } catch (PDOException $e) {}

        // 插入站点数据
        $stmt = $pdo->query("SELECT COUNT(*) FROM sites");
        if ($stmt->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO sites (site_name, site_logo, site_keywords, site_description, created_at, updated_at) 
                VALUES ('好站站企业官网', '/logo.png', '企业建站,可视化建站,拖拽建站,好站站', '好站站企业建站引擎', NOW(), NOW())");
        }
        $siteId = $pdo->lastInsertId() ?: 1;

        // 创建默认页面
        $stmt = $pdo->prepare("INSERT INTO pages (site_id, title, slug, is_home, status, sort_order, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, 0, NOW(), NOW())");
        $stmt->execute([$siteId, '首页', 'index', 1, 1]);
        $stmt->execute([$siteId, '关于我们', 'about', 0, 1]);

        // 创建管理员
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$admin_email]);
        $hashed = password_hash($admin_pass, PASSWORD_BCRYPT);

        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([$admin_name, $admin_email, $hashed]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, password = ?, updated_at = NOW() WHERE email = ?");
            $stmt->execute([$admin_name, $hashed, $admin_email]);
        }

        file_put_contents(LOCK_FILE, date('Y-m-d H:i:s'));
        header('Location: /');
        exit;
    }

    if (!empty($errors)) {
        if (file_exists(ENV_PATH)) unlink(ENV_PATH);
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
        body { background: #f0f2f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
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