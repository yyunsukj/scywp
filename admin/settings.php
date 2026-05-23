<?php
session_start();
require_once '../config.php';

// 检查管理员登录状态
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: auth.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - 系统设置</title>
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
                        <a href="settings.php" class="flex items-center px-4 py-3 bg-primary/10 text-primary rounded-lg">
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
                        <h1 class="text-xl font-bold text-gray-800">系统设置</h1>
                        <p class="text-sm text-gray-500">配置系统参数</p>
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
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">系统信息</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">系统版本</label>
                                <p class="text-gray-900">个人云盘系统 v1.0.0</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">PHP版本</label>
                                <p class="text-gray-900"><?php echo PHP_VERSION; ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">MySQL版本</label>
                                <p class="text-gray-900"><?php echo mysqli_get_server_info(getDBConnection()); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">服务器时间</label>
                                <p class="text-gray-900"><?php echo date('Y-m-d H:i:s'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm mt-6">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">管理员账号</h3>
                    </div>
                    <div class="p-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fa fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-yellow-800">默认管理员账号</h4>
                                    <p class="text-yellow-700 mt-1">账号：admin / 密码：admin123</p>
                                    <p class="text-yellow-700 mt-1">建议在部署后立即修改默认密码。</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>