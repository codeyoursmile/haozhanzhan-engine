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
        
        $html = $this->renderService->render($page);
        return response($html);
    }
    
    /**
     * 自定义页面
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $html = $this->renderService->render($page);
        return response($html);
    }
}