<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('sites', function (Blueprint $table) {
        $table->id();
        $table->string('site_name', 255)->default('好站站企业官网')->comment('网站名称');
        $table->string('site_logo', 500)->default('/logo.png')->comment('LOGO路径，相对于public目录');
        $table->string('site_keywords', 500)->default('企业建站,可视化建站,拖拽建站,好站站,企业官网,建站引擎')->comment('SEO关键词');
        $table->text('site_description')->nullable()->comment('SEO描述');
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};