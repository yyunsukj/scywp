# 个人云盘系统 - Vue + Art Design Pro 后台

## 🎯 系统概览

这是一个现代化的个人云盘系统，采用前后端分离架构：
- **前端**: Vue 3 + Art Design Pro (Element Plus)
- **后端**: PHP + MySQL + 阿里云OSS

## 🚀 访问地址

### 前台系统
- **地址**: https://8000-792e9f794bd9f5a4.monkeycode-ai.online
- **登录账号**: admin / admin
- **功能**: 文件上传、下载、管理

### 后台管理
- **地址**: https://3006-792e9f794bd9f5a4.monkeycode-ai.online
- **登录账号**: admin / admin123
- **功能**: 用户管理、文件管理、系统设置

## 📁 项目结构

```
/workspace/
├── api/                    # PHP后端API
│   ├── login.php          # 登录接口
│   ├── users.php          # 用户管理接口
│   └── files.php          # 文件管理接口
├── admin-vue/             # Vue前端后台
│   ├── src/               # 源代码
│   ├── dist/              # 构建输出
│   └── package.json       # 项目配置
├── admin/                 # 原PHP后台(已弃用)
├── index.php              # 前台主文件
├── download.php           # 文件下载
├── template.php           # 前台模板
├── config.php             # 系统配置
└── .env                   # 环境变量
```

## 🔧 技术栈

### 前端
- Vue 3.5.22
- Element Plus 2.11.4
- TypeScript 5.6.3
- Vite 7.1.7
- Pinia 3.0.3
- Vue Router 4.5.1
- Tailwind CSS 4.1.14

### 后端
- PHP 8.2.31
- MySQL
- 阿里云OSS

## 🚦 启动服务

### 前台服务 (端口 8000)
```bash
php -S localhost:8000
```

### 后台服务 (端口 3006)
```bash
cd admin-vue
pnpm dev
```

## 🔌 API接口说明

### 登录接口
- **URL**: `/api/login.php`
- **方法**: POST
- **请求体**:
```json
{
  "username": "admin",
  "password": "admin123"
}
```
- **响应**:
```json
{
  "code": 200,
  "message": "登录成功",
  "data": {
    "token": "session_id",
    "userInfo": {
      "username": "admin",
      "roles": ["admin"]
    }
  }
}
```

### 用户列表接口
- **URL**: `/api/users.php`
- **方法**: GET
- **响应**:
```json
{
  "code": 200,
  "data": [
    {
      "id": 1,
      "username": "admin",
      "createTime": "2026-05-23 00:00:00",
      "status": "active"
    }
  ]
}
```

### 文件列表接口
- **URL**: `/api/files.php`
- **方法**: GET
- **响应**:
```json
{
  "code": 200,
  "data": [
    {
      "id": 1,
      "fileName": "example.txt",
      "fileSize": 1024,
      "uploadTime": "2026-05-23 00:00:00",
      "username": "admin"
    }
  ]
}
```

## 🎨 功能特性

### 前台功能
- ✅ 文件上传 (支持拖拽)
- ✅ 文件下载 (单个/批量)
- ✅ 文件管理 (删除、重命名、移动)
- ✅ 文件分享
- ✅ 存储空间管理
- ✅ 文件预览

### 后台功能
- ✅ 用户管理
- ✅ 文件管理
- ✅ 数据统计
- ✅ 权限管理
- ✅ 系统设置

## 🔐 安全特性

- Session会话管理
- 密码加密存储
- OSS防盗链设置
- API接口鉴权
- 跨域请求控制

## 📱 响应式设计

- 完美支持PC端
- 支持平板设备
- 移动端适配

## 🚀 部署说明

### 前端部署
1. 构建生产版本: `cd admin-vue && pnpm build`
2. 将 `dist/` 目录内容部署到Web服务器

### 后端部署
1. 配置Web服务器 (Apache/Nginx)
2. 设置正确的PHP环境
3. 配置MySQL数据库
4. 配置阿里云OSS

## 🐛 常见问题

### 1. 跨域问题
确保API接口设置了正确的CORS头：
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
```

### 2. 登录失败
检查PHP Session是否正常工作：
```php
session_start();
$_SESSION['admin_logged_in'] = true;
```

### 3. API接口404
检查Web服务器配置是否正确路由请求到 `api/` 目录

## 📞 技术支持

- Art Design Pro文档: https://www.artd.pro/docs
- Element Plus文档: https://element-plus.org
- Vue.js文档: https://vuejs.org

## 📄 许可证

MIT License

---

**最后更新**: 2026-05-23
**版本**: v1.0.0