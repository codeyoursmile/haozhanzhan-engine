<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('site')->get();
        return response()->json($pages);
    }

    


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages',
            'is_home' => 'boolean',
            'status' => 'boolean',
        ]);

        // 获取第一个站点的 ID（因为当前系统只有一个站点）
        $site = \App\Models\Site::first();
        if (!$site) {
            // 如果没有站点，创建一个默认站点
            $site = \App\Models\Site::create([
                'site_name' => '好站站企业官网',
                'site_logo' => '/logo.png',
            ]);
        }
        
        $validated['site_id'] = $site->id;
        $page = \App\Models\Page::create($validated);
        
        return response()->json($page, 201);
    }

    public function show($id)
    {
        $page = Page::with('components')->findOrFail($id);
        return response()->json($page);
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|max:255|unique:pages,slug,' . $id,
            'is_home' => 'boolean',
            'status' => 'boolean',
            'sort_order' => 'integer',  // 新增这一行
        ]);

        $page->update($validated);
        return response()->json($page);
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();
        return response()->json(null, 204);
    }
    /**
     * 一键发布 - 生成静态页面
     */
    public function publish()
    {
        try {
            Artisan::call('generate:static');
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => '静态页面生成成功',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '生成失败：' . $e->getMessage()
            ], 500);
        }
    }
}