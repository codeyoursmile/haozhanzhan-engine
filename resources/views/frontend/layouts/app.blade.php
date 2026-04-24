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
        
        /* 横幅样式 */
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
        
        /* 文本块样式 */
        .text-block { padding: 60px 0; text-align: center; max-width: 800px; margin: 0 auto; }
        .text-block h2 { font-size: 32px; margin-bottom: 20px; color: #333; }
        .text-block p { font-size: 16px; line-height: 1.8; color: #666; }
        
        /* 图片块样式 */
        .image-block { text-align: center; padding: 40px 0; }
        .image-block img { max-width: 100%; border-radius: 8px; }
        
        /* 响应式 */
        @media (max-width: 768px) {
            .banner h1 { font-size: 32px; }
            .banner p { font-size: 16px; }
            .text-block { padding: 40px 20px; }
            .text-block h2 { font-size: 24px; }
        }
    </style>
</head>
<body>
    {!! $content !!}
</body>
</html>