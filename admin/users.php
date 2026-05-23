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
    <script src="../file/3.4.16"></script>
    <link href="../file/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">
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
                        <a href="index.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fa fa-dashboard mr-3"></i>
                            <span>控制台</span>
                        </a>
                    </li>
                    <li>
                        <a href="users.php" class="flex items-center px-4 py-3 bg-primary/10 text-primary rounded-lg">
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
                        <h1 class="text-xl font-bold text-gray-800">用户管理</h1>
                        <p class="text-sm text-gray-500">管理系统用户</p>
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
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">用户列表</h3>
                        <span class="text-sm text-gray-500">共 <?php echo count($users); ?> 个用户</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">用户名</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">注册时间</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-900"><?php echo $user['id']; ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-r from-primary to-accent rounded-full flex items-center justify-center text-white mr-3">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                            <span class="text-gray-900"><?php echo htmlspecialchars($user['username']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600"><?php echo $user['created_at']; ?></td>
                                    <td class="px-6 py-4">
                                        <?php if ($user['username'] !== 'admin'): ?>
                                        <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirm('确定要删除用户 <?php echo htmlspecialchars($user['username']); ?> 吗？此操作将同时删除该用户的所有文件。')"
                                            class="text-red-500 hover:text-red-600 transition-colors">
                                            <i class="fa fa-trash"></i> 删除
                                        </a>
                                        <?php else: ?>
                                        <span class="text-gray-400">管理员</span>
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
    </div>
</body>
</html>