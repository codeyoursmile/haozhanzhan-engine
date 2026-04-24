<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\PageRenderService;

class PageController extends Controller
{
    protected $renderService;
    
    public function __construct(PageRenderService $renderService)
    {
        $this->renderService = $renderService;
    }
    
    /**
     * 首页
     */
    public function home()
    {
        $page = Page::where('is_home', true)->first();
        if (!$page) {
            $page = Page::first();
        }
        
        $pages = Page::where('status', true)->orderBy('sort_order')->get();
        $html = $this->renderService->render($page);
        
        return view('frontend.layouts.app', [
            'title' => $page->title,
            'siteName' => $this->getSiteName(),
            'siteKeywords' => $this->getSiteKeywords(),
            'siteDescription' => $this->getSiteDescription(),
            'pages' => $pages,
            'content' => $html,
        ]);
    }
    
    /**
     * 自定义页面
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $pages = Page::where('status', true)->orderBy('sort_order')->get();
        $html = $this->renderService->render($page);
        
        return view('frontend.layouts.app', [
            'title' => $page->title,
            'siteName' => $this->getSiteName(),
            'siteKeywords' => $this->getSiteKeywords(),
            'siteDescription' => $this->getSiteDescription(),
            'pages' => $pages,
            'content' => $html,
        ]);
    }
    
    protected function getSiteName()
    {
        $site = \App\Models\Site::first();
        return $site->site_name ?? '好站站';
    }
    
    protected function getSiteKeywords()
    {
        $site = \App\Models\Site::first();
        return $site->site_keywords ?? '';
    }
    
    protected function getSiteDescription()
    {
        $site = \App\Models\Site::first();
        return $site->site_description ?? '';
    }
}