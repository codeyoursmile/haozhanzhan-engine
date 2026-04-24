<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Services\PageRenderService;
use Illuminate\Console\Command;

class GenerateStaticPages extends Command
{
    protected $signature = 'generate:static';
    protected $description = '生成静态页面文件';

    protected $renderService;

    public function __construct(PageRenderService $renderService)
    {
        parent::__construct();
        $this->renderService = $renderService;
    }

    public function handle()
    {
        $this->info('开始生成静态页面...');

        $pages = Page::where('status', true)->get();

        if ($pages->isEmpty()) {
            $this->warn('没有已发布的页面');
            return 0;
        }

        $generated = 0;

        foreach ($pages as $page) {
            $html = $this->renderService->render($page);

            if ($page->is_home) {
                $filename = 'index.html';
            } else {
                $filename = $page->slug . '.html';
            }

            $path = public_path($filename);
            file_put_contents($path, $html);

            $this->info("已生成: {$filename}");
            $generated++;
        }

        $this->info("生成完成，共生成 {$generated} 个页面");
        return 0;
    }
}