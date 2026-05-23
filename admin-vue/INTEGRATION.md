# Art Design Pro 后台管理系统集成说明

## 🎯 项目介绍

Art Design Pro 是一个基于 Vue 3 + TypeScript + Vite + Element Plus 的现代化后台管理系统模板，专注于用户体验和视觉设计。

## 🚀 技术栈

- **前端框架**: Vue 3.5.22
- **开发工具**: Vite 7.1.7
- **UI组件库**: Element Plus 2.11.4
- **状态管理**: Pinia 3.0.3
- **路由管理**: Vue Router 4.5.1
- **样式方案**: Tailwind CSS 4.1.14
- **图表库**: ECharts 6.0.0
- **国际化**: Vue I18n 9.14.5
- **类型安全**: TypeScript 5.6.3

## 📦 安装与启动

### 前置要求
- Node.js >= 20.19.0
- pnpm >= 8.8.0

### 安装步骤

```bash
# 克隆项目
git clone https://github.com/Daymychen/art-design-pro.git admin-vue
cd admin-vue

# 安装依赖
pnpm install

# 批准构建脚本（可选）
pnpm approve-builds --all

# 启动开发服务器
pnpm dev

# 构建生产版本
pnpm build
```

### 当前运行状态
- **开发服务器**: `http://localhost:3006/`
- **预览地址**: `https://3006-792e9f794bd9f5a4.monkeycode-ai.online`
- **运行端口**: 3006

## 🔌 PHP 后端集成

### 1. 创建 API 接口

在现有的 PHP 项目中创建以下 API 接口：

```php
// api/login.php - 登录接口
<?php
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['username'] === 'admin' && $data['password'] === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $data['username'];
        echo json_encode([
            'code' => 200,
            'message' => '登录成功',
            'data' => [
                'token' => session_id(),
                'userInfo' => [
                    'username' => 'admin',
                    'avatar' => '',
                    'roles' => ['admin']
                ]
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'code' => 401,
            'message' => '账号或密码错误'
        ]);
    }
}
?>

// api/users.php - 用户管理接口
<?php
header('Content-Type: application/json');
session_start();

// 验证登录状态
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['code' => 401, 'message' => '未登录']);
    exit;
}

require_once 'config.php';

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 获取用户列表
    $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
    echo json_encode([
        'code' => 200,
        'data' => array_map(function($user) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => '',
                'phone' => '',
                'avatar' => '',
                'createTime' => $user['created_at'],
                'status' => 'active'
            ];
        }, $users)
    ]);
}
?>

// api/files.php - 文件管理接口
<?php
header('Content-Type: application/json');
session_start();

// 验证登录状态
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['code' => 401, 'message' => '未登录']);
    exit;
}

require_once 'config.php';

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 获取文件列表
    $files = $conn->query("SELECT f.*, u.username FROM files f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.upload_time DESC")->fetch_all(MYSQLI_ASSOC);
    echo json_encode([
        'code' => 200,
        'data' => array_map(function($file) {
            return [
                'id' => $file['id'],
                'fileName' => $file['file_name'],
                'fileSize' => $file['file_size'],
                'uploadTime' => $file['upload_time'],
                'username' => $file['username']
            ];
        }, $files)
    ]);
}
?>
```

### 2. 修改 Vite 配置

修改 `admin-vue/vite.config.ts` 中的代理配置：

```typescript
server: {
  port: Number(VITE_PORT),
  proxy: {
    '/api': {
      target: 'http://localhost:8000', // PHP后端地址
      changeOrigin: true,
      rewrite: (path) => path.replace(/^\/api/, '')
    }
  },
  host: true,
  allowedHosts: ['.monkeycode-ai.online']
}
```

### 3. 修改环境变量

修改 `admin-vue/.env`：

```env
VITE_PORT = 3006
VITE_BASE_URL = /
VITE_API_URL = /api
VITE_API_PROXY_URL = http://localhost:8000
VITE_ACCESS_MODE = backend
VITE_WITH_CREDENTIALS = true
```

## 🎨 功能特性

### 已包含功能
- ✅ 现代化UI设计
- ✅ 响应式布局
- ✅ 暗色/亮色主题切换
- ✅ 用户管理
- ✅ 文件管理
- ✅ 数据可视化图表
- ✅ 权限管理
- ✅ 国际化支持
- ✅ 表单验证
- ✅ 文件上传
- ✅ 数据导出

### 需要集成功能
- 🔌 连接PHP后端API
- 🔌 实现真实的CRUD操作
- 🔌 文件上传到OSS
- 🔌 用户权限验证
- 🔌 数据统计功能

## 📁 项目结构

```
admin-vue/
├── public/              # 静态资源
├── src/
│   ├── api/            # API接口
│   ├── assets/         # 资源文件
│   ├── components/     # 组件
│   ├── layouts/        # 布局
│   ├── router/         # 路由
│   ├── store/          # 状态管理
│   ├── styles/         # 样式
│   ├── utils/          # 工具函数
│   ├── views/          # 页面
│   └── main.ts         # 入口文件
├── .env                # 环境变量
├── package.json        # 项目配置
└── vite.config.ts      # Vite配置
```

## 🔧 开发指南

### 添加新页面
1. 在 `src/views/` 中创建页面组件
2. 在 `src/router/` 中添加路由配置
3. 在侧边栏导航中添加菜单项

### 调用API示例
```typescript
import { http } from '@/utils/http'

// GET请求
const getUsers = async () => {
  const res = await http.get('/users.php')
  return res.data
}

// POST请求
const login = async (data: LoginParams) => {
  const res = await http.post('/login.php', data)
  return res.data
}
```

## 🚢 部署

### 构建生产版本
```bash
pnpm build
```

构建后的文件在 `dist/` 目录中，可以部署到任何静态文件服务器。

## 📞 技术支持

- 官方文档: https://www.artd.pro/docs
- 在线演示: https://www.artd.pro
- GitHub: https://github.com/Daymychen/art-design-pro

## 🎯 下一步

1. 修改API配置连接到PHP后端
2. 实现真实的数据交互
3. 自定义主题和样式
4. 添加业务特定功能
5. 部署到生产环境