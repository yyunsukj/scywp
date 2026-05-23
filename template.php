<?php

//  * @LastEditors: Swk(葫芦侠:陌南尘。)
//  * @hitokoto: 先谋杀全世界的噪音 再审判心跳的供词.

// 确保配置和函数已加载
require_once __DIR__ . '/config.php';

if (isset($_SESSION['successMessage'])) {
    $successMessage = $_SESSION['successMessage'];
    unset($_SESSION['successMessage']);
}
if (isset($_SESSION['errorMessage'])) {
    $errorMessage = $_SESSION['errorMessage'];
    unset($_SESSION['errorMessage']);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人云盘系统</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="gile/chart.umd.min.js"></script> -->
        <script src="file/3.4.16"></script>
    <link href="file/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="file/chart.umd.min.js"></script>
    <link rel="icon" type="image/x-icon" href="logo.png" />
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
<style>
    /* 统一按钮样式 */
   .batch-action-btn {
        padding: 0.75rem 1rem; /* 统一内边距 */
        font-size: 0.875rem; /* 统一字体大小 */
        border-radius: 0.375rem; /* 统一圆角 */
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 100px; /* 最小宽度 */
        margin-right: 10px;

    }

    /* 不同按钮颜色区分 */
   .copy-link-btn {
        background-color: #165DFF; /* 蓝色 */
        color: white;
    }

   .delete-selected-btn {
        background-color: #FF4747; /* 红色 */
        color: white;
    }

   .move-selected-btn {
        background-color: #36CFC9; /* 绿色 */
        color: white;
    }

   .download-selected-btn {
        background-color: #722ED1; /* 紫色 */
        color: white;
    }

/* 手机端布局调整 */
@media (max-width: 768px) {
    .batch-action-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem; /* 按钮之间的间距 */
    }

    .batch-action-container button {
        width: calc(50% - 0.25rem); /* 每个按钮占一半宽度，减去间距 */
    }
    .jzs{
        display: flex;
        justify-content: center; /* 水平居中 */
        align-items: center; /* 垂直居中 */
    }

    /* 统一按钮样式 */
   .batch-action-btn {
        width: 46%;
        margin-left: 5px;
        margin-right: 5px;
        margin-bottom: 5px;

    }

}
</style>
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
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        }
    </style>
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
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* 新增模态框样式 */
        #actionModalContent {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        #actionModalContent h3 {
            font-size: 1.25rem;
            line-height: 1.75rem;
            font-weight: 600;
            color: #1f2937;
        }
        #actionModalContent label {
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            color: #374151;
        }
        #actionModalContent input[type="text"] {
            border-color: #d1d5db;
            transition: border-color 0.2s ease-in-out;
        }
        #actionModalContent input[type="text"]:focus {
            border-color: #165DFF;
            outline: none;
            box-shadow: 0 0 0 3px rgba(22, 93, 255, 0.1);
        }
        #actionModalContent button[type="submit"] {
            background-color: #165DFF;
            color: #fff;
            transition: background-color 0.2s ease-in-out;
        }
        #actionModalContent button[type="submit"]:hover {
            background-color: rgba(22, 93, 255, 0.9);
        }
    }
</style>
<style>
    .paixu{
        width: 100%;
    }
</style>
</head>
<body class="bg-gray-50 font-inter text-gray-800 min-h-screen flex flex-col">
    <!-- 顶部导航栏 -->
    <header class="bg-white shadow-sm sticky top-0 z-30 transition-all duration-300">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- 左侧logo和标题 -->
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                            <img src="logo.png" alt="">
                        </div>
                        <h1 class="ml-3 text-xl font-bold text-gray-900">个人云盘系统</h1>
                    </div>
                    
                    <!-- 路径导航 -->
                    <div class="hidden md:flex ml-10 space-x-4">
                        <a href="index.php" class="text-gray-500 hover:text-primary transition-colors">
                            <i class="fa fa-home mr-1"></i>根目录
                        </a>
                        <?php if (!empty($relativePath)): ?>
                            <?php
                                $pathParts = explode('/', $relativePath);
                                $currentPath = '';
                            ?>
                            <?php foreach ($pathParts as $part): ?>
                                <?php
                                    $currentPath .= '/' . $part;
                                    $encodedPath = urlencode(ltrim($currentPath, '/'));
                                ?>
                                <span class="text-gray-400">/</span>
                                <a href="index.php?path=<?php echo $encodedPath; ?>" class="text-gray-500 hover:text-primary transition-colors">
                                    <?php echo htmlspecialchars($part); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- 右侧操作区 -->
                <div class="flex items-center space-x-4">
                    <!-- 搜索框 -->
                    <div class="relative hidden md:block">
                        <form method="GET">
                            <input type="hidden" name="path" value="<?php echo urlencode($relativePath); ?>">
                            <input type="text" name="search" placeholder="搜索文件..."
                                class="w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all duration-200"
                                value="<?php echo htmlspecialchars($searchTerm); ?>">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fa fa-search"></i>
                            </div>
                        </form>
                    </div>
                    
                    <!-- 修改后的按钮容器，确保在电脑端横排显示 -->
                    <div class="flex items-center space-x-2 md:flex md:space-x-2 md:block hidden">
                        <!-- 上传按钮 -->
                        <button id="uploadBtn" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center">
                            <i class="fa fa-upload mr-2"></i>
                            <span>上传文件</span>
                        </button>
                        <!-- 新建文件夹按钮 -->
                        <button id="newFolderBtn" class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary/90 transition-colors flex items-center">
                            <i class="fa fa-folder text-primary mr-3" style="color: #fff;"></i>
                            <span>新建文件夹</span>
                        </button>
                    </div>
                    
                    <!-- 登出按钮 -->
                    <a href="index.php?logout=1" class="ml-2 p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-500 hover:text-gray-700">
                        <i class="fa fa-sign-out"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- 主内容区 -->
    <main class="flex-grow container mx-auto px-4 py-6">
    <!-- 新增按钮容器，手机端显示 -->
    <div class="flex flex-wrap -mx-2 mb-4 md:hidden">
        <div class="w-1/2 px-2 mb-4 sm:mb-0">
            <button id="uploadBtns" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center">
                <i class="fa fa-upload mr-2"></i>
                <span>上传文件</span>
            </button>
        </div>
        <div class="w-1/2 px-2">
            <button id="newFolderBtns" class="w-full px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary/90 transition-colors flex items-center justify-center">
                <i class="fa fa-folder text-primary mr-3" style="color: #fff;"></i>
                <span>新建文件夹</span>
            </button>
        </div>
    </div>
<!-- 通知消息 -->



<?php if (isset($successMessage)): ?>
    <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6">
        <div class="flex items-center">
            <i class="fa fa-check-circle mr-2"></i>
            <span><?php echo $successMessage; ?></span>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($errorMessage)): ?>
    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
        <div class="flex items-center">
            <i class="fa fa-exclamation-circle mr-2"></i>
            <span><?php echo $errorMessage; ?></span>
        </div>
    </div>
<?php endif; ?>



        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- 左侧边栏 -->
            <aside class="lg:col-span-3 space-y-6">
                <!-- 存储信息卡片 -->
                <div class="bg-white rounded-xl shadow-soft p-6 transition-all duration-300 hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">存储空间</h2>
                        <span class="text-sm text-gray-500"><?php echo formatSize($usedSpace); ?> / <?php echo formatSize($totalSpace); ?></span>
                    </div>
                    
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                        <div class="bg-primary h-2.5 rounded-full" style="width: <?php echo $spaceUsagePercent; ?>%"></div>
                    </div>
                    
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>已使用: <?php echo round($spaceUsagePercent, 1); ?>%</span>
                        <span>剩余: <?php echo round(100 - $spaceUsagePercent, 1); ?>%</span>
                    </div>
                    
                    <!-- 存储使用图表 -->
                    <div class="mt-6">
                        <canvas id="storageChart" height="150"></canvas>
                    </div>
                </div>
                
                <!-- 文件夹列表 -->
                <div class="bg-white rounded-xl shadow-soft p-6 transition-all duration-300 hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">文件夹</h2>
                        <span class="text-sm text-gray-500"><?php echo count($folders); ?> 个</span>
                    </div>
                    
                    <div class="space-y-2 max-h-80 overflow-y-auto scrollbar-hide">
                        <a href="index.php" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors <?php echo empty($relativePath) ? 'bg-primary/10 text-primary' : ''; ?>">
                            <i class="fa fa-folder text-primary mr-3"></i>
                            <span>根目录</span>
                        </a>
                        
                        <?php foreach ($folders as $folder): ?>
                            <a href="index.php?path=<?php echo urlencode($folder['path']); ?>" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors <?php echo ($relativePath === $folder['path']) ? 'bg-primary/10 text-primary' : ''; ?>">
                                <i class="fa fa-folder text-primary mr-3"></i>
                                <span><?php echo htmlspecialchars($folder['name']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
            
            <!-- 右侧内容区 -->
            <div class="lg:col-span-9">
                <!-- 移动端路径导航 -->
                <div class="md:hidden mb-4">
                    <div class="bg-white rounded-lg shadow-soft p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <a href="index.php" class="text-gray-500 hover:text-primary transition-colors">
                                    <i class="fa fa-home mr-1"></i>根目录
                                </a>
                                <?php if (!empty($relativePath)): ?>
                                    <span class="mx-2 text-gray-400">/</span>
                                    <span class="text-gray-700 font-medium"><?php echo htmlspecialchars(basename($relativePath)); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button id="mobileSearchBtn" class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-gray-500">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 移动端搜索框 -->
                <div id="mobileSearch" class="md:hidden mb-4 hidden">
                    <div class="bg-white rounded-lg shadow-soft p-4">
                        <form method="GET">
                            <input type="hidden" name="path" value="<?php echo urlencode($relativePath); ?>">
                            <div class="relative">
                                <input type="text" name="search" placeholder="搜索文件..."
                                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all duration-200"
                                    value="<?php echo htmlspecialchars($searchTerm); ?>">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                    <i class="fa fa-search"></i>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- 文件列表控制区 -->
                <div class="bg-white rounded-lg shadow-soft p-4 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                        <!-- 左侧文件统计 -->
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">文件列表</h2>
                            <p class="text-sm text-gray-500">
                                共 <span class="font-medium"><?php echo $totalFiles; ?></span> 个文件
                                <?php if (!empty($searchTerm)): ?>
                                    ，搜索结果
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <!-- 右侧排序和分页控制 -->
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                            <!-- 排序选择 -->
                            <div class="relative">
                                <select id="sortSelect" class="appearance-none bg-gray-50 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded-lg leading-tight focus:outline-none focus:bg-white focus:border-primary transition-all duration-200 paixu">
                                    <option value="name-asc" <?php echo ($sortBy === 'name' && $sortOrder === 'asc') ? 'selected' : ''; ?>>按名称 (升序)</option>
                                    <option value="name-desc" <?php echo ($sortBy === 'name' && $sortOrder === 'desc') ? 'selected' : ''; ?>>按名称 (降序)</option>
                                    <option value="time-asc" <?php echo ($sortBy === 'time' && $sortOrder === 'asc') ? 'selected' : ''; ?>>按时间 (最早)</option>
                                    <option value="time-desc" <?php echo ($sortBy === 'time' && $sortOrder === 'desc') ? 'selected' : ''; ?>>按时间 (最新)</option>
                                    <option value="size-asc" <?php echo ($sortBy === 'size' && $sortOrder === 'asc') ? 'selected' : ''; ?>>按大小 (最小)</option>
                                    <option value="size-desc" <?php echo ($sortBy === 'size' && $sortOrder === 'desc') ? 'selected' : ''; ?>>按大小 (最大)</option>
                                    <option value="type-asc" <?php echo ($sortBy === 'type' && $sortOrder === 'asc') ? 'selected' : ''; ?>>按类型 (升序)</option>
                                    <option value="type-desc" <?php echo ($sortBy === 'type' && $sortOrder === 'desc') ? 'selected' : ''; ?>>按类型 (降序)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fa fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            
                            <!-- 分页控制 -->
                            <div class="flex items-center">
                                <span class="text-sm text-gray-500 mr-2">第 <?php echo $currentPage; ?> 页，共 <?php echo $totalPages; ?> 页</span>
                                <div class="flex space-x-1">
                                    <a href="index.php?path=<?php echo urlencode($relativePath); ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>&page=<?php echo max(1, $currentPage - 1); ?>"
                                        class="px-3 py-1 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors <?php echo ($currentPage <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                        <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>>
                                        <i class="fa fa-chevron-left text-xs"></i>
                                    </a>
                                    <a href="index.php?path=<?php echo urlencode($relativePath); ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>&page=<?php echo min($totalPages, $currentPage + 1); ?>"
                                        class="px-3 py-1 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors <?php echo ($currentPage >= $totalPages) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                        <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>>
                                        <i class="fa fa-chevron-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 文件列表 -->
                <div class="bg-white rounded-lg shadow-soft overflow-hidden">
                    <!-- 列表头部 -->
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">

                        
                        <!-- 修改按钮样式，添加手机端适配 -->
                        <div class="flex items-center flex-wrap md:flex-nowrap batch-action-containe jzs">
                            <button id="copySelectedBtn" class="batch-action-btn copy-link-btn opacity-50 cursor-not-allowed" disabled>
                                <i class="fa fa-link mr-1"></i>复制链接
                            </button>
                            <!-- 修改批量删除按钮 -->
                            <button id="deleteSelectedBtn" class="batch-action-btn delete-selected-btn" disabled>
                                <i class="fa fa-trash mr-1"></i> 删除选中
                            </button>

                            <!-- 添加隐藏表单 -->
                            <form id="batchDeleteForm" method="POST">
                                <input type="hidden" name="delete_selected_files" value="1">
                                <div id="selectedFilesContainer"></div>
                            </form>
                            <button id="moveSelectedBtn" class="batch-action-btn move-selected-btn opacity-50 cursor-not-allowed" disabled>
                                <i class="fa fa-arrows mr-1"></i>批量移动
                            </button>
                            <button id="downloadSelectedBtn" class="batch-action-btn download-selected-btn opacity-50 cursor-not-allowed" disabled>
                                <i class="fa fa-download mr-1"></i>批量下载
                            </button>
                        </div>
                    </div>




                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center">
                          
                                <input type="checkbox" id="masterCheckbox" class="sr-only peer">
       
                          
                            <button id="selectAllBtn" class="text-sm text-gray-500 hover:text-primary transition-colors">
                                <i class="fa fa-square-o mr-1"></i>全选
                            </button>
                        </div>
       </div>















                    
                    <!-- 文件列表内容 -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        名称
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        大小
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        类型
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        上传时间
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        操作
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <!-- 文件夹行 -->
                                <?php foreach ($folders as $folder): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center bg-blue-100 rounded">
                                                    <i class="fa fa-folder text-blue-500"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <a href="index.php?path=<?php echo urlencode($folder['path']); ?>">
                                                            <?php echo htmlspecialchars($folder['name']); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">-</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">文件夹</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('Y-m-d H:i:s', $folder['time']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button data-action="rename-folder" data-name="<?php echo htmlspecialchars($folder['name']); ?>" data-path="<?php echo htmlspecialchars($folder['path']); ?>"
                                                class="text-gray-500 hover:text-primary transition-colors mr-3">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button data-action="delete-folder" data-name="<?php echo htmlspecialchars($folder['name']); ?>" data-path="<?php echo htmlspecialchars($folder['path']); ?>"
                                                class="text-gray-500 hover:text-red-500 transition-colors">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <!-- 文件行 -->
                                   <!-- 在 template.php 文件中，找到文件行部分 -->
                                    <?php foreach ($paginatedFiles as $file): ?>
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
<div class="flex items-center">
                                                     <div class="relative mr-4">
                                                         <input type="checkbox" class="item-checkbox sr-only peer" data-id="<?php echo $file['id']; ?>">
                                                         <div class="w-5 h-5 border border-gray-300 rounded peer-checked:bg-primary peer-checked:border-primary flex items-center justify-center cursor-pointer transition-colors">
                                                             <i class="fa fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                                         </div>
                                                     </div>
                                                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center bg-gray-100 rounded">
                                                        <?php
                                                            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                                            $fileIcon = 'file-o';

                                                            if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                                                $fileIcon = 'file-image-o';
                                                            } elseif (in_array($fileExt, ['pdf'])) {
                                                                $fileIcon = 'file-pdf-o';
                                                            } elseif (in_array($fileExt, ['doc', 'docx'])) {
                                                                $fileIcon = 'file-word-o';
                                                            } elseif (in_array($fileExt, ['xls', 'xlsx'])) {
                                                                $fileIcon = 'file-excel-o';
                                                            } elseif (in_array($fileExt, ['ppt', 'pptx'])) {
                                                                $fileIcon = 'file-powerpoint-o';
                                                            } elseif (in_array($fileExt, ['mp3', 'wav', 'ogg'])) {
                                                                $fileIcon = 'file-audio-o';
                                                            } elseif (in_array($fileExt, ['mp4', 'mov', 'avi'])) {
                                                                $fileIcon = 'file-video-o';
                                                            } elseif (in_array($fileExt, ['zip', 'rar', '7z'])) {
                                                                $fileIcon = 'file-archive-o';
                                                            }
                                                        ?>
<i class="fa fa-<?php echo $fileIcon; ?> text-gray-500"></i>
                                                     </div>
                                                     <div class="ml-3">
                                                         <?php
                                                             $supportedImageExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                                                             $supportedVideoExts = ['mp4', 'webm', 'ogg'];
                                                             $supportedDocExts = ['pdf'];
$isSupportedForOnlineView = false; // 暂时禁用在线预览以避免Referer策略问题
                                                              $fileId = $file['id'];
                                                              $url = 'download.php?file=' . $fileId;
                                                          ?>
                                                         <a href="<?php echo $url; ?>" target="_blank" class="text-gray-900 hover:underline" onclick="event.stopPropagation();">
                                                             <?php echo htmlspecialchars($file['name']); ?>
                                                         </a>
                                                     </div>
                                                 </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500"><?php echo formatSize($file['size']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($file['type']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo date('Y-m-d H:i:s', $file['time']); ?>
                                            </td>
<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                 <button data-action="copy-link" data-name="<?php echo htmlspecialchars($file['name']); ?>" data-id="<?php echo $file['id']; ?>"
                                                     class="text-gray-500 hover:text-primary transition-colors mr-3">
                                                     <i class="fa fa-link"></i>
                                                 </button>
                                                 <button data-action="move-file" data-name="<?php echo htmlspecialchars($file['name']); ?>" data-id="<?php echo $file['id']; ?>"
                                                     class="text-gray-500 hover:text-primary transition-colors mr-3">
                                                     <i class="fa fa-arrows"></i>
                                                 </button>
                                                 <button data-action="download-file" data-name="<?php echo htmlspecialchars($file['name']); ?>" data-id="<?php echo $file['id']; ?>"
                                                     class="text-gray-500 hover:text-primary transition-colors mr-3">
                                                     <i class="fa fa-download"></i>
                                                 </button>
                                                  <button data-action="rename-file" data-name="<?php echo htmlspecialchars($file['name']); ?>" data-id="<?php echo $file['id']; ?>"
                                                     class="text-gray-500 hover:text-primary transition-colors mr-3">
                                                     <i class="fa fa-pencil"></i>
                                                 </button>
                                                 <button data-action="delete-file" data-name="<?php echo htmlspecialchars($file['name']); ?>" data-id="<?php echo $file['id']; ?>"
                                                     class="text-gray-500 hover:text-red-500 transition-colors">
                                                     <i class="fa fa-trash"></i>
                                                 </button>
                                             </td>
                                        </tr>
                                    <?php endforeach; ?>
                                
<?php if (empty($folders) && empty($paginatedFiles)): ?>
                                     <tr>
                                         <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                             <div class="mb-4 text-4xl text-gray-300">
                                                 <i class="fa fa-folder-open-o"></i>
                                             </div>
                                             <p class="text-lg">此文件夹为空</p>
                                             <p class="text-sm mt-2">上传文件或创建新文件夹开始使用</p>
                                         </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- 分页控制（移动端） -->
                    <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between">
                        <div class="mb-4 sm:mb-0">
                            <p class="text-sm text-gray-500">
                                显示第 <?php echo min(1, $totalFiles); ?>-<?php echo min($itemsPerPage * $currentPage, $totalFiles); ?> 条，共 <?php echo $totalFiles; ?> 条
                            </p>
                        </div>
                        <div class="flex space-x-1">
                            <a href="index.php?path=<?php echo urlencode($relativePath); ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>&page=<?php echo max(1, $currentPage - 1); ?>"
                                class="px-3 py-1 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors <?php echo ($currentPage <= 1) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>>
                                <i class="fa fa-chevron-left text-xs"></i>
                            </a>
                            <a href="index.php?path=<?php echo urlencode($relativePath); ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>&page=<?php echo min($totalPages, $currentPage + 1); ?>"
                                class="px-3 py-1 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors <?php echo ($currentPage >= $totalPages) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>>
                                <i class="fa fa-chevron-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- 上传文件模态框 -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">上传文件到 <?php echo htmlspecialchars(basename($relativePath) ?: '根目录'); ?></h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                <div class="space-y-4">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-primary transition-colors">
                        <input type="file" name="files[]" id="fileInput" multiple class="hidden">
                        <label for="fileInput" class="cursor-pointer">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa fa-cloud-upload text-4xl text-gray-400 mb-4"></i>
                                <p class="text-sm text-gray-600 mb-2">点击或拖拽文件到此处上传</p>
                                <p class="text-xs text-gray-500">支持多文件上传</p>
                            </div>
                        </label>
                    </div>
                    <div id="uploadProgressContainer" class="hidden">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">上传进度</h4>
                        <div id="uploadProgress" class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                            <div id="uploadProgressBar" class="bg-primary h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span id="uploadProgressText">0%</span>
                            <span id="uploadProgressFiles">0/0</span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeUploadModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">取消</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">上传文件</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- 操作模态框 -->
    <div id="actionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="actionModalContent">
            <!-- 模态框内容将通过JavaScript动态填充 -->
        </div>
    </div>

    <script>
        // 模态框控制
        function openModal(content) {
            const modal = document.getElementById('actionModal');
            const modalContent = document.getElementById('actionModalContent');
            
            modalContent.innerHTML = content;
            modal.classList.remove('hidden');
            
            // 添加动画效果
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        
        function closeModal() {
            const modal = document.getElementById('actionModal');
            const modalContent = document.getElementById('actionModalContent');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        
        // 上传模态框控制
        document.getElementById('uploadBtn').addEventListener('click', function() {
            const modal = document.getElementById('uploadModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            
            // 添加动画效果
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        });

                // 手机端上传模态框控制
        document.getElementById('uploadBtns').addEventListener('click', function() {
            const modal = document.getElementById('uploadModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            
            // 添加动画效果
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        });
        
        
        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        




// 上传文件变化时显示进度条
    document.getElementById('fileInput').addEventListener('change', function() {
        const files = this.files;
        if (files.length > 0) {
            document.getElementById('uploadProgressContainer').classList.remove('hidden');
            document.getElementById('uploadProgressFiles').textContent = `共有文件${files.length}个`;
        } else {
            document.getElementById('uploadProgressContainer').classList.add('hidden');
        }
    });


        
    // 文件上传进度监听
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault(); // 阻止表单默认提交行为

    const currentPath = new URLSearchParams(window.location.search).get('path');
    const formData = new FormData(this);
    const xhr = new XMLHttpRequest();

    const progressBar = document.getElementById('uploadProgressBar');
    const progressText = document.getElementById('uploadProgressText');
    const progressFiles = document.getElementById('uploadProgressFiles');
    const files = document.getElementById('fileInput').files;

    let url = 'index.php';
    if (currentPath) {
        url += '?path=' + encodeURIComponent(currentPath);
    }

    xhr.upload.addEventListener('progress', function(event) {
        if (event.lengthComputable) {
            const percentComplete = (event.loaded / event.total) * 100;
            progressBar.style.width = `${percentComplete}%`;
            progressText.textContent = `${percentComplete.toFixed(0)}%`;
        }
    });

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                // 上传完成后自动关闭模态框
                setTimeout(closeUploadModal, 1000);
                // 可以在这里添加提示信息
                alert('文件上传成功');
                location.reload(true);
            } else {
                console.error('上传失败:', xhr.statusText);
                alert('文件上传失败，请重试');
            }
        }
    };

    xhr.open('POST', url, true);
    xhr.send(formData);
});


        function closeUploadModal() {
        const modal = document.getElementById('uploadModal');
        const modalContent = document.getElementById('modalContent');

        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }



        // 复制选中链接
copySelectedBtn.addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('请先选择要复制链接的文件');
        return;
    }

    let links = '';
    selectedCheckboxes.forEach(checkbox => {
        const relativePath = checkbox.getAttribute('data-path');
        // 生成完整的URL
        const protocol = window.location.protocol;
        const host = window.location.host;
        const currentPath = window.location.pathname;
        // 假设当前页面所在目录下有uploads文件夹，根据实际情况调整
        const baseUrl = protocol + '//' + host + currentPath.replace(/\/[^\/]+$/, '') + '/uploads/'; 
        const fileUrl = baseUrl + relativePath.replace(/^\//, '');

        links += fileUrl + '\n';
    });

    // 检查 navigator.clipboard 是否可用
    if (navigator.clipboard) {
        navigator.clipboard.writeText(links).then(function() {
            alert('已复制 ' + selectedCheckboxes.length + ' 个文件链接到剪贴板');
        }).catch(function(err) {
            console.error('复制失败: ', err);
            alert('复制失败，请手动复制');
        });
    } else {
        // 备用方案：创建临时文本框进行复制
        const textarea = document.createElement('textarea');
        textarea.value = links;
        document.body.appendChild(textarea);
        textarea.select();
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                alert('已复制 ' + selectedCheckboxes.length + ' 个文件链接到剪贴板');
            } else {
                alert('复制失败，请手动复制');
            }
        } catch (err) {
            console.error('复制失败: ', err);
            alert('复制失败，请手动复制');
        }
        document.body.removeChild(textarea);
    }
});












// 处理操作按钮点击事件
document.querySelectorAll('[data-action]').forEach(button => {
    button.addEventListener('click', function(e) {
        e.stopPropagation(); 
        const action = this.getAttribute('data-action');
        const name = this.getAttribute('data-name');
        const id = this.getAttribute('data-id');

        switch (action) {
            case 'rename-folder':
                openModal(`
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">重命名文件夹</h3>
                        <form method="POST" id="renameFolderForm">
                            <input type="hidden" name="old_name" value="${name}">
                            <div class="mb-4">
                                <label for="new_folder_name" class="block text-sm font-medium text-gray-700 mb-1">新文件夹名</label>
                                <input type="text" id="new_folder_name" name="new_name" value="${name}" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-focus" required>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="closeModal()">取消</button>
                                <button type="submit" name="rename_folder" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">确定</button>
                            </div>
                        </form>
                    </div>
                `);
                break;
            case 'delete-folder':
                openModal(`
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">删除文件夹</h3>
                        <p class="text-gray-600 mb-6">确定要删除文件夹 "${name}" 吗？此操作不可撤销，且会删除该文件夹下的所有文件和子文件夹。</p>
                        <form method="POST" id="deleteFolderForm">
                            <input type="hidden" name="folder_name" value="${name}">
                            <div class="flex justify-end space-x-3">
                                <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="closeModal()">取消</button>
                                <button type="submit" name="delete_folder" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition-colors">删除</button>
                            </div>
                        </form>
                    </div>
                `);
                break;
            case 'rename-file':
                openModal(`
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">重命名文件</h3>
                        <form method="POST" id="renameFileForm">
                            <input type="hidden" name="old_name" value="${name}">
                            <div class="mb-4">
                                <label for="new_file_name" class="block text-sm font-medium text-gray-700 mb-1">新文件名</label>
                                <input type="text" id="new_file_name" name="new_name" value="${name}" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-focus" required>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="closeModal()">取消</button>
                                <button type="submit" name="rename_file" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">确定</button>
                            </div>
                        </form>
                    </div>
                `);
                break;
case 'copy-link':
    const selectedCheckboxes = [this]; // 模拟单个文件的选中状态
    if (selectedCheckboxes.length === 0) {
        alert('请先选择要复制链接的文件');
        return;
    }

    let links = '';
    selectedCheckboxes.forEach(checkbox => {
        const relativePath = checkbox.getAttribute('data-path');
        // 生成完整的URL
        const protocol = window.location.protocol;
        const host = window.location.host;
        const currentPath = window.location.pathname;
        // 动态生成基础URL
        const baseUrl = protocol + '//' + host + currentPath.replace(/\/[^\/]+$/, '') + '/uploads/'; 
        const fileUrl = baseUrl + relativePath.replace(/^\//, '');

        links += fileUrl + '\n';
    });

    // 检查 navigator.clipboard 是否可用
    if (navigator.clipboard) {
        navigator.clipboard.writeText(links).then(function() {
            alert('文件链接已复制到剪贴板');
        }).catch(function(err) {
            console.error('复制失败: ', err);
            alert('复制失败，请手动复制');
        });
    } else {
        // 备用方案：创建临时文本框进行复制
        const textarea = document.createElement('textarea');
        textarea.value = links;
        document.body.appendChild(textarea);
        textarea.select();
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                alert('文件链接已复制到剪贴板');
            } else {
                alert('复制失败，请手动复制');
            }
        } catch (err) {
            console.error('复制失败: ', err);
            alert('复制失败，请手动复制');
        }
        document.body.removeChild(textarea);
    }
    break;
            case 'move-file':
                // 获取所有文件夹选项
                const folders = <?php echo json_encode($allFolders); ?>;
                let folderOptions = '';

                folders.forEach(folder => {
                    if (folder.relativePath !== '<?php echo $relativePath; ?>') {
                        folderOptions += `<option value="${folder.relativePath}">${folder.relativePath || '根目录'}</option>`;
                    }
                });

                if (!folderOptions) {
                    alert('没有可移动到的其他文件夹');
                    return;
                }

                openModal(`
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">移动文件</h3>
                        <form method="POST" id="moveFileForm">
                            <input type="hidden" name="file_name" value="${name}">
                            <div class="mb-4">
                                <label for="target_folder" class="block text-sm font-medium text-gray-700 mb-1">目标文件夹</label>
                                <select id="target_folder" name="target_folder" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-focus" required>
                                    ${folderOptions}
                                </select>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="closeModal()">取消</button>
                                <button type="submit" name="move_file" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">移动</button>
                            </div>
                        </form>
                    </div>
                `);
                break;
            case 'delete-file':
                openModal(`
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">删除文件</h3>
                        <p class="text-gray-600 mb-6">确定要删除文件 "${name}" 吗？此操作不可撤销。</p>
                        <form method="POST" id="deleteFileForm">
                            <input type="hidden" name="file_id" value="${id}">
                            <div class="flex justify-end space-x-3">
                                <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="closeModal()">取消</button>
                                <button type="submit" name="delete_file" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition-colors">删除</button>
                            </div>
                        </form>
                    </div>
                `);
                break;
        }
    });
});
        // 移动端搜索框切换
        document.getElementById('mobileSearchBtn').addEventListener('click', function() {
            const mobileSearch = document.getElementById('mobileSearch');
            mobileSearch.classList.toggle('hidden');
        });
        
        // 排序选择变化
        document.getElementById('sortSelect').addEventListener('change', function() {
            const value = this.value;
            const [sortBy, sortOrder] = value.split('-');
            
            // 构建URL参数
            let url = 'index.php?path=' + encodeURIComponent('<?php echo $relativePath; ?>');
            url += '&sort=' + sortBy + '&order=' + sortOrder;
            
            // 添加当前页码
            const currentPage = <?php echo $currentPage; ?>;
            if (currentPage > 1) {
                url += '&page=' + currentPage;
            }
            
            // 添加搜索词
            const searchTerm = '<?php echo $searchTerm; ?>';
            if (searchTerm) {
                url += '&search=' + encodeURIComponent(searchTerm);
            }
            
            // 跳转到新URL
            window.location.href = url;
        });
        
        // 初始化存储使用图表
        document.addEventListener('DOMContentLoaded', function() {








// 动态绑定项目复选框事件（处理动态加载的元素）
function bindItemCheckboxEvents() {
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    itemCheckboxes.forEach(checkbox => {
        // 跳过已绑定的元素
        if (checkbox.dataset.eventBound) return;
        checkbox.dataset.eventBound = 'true';

        // 确保复选框本身可以正常点击
        checkbox.addEventListener('change', function(e) {
            e.stopPropagation();
            console.log('复选框状态变化:', this.checked);
            updateSelectAllButton();
        });

        // 确保复选框的点击事件不被阻止
        checkbox.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('复选框被点击');
        });

        // 找到复选框所在的行
        const row = checkbox.closest('tr');
        if (row) {
            // 点击行时切换复选框状态
            row.addEventListener('click', function(e) {
                if (!e.target.closest('.item-checkbox')) {
                    checkbox.checked = !checkbox.checked;
                    const event = new Event('change', { bubbles: true });
                    checkbox.dispatchEvent(event);
                    console.log('通过点击行切换复选框状态');
                }
            });
        }
    });
}

// 初始绑定事件
bindItemCheckboxEvents();

// 处理动态加载的内容（如果有）
const observer = new MutationObserver(mutations => {
    mutations.forEach(mutation => {
        if (mutation.addedNodes.length) {
            bindItemCheckboxEvents();
        }
    });
});

observer.observe(document.body, {
    childList: true,
    subtree: true
});

// template.php 文件中，找到 updateSelectAllButton 函数，添加以下代码
function updateSelectAllButton() {
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
    const totalCount = itemCheckboxes.length;
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    console.log(`选中项数量: ${checkedCount}/${totalCount}`);

    // 更新全选按钮状态
    if (totalCount > 0 && checkedCount === totalCount) {
        isSelectAll = true;
        masterCheckbox.checked = true;
        selectAllBtn.innerHTML = '<i class="fa fa-check-square mr-1"></i>取消全选';
    } else {
        isSelectAll = false;
        masterCheckbox.checked = false;
        selectAllBtn.innerHTML = '<i class="fa fa-square-o mr-1"></i>全选';
    }

    // 更新批量操作按钮状态
    if (checkedCount > 0) {
        enableButton(copySelectedBtn);
        deleteSelectedBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        deleteSelectedBtn.disabled = false;
        // 启用批量移动按钮
        enableButton(document.getElementById('moveSelectedBtn'));
         // 启用批量下载按钮
        enableButton(document.getElementById('downloadSelectedBtn'));

    } else {
        disableButton(copySelectedBtn);
        deleteSelectedBtn.classList.add('opacity-50', 'cursor-not-allowed');
        deleteSelectedBtn.disabled = true;
        // 禁用批量移动按钮
        disableButton(document.getElementById('moveSelectedBtn'));
            // 禁用批量下载按钮
        disableButton(document.getElementById('downloadSelectedBtn'));

    }
}






















// 辅助函数：启用按钮
function enableButton(button) {
    if (button) {
        button.classList.remove('opacity-50', 'cursor-not-allowed');
        button.disabled = false;
    }
}

// 辅助函数：禁用按钮
function disableButton(button) {
    if (button) {
        button.classList.add('opacity-50', 'cursor-not-allowed');
        button.disabled = true;
    }
}

// 检查CSS是否阻止点击
function checkCSSBlocking() {
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    itemCheckboxes.forEach(checkbox => {
        const style = window.getComputedStyle(checkbox);
        if (style.pointerEvents === 'none' || 
            style.display === 'none' || 
            style.visibility === 'hidden' ||
            parseFloat(style.opacity) === 0) {
            console.warn('发现可能阻止点击的CSS样式:', checkbox);
        }
    });
}

// 页面加载后检查
setTimeout(checkCSSBlocking, 1000);

console.log('多选功能初始化完成，等待交互...');

// 全选/取消全选控制
let isSelectAll = false;
const masterCheckbox = document.getElementById('masterCheckbox');
const selectAllBtn = document.getElementById('selectAllBtn');
const copySelectedBtn = document.getElementById('copySelectedBtn');
// const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

if (!masterCheckbox || !selectAllBtn) {
    console.error('关键元素未找到，请检查HTML结构');
    return;
}

// 初始化批量操作按钮状态
updateSelectAllButton();

// 全选按钮点击事件
selectAllBtn.addEventListener('click', function() {
    isSelectAll = !isSelectAll;
    masterCheckbox.checked = isSelectAll;
    updateItemCheckboxes(isSelectAll);
    updateSelectAllButton();
});

// 主复选框变化事件
masterCheckbox.addEventListener('change', function() {
    isSelectAll = this.checked;
    updateItemCheckboxes(isSelectAll);
    updateSelectAllButton();
});

// 更新所有项目复选框状态
function updateItemCheckboxes(checked) {
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = checked;
        // 触发change事件，确保视觉状态更新
        const event = new Event('change', { bubbles: true });
        checkbox.dispatchEvent(event);
    });
}








            const ctx = document.getElementById('storageChart').getContext('2d');
            
            const storageChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['已使用', '剩余'],
                    datasets: [{
                        data: [<?php echo $usedSpace; ?>, <?php echo $freeSpace; ?>],
                        backgroundColor: [
                            '#165DFF',  // 主色调
                            '#E5E7EB'   // 灰色
                        ],
                        borderWidth: 0,
                        cutout: '70%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const percentage = ((value / (<?php echo $totalSpace; ?>)) * 100).toFixed(1);
                                    return `${context.label}: ${formatBytes(value)} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
            
            // 格式化字节函数
            function formatBytes(bytes) {
                if (bytes === 0) return '0 Bytes';
                
                const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(bytes) / Math.log(1024));
                
                return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
            }





            // template.php 文件中，找到 newFolderBtn 点击事件部分，修改为以下代码
            const newFolderBtn = document.getElementById('newFolderBtn');
            if (newFolderBtn) {
                newFolderBtn.addEventListener('click', function() {
                    const modalContent = `
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">新建文件夹</h3>
                            <form method="POST" id="newFolderForm">
                                <input type="hidden" name="create_folder" value="1">
                                <div class="mb-4">
                                    <label for="folder_name" class="block text-sm font-medium text-gray-700 mb-1">文件夹名称</label>
                                    <input type="text" id="folder_name" name="folder_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-focus" required>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="closeModal()">取消</button>
                                    <button type="submit" name="create_folder" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">创建</button>
                                </div>
                            </form>
                        </div>
                    `;
                    openModal(modalContent);
                });
            }


            // template.php 文件中，找到 newFolderBtn 点击事件部分，修改为以下代码
            const newFolderBtns = document.getElementById('newFolderBtns');
            if (newFolderBtns) {
                newFolderBtns.addEventListener('click', function() {
                    const modalContents = `
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">新建文件夹</h3>
                            <form method="POST" id="newFolderForm">
                                <input type="hidden" name="create_folder" value="1">
                                <div class="mb-4">
                                    <label for="folder_name" class="block text-sm font-medium text-gray-700 mb-1">文件夹名称</label>
                                    <input type="text" id="folder_name" name="folder_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-focus" required>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="closeModal()">取消</button>
                                    <button type="submit" name="create_folder" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">创建</button>
                                </div>
                            </form>
                        </div>
                    `;
                    openModal(modalContents);
                });
            }





  
});



// template.php 文件中，找到 JavaScript 部分，添加以下代码
// 批量移动按钮点击事件
document.getElementById('moveSelectedBtn').addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('请先选择要移动的文件');
        return;
    }

    // 获取所有文件夹选项
    const folders = <?php echo json_encode($allFolders); ?>;
    let folderOptions = '';

    folders.forEach(folder => {
        if (folder.relativePath !== '<?php echo $relativePath; ?>') {
            folderOptions += `<option value="${folder.relativePath}">${folder.relativePath || '根目录'}</option>`;
        }
    });

    if (!folderOptions) {
        alert('没有可移动到的其他文件夹');
        return;
    }

    // 构建批量移动表单
    let formHtml = `
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">批量移动文件</h3>
            <form method="POST" id="moveSelectedFilesForm">
                <input type="hidden" name="move_selected_files" value="1">
                ${Array.from(selectedCheckboxes).map(checkbox => `<input type="hidden" name="selected_files[]" value="${checkbox.getAttribute('data-path')}">`).join('')}
                <div class="mb-4">
                    <label for="target_folder" class="block text-sm font-medium text-gray-700 mb-1">目标文件夹</label>
                    <select id="target_folder" name="target_folder" class="w-full px-3 py-2 border border-gray-300 rounded-lg input-focus" required>
                        ${folderOptions}
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="closeModal()">取消</button>
                    <button type="submit" name="move_selected_files" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">移动</button>
                </div>
            </form>
        </div>
    `;

    openModal(formHtml);
});















// template.php 文件中，找到 JavaScript 部分，添加以下代码
// 批量下载按钮点击事件
document.getElementById('downloadSelectedBtn').addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('请先选择要下载的文件');
        return;
    }

    let selectedFiles = [];
    selectedCheckboxes.forEach(checkbox => {
        selectedFiles.push(checkbox.getAttribute('data-id'));
    });

    let url = 'download.php?files=' + encodeURIComponent(selectedFiles.join(','));
    window.location.href = url;
});





// template.php 文件中，找到 JavaScript 部分，添加以下代码
// 处理下载文件按钮点击事件
document.querySelectorAll('[data-action="download-file"]').forEach(button => {
    button.addEventListener('click', function(e) {
        e.stopPropagation();
        const fileId = this.getAttribute('data-id');
        let url = 'download.php?file=' + fileId;
        window.location.href = url;
    });
});




document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('请先选择要删除的文件');
        return;
    }

    // 构建文件名称列表
    let fileNames = '';
    selectedCheckboxes.forEach(checkbox => {
        const name = checkbox.closest('tr').querySelector('td:nth-child(1)').textContent.trim();
        fileNames += `<li class="text-gray-600">${name}</li>`;
    });

    // 构建自定义确认弹窗（与新建文件夹弹窗样式一致）
    const modalContent = `
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">删除选中的文件</h3>
            <p class="text-gray-600 mb-6">确定要删除以下文件吗？此操作不可撤销。</p>
            <ul class="list-disc list-inside mb-6">
                ${fileNames}
            </ul>
<form method="POST" id="batchDeleteForm">
                 <input type="hidden" name="delete_selected_files" value="1">
                 ${Array.from(selectedCheckboxes).map(checkbox => `<input type="hidden" name="selected_files[]" value="${checkbox.getAttribute('data-id')}">`).join('')}
                 <div class="flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="closeModal()">取消</button>
                    <button type="submit" name="delete_selected_files" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition-colors">删除</button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
});


// document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
//     const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
//     if (selectedCheckboxes.length === 0) {
//         alert('请先选择要删除的文件');
//         return;
//     }

//     // 构建自定义确认弹窗（与新建文件夹弹窗样式一致）
//     const modalContent = `
//         <div class="p-6">
//             <h3 class="text-lg font-medium text-gray-900 mb-4">删除选中的文件</h3>
//             <p class="text-gray-600 mb-6">确定要删除选中的文件吗？此操作不可撤销。</p>
//             <div class="flex justify-end space-x-3">
//                 <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="closeModal()">取消</button>
//                 <button type="button" id="confirmDeleteBtn" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition-colors">删除</button>
//             </div>
//         </div>
//     `;
//     openModal(modalContent);

//     // 绑定确认删除按钮事件
//     document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
//         const selectedFiles = [];
//         selectedCheckboxes.forEach(checkbox => {
//             selectedFiles.push(checkbox.getAttribute('data-path'));
//         });

//         // 发送 AJAX 请求
//         const xhr = new XMLHttpRequest();
//         xhr.open('POST', 'index.php', true);
//         xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
//         xhr.onreadystatechange = function() {
//             if (xhr.readyState === 4 && xhr.status === 200) {
//                 const response = JSON.parse(xhr.responseText);
//                 closeModal(); // 关闭弹窗
                
//                 // 显示通知消息（保持原有逻辑不变）
//                 const notificationContainer = document.querySelector('.notification-container');
//                 if (!notificationContainer) {
//                     const container = document.createElement('div');
//                     container.className = 'notification-container fixed top-20 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md';
//                     document.body.appendChild(container);
//                 }
                
//                 const notification = document.createElement('div');
//                 notification.className = response.success 
//                     ? 'bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-2 flex items-center animate-fade-in'
//                     : 'bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-2 flex items-center animate-fade-in';
                
//                 notification.innerHTML = `
//                     <i class="fa ${response.success ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
//                     <span>${response.message}</span>
//                 `;
                
//                 document.querySelector('.notification-container').appendChild(notification);
                
//                 // 添加动画样式
//                 const style = document.createElement('style');
//                 style.textContent = `
//                     @keyframes fadeIn {
//                         from { opacity: 0; transform: translateY(-10px); }
//                         to { opacity: 1; transform: translateY(0); }
//                     }
//                     .animate-fade-in {
//                         animation: fadeIn 0.3s ease-out forwards;
//                     }
//                 `;
//                 document.head.appendChild(style);
                
//                 // 自动关闭通知
//                 setTimeout(() => {
//                     notification.style.opacity = '0';
//                     notification.style.transform = 'translateY(-10px)';
//                     notification.style.transition = 'opacity 0.3s, transform 0.3s';
//                     setTimeout(() => {
//                         notification.remove();
//                     }, 300);
//                 }, 3000);
                
//                 // 动态移除被删除的文件项
//                 if (response.success) {
//                     selectedCheckboxes.forEach(checkbox => {
//                         const row = checkbox.closest('tr');
//                         if (row) {
//                             row.style.opacity = '0';
//                             row.style.transform = 'translateX(-20px)';
//                             row.style.transition = 'opacity 0.3s, transform 0.3s';
//                             setTimeout(() => {
//                                 row.remove();
//                             }, 300);
//                         }
//                     });
//                 }
//             }
//         };
//         const data = `delete_selected_files=1&selected_files[]=${selectedFiles.join('&selected_files[]=')}`;
//         xhr.send(data);
//     });
// });



    </script>
    



</body>
</html>    