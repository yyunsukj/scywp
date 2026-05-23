# 后台管理系统

## 目录结构
```
admin/
├── auth.php       # 后台登录认证
├── index.php      # 后台主页（控制台）
├── users.php      # 用户管理
├── files.php      # 文件管理
├── settings.php   # 系统设置
└── login.php      # 后台登录页面（已弃用，使用auth.php）
```

## 功能说明

### 1. 登录认证
- 访问 `admin/auth.php` 进入登录页面
- 默认管理员账号：`admin`
- 默认管理员密码：`admin123`

### 2. 控制台（index.php）
- 显示系统统计数据
- 用户总数、文件总数、存储使用情况
- 最近上传的文件列表

### 3. 用户管理（users.php）
- 查看所有注册用户
- 删除普通用户（不能删除管理员）
- 显示用户注册时间和ID

### 4. 文件管理（files.php）
- 查看所有上传文件
- 删除文件（同时从OSS和数据库删除）
- 显示文件大小、上传时间、所属用户

### 5. 系统设置（settings.php）
- 查看系统版本信息
- 查看PHP和MySQL版本
- 显示默认管理员账号信息

## 安全提示

1. 默认密码仅为演示使用，建议修改
2. 后台管理系统需要独立登录验证
3. 所有删除操作都有确认提示
4. 管理员无法删除自己的账号

## 访问地址

- 后台登录：`https://你的域名/admin/auth.php`
- 后台首页：`https://你的域名/admin/index.php`
- 用户管理：`https://你的域名/admin/users.php`
- 文件管理：`https://你的域名/admin/files.php`
- 系统设置：`https://你的域名/admin/settings.php`

## 技术栈

- 前端：Tailwind CSS + Font Awesome 4.7
- 后端：PHP
- 数据库：MySQL
- 存储服务：阿里云OSS

## 扩展建议

1. 添加管理员密码修改功能
2. 实现权限分级管理
3. 添加系统日志功能
4. 实现数据备份功能
5. 添加系统监控面板