<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\ComponentController;

// 无需认证的路由
Route::post('/admin/login', [AuthController::class, 'login']);

// 需要认证的路由
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // 页面管理
    Route::get('/pages', [PageController::class, 'index']);
    Route::post('/pages', [PageController::class, 'store']);
    Route::get('/pages/{id}', [PageController::class, 'show']);
    Route::put('/pages/{id}', [PageController::class, 'update']);
    Route::delete('/pages/{id}', [PageController::class, 'destroy']);
    
    // 一键发布
    Route::post('/publish', [PageController::class, 'publish']);
    
    // 站点配置
    Route::get('/site', [SiteController::class, 'index']);
    Route::put('/site', [SiteController::class, 'update']);

    // 组件管理
    Route::get('/pages/{pageId}/components', [ComponentController::class, 'getByPageId']);
    Route::post('/components', [ComponentController::class, 'store']);
    Route::put('/components/{id}', [ComponentController::class, 'update']);
    Route::delete('/components/{id}', [ComponentController::class, 'destroy']);
    Route::post('/components/sort', [ComponentController::class, 'updateSortOrder']);
});