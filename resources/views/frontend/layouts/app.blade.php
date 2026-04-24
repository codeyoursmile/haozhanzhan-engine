<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $siteName }}</title>
    <meta name="keywords" content="{{ $siteKeywords }}">
    <meta name="description" content="{{ $siteDescription }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        
        /* 导航栏样式 */
        .navbar {
            background: #fff;
            border-bottom: 1px solid #e8e8e8;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #1890ff;
            text-decoration: none;
        }
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 30px;
            margin: 0;
            padding: 0;
        }
        .nav-link {
            color: #333;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #1890ff;
        }
        .nav-link.active {
            color: #1890ff;
            font-weight: bold;
        }
        
        /* 其他样式保持不变 */
        .banner {
            background-size: cover;
            background-position: center;
            min-height: 400px;
            display: flex;
            align-items: center;
            text-align: center;
            color: #fff;
            background-color: #1a1a2e;
        }
        .banner-content { max-width: 800px; margin: 0 auto; padding: 0 20px; }
        .banner h1 { font-size: 48px; margin-bottom: 20px; }
        .banner p { font-size: 20px; margin-bottom: 30px; }
        .banner-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #1890ff;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .banner-btn:hover { background: #0c6bcf; }
        
        .text-block { padding: 60px 0; text-align: center; max-width: 800px; margin: 0 auto; }
        .text-block h2 { font-size: 32px; margin-bottom: 20px; color: #333; }
        .text-block p { font-size: 16px; line-height: 1.8; color: #666; }
        
        .image-block { text-align: center; padding: 40px 0; }
        .image-block img { max-width: 100%; border-radius: 8px; }
        
        @media (max-width: 768px) {
            .banner h1 { font-size: 32px; }
            .banner p { font-size: 16px; }
            .text-block { padding: 40px 20px; }
            .text-block h2 { font-size: 24px; }
            .nav-menu { gap: 15px; }
        }
    </style>
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="/" class="logo">{{ $siteName }}</a>
            <ul class="nav-menu">
                @foreach($pages as $page)
                    <li class="nav-item">
                        <a href="/{{ $page->slug }}.html" class="nav-link">{{ $page->title }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>

    {!! $content !!}

    <script>
        // 当前页面高亮
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>