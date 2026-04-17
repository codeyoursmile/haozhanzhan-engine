<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade')->comment('关联站点ID');
            $table->string('title', 255)->default('新页面')->comment('页面标题');
            $table->string('slug', 255)->comment('访问别名，如：index、about');
            $table->boolean('is_home')->default(false)->comment('是否为首页：1是0否');
            $table->boolean('status')->default(false)->comment('发布状态：0草稿 1已发布');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};