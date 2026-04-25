```markdown
# 好站站企业建站引擎

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.2-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12-red)](https://laravel.com)
[![Vue Version](https://img.shields.io/badge/Vue-3-green)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue)](https://www.typescriptlang.org/)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

## 项目简介

好站站是一款基于 Laravel 12 + Vue 3 + TypeScript 的企业建站引擎，支持可视化拖拽 DIY，一键生成静态企业官网，无需编程基础，快速搭建企业官网。

## 核心功能

| 功能 | 状态 | 说明 |
|------|------|------|
| 一键安装向导 | ✅ 已完成 | 傻瓜式安装，自动生成配置 |
| 可视化拖拽编辑 | ✅ 已完成 | 拖拽组件，实时预览 |
| 多页面管理 | ✅ 已完成 | 增删改查页面 |
| 静态页面生成 | ✅ 已完成 | 一键发布，生成 HTML |
| SEO 友好 | ✅ 已完成 | 自定义标题、关键词、描述 |
| 响应式设计 | ✅ 已完成 | 自适应 PC/移动端 |

## 技术栈

| 模块 | 技术 | 版本 |
|------|------|------|
| 后端框架 | Laravel | 12.x |
| 开发语言 | PHP | 8.3+ |
| 数据库 | MySQL | 8.0+ |
| 前端框架 | Vue 3 | 3.x |
| 开发语言 | TypeScript | 5.x |
| UI 组件库 | Element Plus | 最新 |
| 拖拽库 | SortableJS | 最新 |
| 状态管理 | Pinia | 最新 |
| Web 服务器 | Nginx / Apache | - |

## 环境要求

- PHP >= 8.2
- MySQL >= 5.7
- Nginx / Apache
- Composer
- Node.js >= 18

## 快速开始

### 1. 下载源码

```bash
git clone https://github.com/codeyoursmile/haozhanzhan-engine.git
cd haozhanzhan-engine
```

### 2. 配置域名

将域名指向 `public` 目录。

### 3. 一键安装

访问 `http://您的域名`，自动跳转到安装页面：

- 填写数据库信息
- 填写管理员账号
- 点击「开始安装」

### 4. 登录后台

访问 `http://您的域名/admin/login`，使用管理员账号登录。

### 5. 开始建站

- 进入「页面管理」创建页面
- 点击「编辑」进入可视化拖拽编辑器
- 拖拽组件到画布，编辑属性
- 点击「保存页面」
- 点击「一键发布」生成静态文件
- 访问首页查看效果

## 目录结构

```
engine-api/
├── public/
│   ├── index.php          # 入口文件（含安装检测）
│   └── install.php        # 一键安装向导
├── app/
│   ├── Models/            # 数据模型
│   ├── Http/Controllers/  # 控制器
│   └── Services/          # 服务类
├── database/migrations/    # 数据表迁移
├── resources/
│   ├── js/admin/          # Vue 3 + TypeScript 后台源码
│   └── views/frontend/    # 前台模板
└── routes/                # 路由配置
```

## 开发进度

| 板块 | 名称 | 状态 |
|------|------|------|
| 板块1 | 项目初始化与环境搭建 | ✅ 100% |
| 板块2 | 一键安装向导 | ✅ 100% |
| 板块3 | Vue 3 + Element Plus 后台 | ✅ 100% |
| 板块4 | 可视化拖拽编辑器 | ✅ 100% |
| 板块5 | 静态页面生成器 | ✅ 100% |
| 板块6 | 前台展示 | ✅ 100% |

## 开源协议

MIT License © 2026 南京可道有思科技有限公司

## 技术博客

[好站站！企业建站引擎 · 研发笔记](https://adorablecode.com)