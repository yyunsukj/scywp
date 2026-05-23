<?php
session_start();
require_once '../config.php';

// 检查管理员登录状态
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 处理登出
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// 获取统计数据
$conn = getDBConnection();

// 用户数量
$userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];

// 文件数量
$fileCount = $conn->query("SELECT COUNT(*) as count FROM files")->fetch_assoc()['count'];

// 总存储使用
$totalStorage = $conn->query("SELECT SUM(file_size) as total FROM files")->fetch_assoc()['total'];
$totalStorageGB = number_format($totalStorage / (1024 * 1024 * 1024), 2);

// 最近上传的文件
$recentFiles = $conn->query("SELECT * FROM files ORDER BY upload_time DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理系统 - 首页</title>
    <script src="../file/3.4.16"></script>
    <link href="../file/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="../file/chart.umd.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#165DFF',
                        secondary: '#36CFC9',
                        accent: '#722ED1',
                        neutral: '#F5F7FA',
                        'neutral-dark': '#4E5969',
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-neutral min-h-screen">
    <div class="flex">
        <!-- 侧边栏 -->
        <div class="w-64 bg-white shadow-lg min-h-screen fixed">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-r from-primary to-accent rounded-lg flex items-center justify-center mr-3">
                        <i class="fa fa-shield text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">后台管理</h2>
                        <p class="text-xs text-gray-500">系统控制台</p>
                    </div>
                </div>
            </div>

            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="index.php" class="flex items-center px-4 py-3 bg-primary/10 text-primary rounded-lg">
                            <i class="fa fa-dashboard mr-3"></i>
                            <span>控制台</span>
                        </a>
                    </li>
                    <li>
                        <a href="users.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fa fa-users mr-3"></i>
                            <span>用户管理</span>
                        </a>
                    </li>
                    <li>
                        <a href="files.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fa fa-folder mr-3"></i>
                            <span>文件管理</span>
                        </a>
                    </li>
                    <li>
                        <a href="settings.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fa fa-cog mr-3"></i>
                            <span>系统设置</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-100">
                <a href="../index.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fa fa-arrow-left mr-3"></i>
                    <span>返回前台</span>
                </a>
            </div>
        </div>

        <!-- 主内容区 -->
        <div class="flex-1 ml-64">
            <!-- 顶部导航栏 -->
            <div class="bg-white shadow-sm border-b border-gray-100">
                <div class="flex items-center justify-between px-8 py-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">控制台</h1>
                        <p class="text-sm text-gray-500">欢迎回来，管理员</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="?logout=1" class="text-red-500 hover:text-red-600 transition-colors">
                            <i class="fa fa-sign-out mr-1"></i>退出登录
                        </a>
                    </div>
                </div>
            </div>

            <!-- 内容区域 -->
            <div class="p-8">
                <!-- 统计卡片 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-primary">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">总用户数</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $userCount; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fa fa-users text-primary text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-secondary">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">总文件数</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $fileCount; ?></p>
                            </div>
                            <div class="w-12 h-12 bg-secondary/10 rounded-lg flex items-center justify-center">
                                <i class="fa fa-file text-secondary text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-accent">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">存储使用</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $totalStorageGB; ?> GB</p>
                            </div>
                            <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fa fa-database text-accent text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 最近文件 -->
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">最近上传的文件</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">文件名</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">大小</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">上传时间</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">用户</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($recentFiles as $file): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <i class="fa fa-file text-gray-400 mr-3"></i>
                                            <span class="text-gray-900"><?php echo htmlspecialchars($file['file_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600"><?php echo formatFileSize($file['file_size']); ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?php echo $file['upload_time']; ?></td>
                                    <td class="px-6 py-4 text-gray-600">用户ID: <?php echo $file['user_id']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>