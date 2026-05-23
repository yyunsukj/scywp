<!DOCTYPE html>

<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人云盘系统 - 登录</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet"> -->
    <script src="file/3.4.16"></script>
    <link href="file/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="file/chart.umd.min.js"></script>
    
    <!-- Tailwind 配置 -->
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
                    fontFamily: {
                        inter: ['Inter', 'system-ui', 'sans-serif'],
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
            .shadow-soft {
                box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            }
            .bg-glass {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center font-inter">
    <div class="w-full max-w-md px-6 py-8 bg-white rounded-2xl shadow-xl transform transition-all duration-300 hover:shadow-2xl">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-4">
                <img src="logo.png" alt="">
            </div>
            <h1 class="text-[clamp(1.5rem,3vw,2rem)] font-bold text-gray-800">个人云盘系统</h1>
            <p class="text-gray-500 mt-2">请登录以访问您的文件</p>
        </div>
        
        <?php if (isset($loginError)): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <i class="fa fa-exclamation-circle mr-2"></i>
                    <span><?php echo $loginError; ?></span>
                </div>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php" class="space-y-5">
            <div class="space-y-2">
                <label for="username" class="block text-sm font-medium text-gray-700">用户名</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-user text-gray-400"></i>
                    </div>
                    <input type="text" id="username" name="username" required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all duration-200"
                        placeholder="请输入用户名">
                </div>
            </div>
            
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">密码</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all duration-200"
                        placeholder="请输入密码">
                </div>
            </div>
            
            <button type="submit" name="login"
                class="w-full bg-primary hover:bg-primary/90 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 transform hover:-translate-y-1 hover:shadow-lg flex items-center justify-center">
                <i class="fa fa-sign-in mr-2"></i>
                登录
            </button>
        </form>
        
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>© <?php echo date('Y'); ?> 个人云盘系统</p>
        </div>
    </div>
    
    <script>
        // 添加登录表单动画
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.parentElement.classList.add('scale-105');
                    this.parentElement.parentElement.style.transition = 'all 0.2s ease';
                });
                input.addEventListener('blur', function() {
                    this.parentElement.parentElement.classList.remove('scale-105');
                });
            });
        });
    </script>
</body>
</html>    