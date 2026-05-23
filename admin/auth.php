<?php
session_start();
require_once '../config.php';

// 处理管理员登录
if (isset($_POST['admin_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 简单的管理员认证（可以修改为从数据库读取）
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $loginError = '管理员账号或密码错误';
    }
}

// 如果已经登录，直接跳转到首页
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - 登录</title>
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
    <style type="text/tailwindcss">
        @layer utilities {
            .content-auto {
                content-visibility: auto;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md mx-4">
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-primary to-accent rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fa fa-shield text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">后台管理系统</h1>
                <p class="text-gray-300">请使用管理员账号登录</p>
            </div>

            <?php if (isset($loginError)): ?>
                <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fa fa-exclamation-circle mr-2"></i>
                        <span><?php echo $loginError; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-gray-300">管理员账号</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="username" name="username" required
                            class="block w-full pl-10 pr-3 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all duration-200"
                            placeholder="请输入管理员账号">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-300">密码</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="block w-full pl-10 pr-3 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all duration-200"
                            placeholder="请输入密码">
                    </div>
                </div>

                <button type="submit" name="admin_login"
                    class="w-full bg-gradient-to-r from-primary to-accent hover:from-primary/90 hover:to-accent/90 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 transform hover:-translate-y-1 hover:shadow-lg flex items-center justify-center">
                    <i class="fa fa-sign-in mr-2"></i>
                    登录后台
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="../index.php" class="text-gray-400 hover:text-white transition-colors text-sm">
                    <i class="fa fa-arrow-left mr-1"></i>返回前台
                </a>
            </div>
        </div>

        <div class="mt-6 text-center text-sm text-gray-400">
            <p>© <?php echo date('Y'); ?> 个人云盘系统 - 后台管理</p>
        </div>
    </div>
</body>
</html>