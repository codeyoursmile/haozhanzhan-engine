<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade')->comment('关联页面ID');
            $table->string('component_type', 50)->comment('组件类型：banner/text/image/multi_col/map/contact_form');
            $table->json('content')->nullable()->comment('组件内容（标题、图片、文字、链接等）');
            $table->json('settings')->nullable()->comment('样式设置（背景色、内边距、对齐方式等）');
            $table->integer('sort_order')->default(0)->comment('排序，数值越小越靠前');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_components');
    }
};