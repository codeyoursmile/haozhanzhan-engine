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
        
        return view('frontend.layouts.app', [
            'title' => $page->title,
            'siteName' => $site->site_name ?? '好站站',
            'siteKeywords' => $site->site_keywords ?? '',
            'siteDescription' => $site->site_description ?? '',
            'content' => $content,
        ])->render();
    }
}