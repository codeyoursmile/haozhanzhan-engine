<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageComponent;

class PageRenderService
{
    /**
     * 渲染整个页面为 HTML
     */
    public function render(Page $page): string
    {
        $components = $page->components()->orderBy('sort_order')->get();
        
        $html = '';
        foreach ($components as $component) {
            $html .= $this->renderComponent($component);
        }
        
        return $this->wrapWithLayout($page, $html);
    }
    
    /**
     * 渲染单个组件
     */
    protected function renderComponent(PageComponent $component): string
    {
        $content = $component->content ?? [];
        $settings = $component->settings ?? [];
        
        $method = 'render' . ucfirst($component->component_type);
        if (method_exists($this, $method)) {
            return $this->$method($content, $settings);
        }
        
        return $this->renderDefault($content, $settings);
    }
    
    /**
     * 渲染横幅组件
     */
    protected function renderBanner(array $content, array $settings): string
    {
        $title = $content['title'] ?? '';
        $subtitle = $content['subtitle'] ?? '';
        $imageUrl = $content['image_url'] ?? '';
        $linkUrl = $content['link_url'] ?? '';
        
        $style = $imageUrl ? 'background-image: url(' . e($imageUrl) . '); background-size: cover; background-position: center;' : '';
        
        $html = '<div class="banner" style="' . $style . '">';
        $html .= '<div class="banner-content">';
        $html .= '<h1>' . e($title) . '</h1>';
        if ($subtitle) {
            $html .= '<p>' . e($subtitle) . '</p>';
        }
        if ($linkUrl) {
            $html .= '<a href="' . e($linkUrl) . '" class="banner-btn">了解更多</a>';
        }
        $html .= '</div></div>';
        
        return $html;
    }
    
    /**
     * 渲染文本组件
     */
    protected function renderText(array $content, array $settings): string
    {
        $title = $content['title'] ?? '';
        $text = $content['text'] ?? '';
        
        $html = '<div class="text-block">';
        if ($title) {
            $html .= '<h2>' . e($title) . '</h2>';
        }
        if ($text) {
            $html .= '<p>' . nl2br(e($text)) . '</p>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * 渲染图片组件
     */
    protected function renderImage(array $content, array $settings): string
    {
        $imageUrl = $content['image_url'] ?? '';
        $linkUrl = $content['link_url'] ?? '';
        $alt = $content['title'] ?? '';
        
        $img = '<img src="' . e($imageUrl) . '" alt="' . e($alt) . '">';
        
        if ($linkUrl) {
            return '<div class="image-block"><a href="' . e($linkUrl) . '">' . $img . '</a></div>';
        }
        
        return '<div class="image-block">' . $img . '</div>';
    }
    
    /**
     * 渲染多列布局组件
     */
    protected function renderMultiCol(array $content, array $settings): string
    {
        $columns = $content['columns'] ?? [];
        $columnCount = count($columns);
        
        if ($columnCount === 0) {
            return '';
        }
        
        $width = floor(12 / $columnCount);
        
        $html = '<div class="multi-col" style="padding: 60px 0;">';
        $html .= '<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">';
        $html .= '<div class="row" style="display: flex; flex-wrap: wrap; gap: 30px;">';
        
        foreach ($columns as $column) {
            $html .= '<div class="col" style="flex: 1; min-width: 250px;">';
            if (!empty($column['image_url'])) {
                $html .= '<img src="' . e($column['image_url']) . '" alt="' . e($column['title']) . '" style="width: 100%; border-radius: 8px; margin-bottom: 20px;">';
            }
            if (!empty($column['title'])) {
                $html .= '<h3 style="font-size: 20px; margin-bottom: 15px;">' . e($column['title']) . '</h3>';
            }
            if (!empty($column['content'])) {
                $html .= '<p style="color: #666; line-height: 1.6;">' . nl2br(e($column['content'])) . '</p>';
            }
            $html .= '</div>';
        }
        
        $html .= '</div></div></div>';
        
        return $html;
    }

    /**
    * 渲染地图组件
    */
    protected function renderMap(array $content, array $settings): string
    {
        $address = $content['address'] ?? '';
        $latitude = $content['latitude'] ?? '39.9042';
        $longitude = $content['longitude'] ?? '116.4074';
        $title = $content['title'] ?? '地图';
        
        $html = '<div class="map-block" style="padding: 40px 0;">';
        $html .= '<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">';
        if ($address) {
            $html .= '<div style="text-align: center; margin-bottom: 20px;">';
            $html .= '<p style="color: #666;">📍 ' . e($address) . '</p>';
            $html .= '</div>';
        }
        $html .= '<div style="background: #f5f5f5; border-radius: 8px; overflow: hidden; min-height: 300px; display: flex; align-items: center; justify-content: center;">';
        $html .= '<div style="text-align: center;">';
        $html .= '<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#1890ff" stroke-width="1.5">';
        $html .= '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>';
        $html .= '<circle cx="12" cy="9" r="2.5" fill="#1890ff"/>';
        $html .= '</svg>';
        $html .= '<p style="color: #999; margin-top: 10px;">地图预览区域</p>';
        $html .= '<p style="color: #999; font-size: 12px;">' . e($title) . '</p>';
        $html .= '</div></div></div></div>';
        
        return $html;
    }

    /**
     * 渲染联系表单组件
     */
    protected function renderContactForm(array $content, array $settings): string
    {
        $title = $content['title'] ?? '联系我们';
        $subtitle = $content['subtitle'] ?? '';
        $email = $content['email'] ?? '';
        $phone = $content['phone'] ?? '';
        $address = $content['address'] ?? '';
        
        $html = '<div class="contact-block" style="padding: 60px 0; background: #f8f9fa;">';
        $html .= '<div class="container" style="max-width: 800px; margin: 0 auto; padding: 0 20px;">';
        $html .= '<h2 style="text-align: center; font-size: 32px; margin-bottom: 15px;">' . e($title) . '</h2>';
        if ($subtitle) {
            $html .= '<p style="text-align: center; color: #666; margin-bottom: 40px;">' . e($subtitle) . '</p>';
        }
        
        $html .= '<div style="display: flex; flex-wrap: wrap; gap: 40px;">';
        $html .= '<div style="flex: 1; min-width: 250px;">';
        $html .= '<h3 style="margin-bottom: 20px;">联系方式</h3>';
        if ($email) {
            $html .= '<p style="margin-bottom: 15px; color: #666;">📧 <a href="mailto:' . e($email) . '" style="color: #1890ff; text-decoration: none;">' . e($email) . '</a></p>';
        }
        if ($phone) {
            $html .= '<p style="margin-bottom: 15px; color: #666;">📞 ' . e($phone) . '</p>';
        }
        if ($address) {
            $html .= '<p style="margin-bottom: 15px; color: #666;">📍 ' . e($address) . '</p>';
        }
        $html .= '</div>';
        
        $html .= '<div style="flex: 2; min-width: 300px;">';
        $html .= '<form method="POST" action="/contact/submit" style="display: flex; flex-direction: column; gap: 15px;">';
        $html .= '<input type="text" name="name" placeholder="您的姓名" style="padding: 12px; border: 1px solid #ddd; border-radius: 6px;">';
        $html .= '<input type="email" name="email" placeholder="您的邮箱" style="padding: 12px; border: 1px solid #ddd; border-radius: 6px;">';
        $html .= '<textarea name="message" rows="4" placeholder="留言内容" style="padding: 12px; border: 1px solid #ddd; border-radius: 6px;"></textarea>';
        $html .= '<button type="submit" style="background: #1890ff; color: #fff; border: none; padding: 12px; border-radius: 6px; cursor: pointer;">提交留言</button>';
        $html .= '</form>';
        $html .= '</div></div></div></div>';
        
        return $html;
    }
    /**
     * 默认渲染（未实现的组件类型）
     */
    protected function renderDefault(array $content, array $settings): string
    {
        return '<div class="component">组件开发中</div>';
    }
    
    /**
     * 包裹页面布局
     */
    protected function wrapWithLayout(Page $page, string $content): string
    {
        $site = \App\Models\Site::first();
        $pages = \App\Models\Page::where('status', true)->orderBy('sort_order')->get();
        
        return view('frontend.layouts.app', [
            'title' => $page->title,
            'siteName' => $site->site_name ?? '好站站',
            'siteKeywords' => $site->site_keywords ?? '',
            'siteDescription' => $site->site_description ?? '',
            'pages' => $pages,
            'content' => $content,
        ])->render();
    }
}