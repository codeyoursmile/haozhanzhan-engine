<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

// 无需认证的路由
Route::post('/admin/login', [AuthController::class, 'login']);

// 需要认证的路由
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// 页面管理路由（需要认证）
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/pages', [App\Http\Controllers\Admin\PageController::class, 'index']);
    Route::post('/pages', [App\Http\Controllers\Admin\PageController::class, 'store']);
    Route::get('/pages/{id}', [App\Http\Controllers\Admin\PageController::class, 'show']);
    Route::put('/pages/{id}', [App\Http\Controllers\Admin\PageController::class, 'update']);
    Route::delete('/pages/{id}', [App\Http\Controllers\Admin\PageController::class, 'destroy']);
});