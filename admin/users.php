<?php
session_start();
require_once '../config.php';

// 检查管理员登录状态
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: auth.php');
    exit;
}

$conn = getDBConnection();

// 获取所有用户
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

// 处理删除用户
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = $_GET['delete'];
    // 不能删除自己
    if ($userId != $_SESSION['admin_user_id']) {
        $conn->query("DELETE FROM files WHERE user_id = $userId");
        $conn->query("DELETE FROM users WHERE id = $userId");
        header('Location: users.php');
        exit;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - 用户管理</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/antd/5.12.2/reset.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
        }

        /* 侧边栏 */
        .ant-layout-sider {
            width: 256px;
            background: #001529;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .ant-layout-sider-children {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .logo {
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            background: #002140;
        }

        .logo i {
            font-size: 24px;
            color: #1890ff;
            margin-right: 16px;
        }

        .logo span {
            color: white;
            font-size: 18px;
            font-weight: 600;
        }

        .ant-menu {
            background: transparent;
            color: rgba(255, 255, 255, 0.65);
            border-right: none;
        }

        .ant-menu-item {
            display: flex;
            align-items: center;
            padding: 0 24px;
            margin: 4px 0;
            height: 40px;
            line-height: 40px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .ant-menu-item:hover {
            color: white;
        }

        .ant-menu-item-selected {
            background: #1890ff;
            color: white;
        }

        .ant-menu-item i {
            margin-right: 12px;
            font-size: 16px;
            width: 16px;
            text-align: center;
        }

        /* 主内容区域 */
        .ant-layout {
            flex: 1;
            margin-left: 256px;
            display: flex;
            flex-direction: column;
        }

        .ant-layout-header {
            height: 64px;
            background: white;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .ant-layout-content {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
        }

        .breadcrumb {
            color: rgba(0, 0, 0, 0.45);
            font-size: 14px;
            margin-bottom: 16px;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.85);
            margin-bottom: 8px;
        }

        .page-header p {
            color: rgba(0, 0, 0, 0.45);
            font-size: 14px;
        }

        /* 表格 */
        .ant-table-container {
            background: white;
            border-radius: 2px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            border: 1px solid #f0f0f0;
        }

        .ant-table-wrapper {
            padding: 24px;
        }

        .ant-table-title {
            font-size: 16px;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.85);
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ant-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .ant-table thead {
            background: #fafafa;
        }

        .ant-table th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.85);
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        .ant-table td {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
            color: rgba(0, 0, 0, 0.65);
            font-size: 14px;
        }

        .ant-table tr:hover td {
            background: #fafafa;
        }

        /* 用户头像 */
        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 12px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        /* 按钮样式 */
        .ant-btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            cursor: pointer;
            background: #fff;
            border: 1px solid #d9d9d9;
            box-shadow: 0 2px 0 rgba(0, 0, 0, 0.015);
            transition: all 0.3s;
            height: 32px;
            padding: 4px 15px;
            font-size: 14px;
            border-radius: 2px;
            color: rgba(0, 0, 0, 0.65);
            text-decoration: none;
            line-height: 1.5715;
        }

        .ant-btn:hover {
            color: #40a9ff;
            border-color: #40a9ff;
        }

        .ant-btn-danger {
            background: #fff;
            border-color: #ffccc7;
            color: #ff4d4f;
        }

        .ant-btn-danger:hover {
            background: #fff1f0;
            border-color: #ffccc7;
            color: #ff4d4f;
        }

        /* 响应式 */
        @media (max-width: 768px) {
            .ant-layout-sider {
                width: 0;
                overflow: hidden;
            }

            .ant-layout {
                margin-left: 0;
            }

            .ant-table {
                font-size: 12px;
            }

            .ant-table th,
            .ant-table td {
                padding: 12px 8px;
            }
        }
    </style>
</head>
<body>
    <!-- 侧边栏 -->
    <div class="ant-layout-sider">
        <div class="ant-layout-sider-children">
            <div class="logo">
                <i class="fas fa-shield-alt"></i>
                <span>后台管理</span>
            </div>

            <div class="ant-menu">
                <a href="index.php" style="text-decoration: none; color: inherit;">
                    <div class="ant-menu-item">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>控制台</span>
                    </div>
                </a>
                <div class="ant-menu-item ant-menu-item-selected">
                    <i class="fas fa-users"></i>
                    <span>用户管理</span>
                </div>
                <a href="files.php" style="text-decoration: none; color: inherit;">
                    <div class="ant-menu-item">
                        <i class="fas fa-folder"></i>
                        <span>文件管理</span>
                    </div>
                </a>
                <a href="settings.php" style="text-decoration: none; color: inherit;">
                    <div class="ant-menu-item">
                        <i class="fas fa-cog"></i>
                        <span>系统设置</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- 主内容区域 -->
    <div class="ant-layout">
        <!-- 顶部导航栏 -->
        <div class="ant-layout-header">
            <div>
                <div class="breadcrumb">首页 / 用户管理</div>
            </div>
            <div class="ant-btn" style="border: none; background: transparent;">
                <a href="?logout=1" style="text-decoration: none; color: rgba(0, 0, 0, 0.65);">
                    <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>
                    退出登录
                </a>
            </div>
        </div>

        <!-- 内容区域 -->
        <div class="ant-layout-content">
            <div class="page-header">
                <h1>用户管理</h1>
                <p>管理系统用户</p>
            </div>

            <!-- 用户表格 -->
            <div class="ant-table-container">
                <div class="ant-table-wrapper">
                    <div class="ant-table-title">
                        <span>用户列表</span>
                        <span style="color: rgba(0, 0, 0, 0.45); font-weight: normal;">共 <?php echo count($users); ?> 个用户</span>
                    </div>
                    <table class="ant-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>用户名</th>
                                <th>注册时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                        </div>
                                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo $user['created_at']; ?></td>
                                <td>
                                    <?php if ($user['username'] !== 'admin'): ?>
                                    <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirm('确定要删除用户 <?php echo htmlspecialchars($user['username']); ?> 吗？此操作将同时删除该用户的所有文件。')"
                                        class="ant-btn ant-btn-danger">
                                        <i class="fas fa-trash"></i> 删除
                                    </a>
                                    <?php else: ?>
                                    <span style="color: rgba(0, 0, 0, 0.25);">管理员</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>