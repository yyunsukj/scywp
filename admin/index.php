<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: auth.php');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: auth.php');
    exit;
}

$conn = getDBConnection();

$userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$fileCount = $conn->query("SELECT COUNT(*) as count FROM files")->fetch_assoc()['count'];
$totalStorage = $conn->query("SELECT SUM(file_size) as total FROM files")->fetch_assoc()['total'];
$totalStorageGB = number_format($totalStorage / (1024 * 1024 * 1024), 2);
$recentFiles = $conn->query("SELECT * FROM files ORDER BY upload_time DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - 控制台</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #06d6a0;
            --warning-color: #ffd166;
            --danger-color: #ef476f;
            --dark-color: #2b2d42;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e1e2f 0%, #252540 100%);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: 700;
        }

        .sidebar-brand i {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 24px;
        }

        .sidebar-nav {
            padding: 24px 16px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu-item {
            margin-bottom: 8px;
        }

        .sidebar-menu-link {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .sidebar-menu-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar-menu-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .sidebar-menu-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 18px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-bar {
            background: white;
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .breadcrumb-custom {
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }

        .breadcrumb-custom a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb-custom a:hover {
            color: var(--secondary-color);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-menu a {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .user-menu a:hover {
            color: var(--primary-color);
        }

        .content-area {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .page-header p {
            color: #64748b;
            font-size: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .stat-card.blue {
            border-left: 4px solid var(--primary-color);
        }

        .stat-card.green {
            border-left: 4px solid var(--success-color);
        }

        .stat-card.purple {
            border-left: 4px solid #9d4edd;
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.blue {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }

        .stat-icon.green {
            background: rgba(6, 214, 160, 0.1);
            color: var(--success-color);
        }

        .stat-icon.purple {
            background: rgba(157, 78, 221, 0.1);
            color: #9d4edd;
        }

        .stat-card-body h3 {
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stat-card-body .stat-value {
            color: var(--dark-color);
            font-size: 36px;
            font-weight: 700;
            line-height: 1;
        }

        .content-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-header-custom {
            padding: 24px 28px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header-custom h3 {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .card-header-custom span {
            color: #64748b;
            font-size: 14px;
            font-weight: 400;
        }

        .table-responsive-custom {
            padding: 0;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background: #f8f9fa;
            color: #475569;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 24px;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-custom tbody td {
            padding: 16px 24px;
            color: #475569;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tbody tr {
            transition: background-color 0.2s;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }

        .file-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            margin-right: 12px;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- 侧边栏 -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-brand">
                <i class="fas fa-shield-alt"></i>
                <span>后台管理</span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="index.php" class="sidebar-menu-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>控制台</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="users.php" class="sidebar-menu-link">
                        <i class="fas fa-users"></i>
                        <span>用户管理</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="files.php" class="sidebar-menu-link">
                        <i class="fas fa-folder"></i>
                        <span>文件管理</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="settings.php" class="sidebar-menu-link">
                        <i class="fas fa-cog"></i>
                        <span>系统设置</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- 主内容区域 -->
    <div class="main-content">
        <!-- 顶部导航栏 -->
        <div class="top-bar">
            <div class="breadcrumb-custom">
                <a href="index.php">首页</a>
                <span class="mx-2">/</span>
                <span>控制台</span>
            </div>
            <div class="user-menu">
                <a href="?logout=1">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    退出登录
                </a>
            </div>
        </div>

        <!-- 内容区域 -->
        <div class="content-area">
            <div class="page-header">
                <h1>控制台</h1>
                <p>欢迎回来，管理员</p>
            </div>

            <!-- 统计卡片 -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-card-header">
                        <div class="stat-icon blue">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-card-body">
                        <h3>总用户数</h3>
                        <div class="stat-value"><?php echo $userCount; ?></div>
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-card-header">
                        <div class="stat-icon green">
                            <i class="fas fa-file"></i>
                        </div>
                    </div>
                    <div class="stat-card-body">
                        <h3>总文件数</h3>
                        <div class="stat-value"><?php echo $fileCount; ?></div>
                    </div>
                </div>

                <div class="stat-card purple">
                    <div class="stat-card-header">
                        <div class="stat-icon purple">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                    <div class="stat-card-body">
                        <h3>存储使用</h3>
                        <div class="stat-value"><?php echo $totalStorageGB; ?> GB</div>
                    </div>
                </div>
            </div>

            <!-- 最近文件表格 -->
            <div class="content-card">
                <div class="card-header-custom">
                    <h3>最近上传的文件</h3>
                    <span>共 <?php echo count($recentFiles); ?> 个文件</span>
                </div>
                <div class="table-responsive-custom">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>文件名</th>
                                <th>大小</th>
                                <th>上传时间</th>
                                <th>用户</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentFiles as $file): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon">
                                            <i class="fas fa-file"></i>
                                        </div>
                                        <span><?php echo htmlspecialchars($file['file_name']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo formatFileSize($file['file_size']); ?></td>
                                <td><?php echo $file['upload_time']; ?></td>
                                <td>用户ID: <?php echo $file['user_id']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>