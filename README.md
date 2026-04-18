## `README.md`

```markdown
# 好站站企业建站引擎

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.2-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12-red)](https://laravel.com)
[![Vue Version](https://img.shields.io/badge/Vue-3-green)](https://vuejs.org)
[![License](https://img.shields.io/badge/License-Commercial-red)]()

## 项目简介

好站站是一款基于 Laravel 12 + Vue 3 的企业建站引擎，支持可视化拖拽 DIY，一键生成静态企业官网，无需编程基础，快速搭建企业官网。

## 核心功能

| 功能 | 状态 | 说明 |
|------|------|------|
| 一键安装向导 | ✅ 已完成 | 傻瓜式安装，自动生成配置 |
| 可视化拖拽编辑 | 🔜 开发中 | 拖拽组件，实时预览 |
| 多页面管理 | 🔜 开发中 | 增删改查页面 |
| 静态页面生成 | 🔜 开发中 | 一键发布，生成 HTML |
| SEO 友好 | 🔜 开发中 | 自定义标题、关键词、描述 |
| 响应式设计 | 🔜 开发中 | 自适应 PC/移动端 |

## 技术栈

| 模块 | 技术 | 版本 |
|------|------|------|
| 后端框架 | Laravel | 12.x |
| 开发语言 | PHP | 8.3+ |
| 数据库 | MySQL | 8.0+ |
| 前端框架 | Vue | 3.x |
| UI 组件库 | Element Plus | 最新 |
| 拖拽库 | vue-draggable-next | 最新 |
| 状态管理 | Pinia | 最新 |
| Web 服务器 | Nginx / Apache | - |

## 快速开始

### 环境要求

- PHP >= 8.2
- MySQL >= 5.7
- Nginx / Apache
- Composer
- Node.js >= 18

### 安装步骤

1. **下载源码到服务器根目录**

2. **配置域名指向 `public` 目录**

   Nginx 配置示例：
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /path/to/engine-api/public;
       index index.php index.html;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include fastcgi_params;
           fastcgi_pass php_upstream;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
       }
   }
   ```

3. **访问网站**

   访问 `http://您的域名`，自动跳转到安装页面。

4. **填写安装信息**

   - 数据库主机、端口、数据库名、用户名、密码
   - 管理员姓名、邮箱、密码

5. **完成安装**

   点击「开始安装」，自动生成配置文件、创建数据表、创建管理员账号。

6. **登录后台**

   访问 `http://您的域名/login`，使用管理员账号登录。

## 目录结构

```
engine-api/
├── public/
│   ├── index.php          # 入口文件（含安装检测）
│   └── install.php        # 一键安装向导
├── database/
│   └── migrations/        # 数据表迁移文件
├── app/
│   ├── Models/            # 数据模型
│   │   ├── Site.php       # 站点配置模型
│   │   ├── Page.php       # 页面模型
│   │   └── PageComponent.php  # 组件模型
│   └── Http/Controllers/  # 控制器
├── resources/
│   └── js/                # Vue 3 前端源码
├── routes/
│   └── web.php            # 路由配置
├── .env.example           # 环境配置示例
└── README.md              # 项目说明
```

## 开发进度

| 阶段 | 任务 | 状态 |
|------|------|------|
| 0 | 环境搭建 + Git 仓库 | ✅ 已完成 |
| 1 | 数据库迁移 | ✅ 已完成 |
| 2 | Laravel Breeze 后台认证 | ✅ 已完成 |
| 3 | 一键安装向导 | ✅ 已完成 |
| 4 | Vue 3 + Element Plus 后台 | 🔜 开发中 |
| 5 | 页面管理功能 | 🔜 开发中 |
| 6 | 可视化拖拽编辑器 | 🔜 开发中 |
| 7 | 静态页面生成器 | 🔜 开发中 |

## 更新日志

### 2026-04-18

- 完成一键安装向导功能
- 更新 README.md

### 2026-04-17

- 安装 Laravel Breeze 后台认证
- 完成数据库迁移
- 完成整体规划文档

### 2026-04-16

- 搭建 Laravel 12 开发环境
- 初始化 Git 仓库

## 版权信息

© 2026 南京可道有思科技有限公司 版权所有

## License

商业软件，未经授权请勿随意传播、复制或二次分发。
```

---