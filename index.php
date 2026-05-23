<?php

//  * @LastEditors: Swk(葫芦侠:陌南尘。)
//  * @hitokoto: 先谋杀全世界的噪音 再审判心跳的供词.

ob_clean();
// 配置文件
require_once 'config.php';

// 会话管理
session_start();

// 验证登录状态
function isLoggedIn() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

// 登录处理
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['authenticated'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        }
    }
    
    $loginError = '用户名或密码错误';
    $stmt->close();
    $conn->close();
}

// 登出处理
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// 如果未登录，显示登录页面
if (!isLoggedIn()) {
    include 'login.php';
    exit;
}

// 文件管理功能
// 获取当前路径
// 获取当前路径
function getCurrentPath() {
    $path = isset($_GET['path']) ? $_GET['path'] : '';
    $path = urldecode($path);
    $path = UPLOAD_DIR . '/' . ltrim($path, '/');
    
    // 安全检查：确保路径在上传目录内
    if (strpos(realpath($path), realpath(UPLOAD_DIR)) !== 0) {
        return UPLOAD_DIR;
    }
    
    return $path;
}
// 获取相对路径
function getRelativePath($path) {
    $uploadDir = realpath(UPLOAD_DIR);
    $realPath = realpath($path);
    
    if (strpos($realPath, $uploadDir) === 0) {
        return substr($realPath, strlen($uploadDir) + 1);
    }
    
    return '';
}

// 创建文件夹
if (isset($_POST['create_folder'])) {
    $folderName = $_POST['folder_name'];
    $currentPath = getCurrentPath();
    $newFolderPath = $currentPath . '/' . $folderName;
    
    if (!file_exists($newFolderPath)) {
        if (mkdir($newFolderPath, 0777, true)) {
            $successMessage = '文件夹创建成功';
        } else {
            $errorMessage = '文件夹创建失败';
        }
    } else {
        $errorMessage = '文件夹已存在';
    }
}

// 重命名文件夹
if (isset($_POST['rename_folder'])) {
    $oldName = $_POST['old_name'];
    $newName = $_POST['new_name'];
    $currentPath = getCurrentPath();
    
    $oldPath = $currentPath . '/' . $oldName;
    $newPath = $currentPath . '/' . $newName;
    
    if (file_exists($oldPath) && !file_exists($newPath)) {
        if (rename($oldPath, $newPath)) {
            $successMessage = '文件夹重命名成功';
        } else {
            $errorMessage = '文件夹重命名失败';
        }
    } else {
        $errorMessage = '操作失败，可能源文件夹不存在或目标文件夹已存在';
    }
}

// 删除文件夹
if (isset($_POST['delete_folder'])) {
    $folderName = $_POST['folder_name'];
    $currentPath = getCurrentPath();
    $folderPath = $currentPath . '/' . $folderName;
    
    if (is_dir($folderPath)) {
        // 递归删除文件夹
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        
        if (rmdir($folderPath)) {
            $successMessage = '文件夹删除成功';
        } else {
            $errorMessage = '文件夹删除失败';
        }
    } else {
        $errorMessage = '文件夹不存在';
    }
}



// index.php

// 批量删除文件
if (isset($_POST['delete_selected_files'])) {
    $selectedFileIds = $_POST['selected_files'];
    $userId = $_SESSION['user_id'];
    
    try {
        $conn = getDBConnection();
        $ossClient = getOSSClient();
        $successCount = 0;
        
        foreach ($selectedFileIds as $fileId) {
            $stmt = $conn->prepare("SELECT file_path FROM files WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $fileId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $file = $result->fetch_assoc();
                $ossPath = $file['file_path'];
                
                $ossClient->deleteObject(OSS_BUCKET, $ossPath);
                
                $deleteStmt = $conn->prepare("DELETE FROM files WHERE id = ? AND user_id = ?");
                $deleteStmt->bind_param("ii", $fileId, $userId);
                $deleteStmt->execute();
                
                if ($deleteStmt->affected_rows > 0) {
                    $successCount++;
                }
                $deleteStmt->close();
            }
            
            $stmt->close();
        }
        
        $conn->close();
        
        if ($successCount > 0) {
            $successMessage = "成功删除 {$successCount} 个文件";
        } else {
            $errorMessage = '文件删除失败';
        }
    } catch (\Exception $e) {
        $errorMessage = '批量删除失败: ' . $e->getMessage();
    }
}


// 重命名文件
if (isset($_POST['rename_file'])) {
    $oldName = $_POST['old_name'];
    $newName = $_POST['new_name'];
    $currentPath = getCurrentPath();
    
    $oldPath = $currentPath . '/' . $oldName;
    $newPath = $currentPath . '/' . $newName;
    
    if (file_exists($oldPath) && !file_exists($newPath)) {
        if (rename($oldPath, $newPath)) {
            $successMessage = '文件重命名成功';
        } else {
            $errorMessage = '文件重命名失败';
        }
    } else {
        $errorMessage = '操作失败，可能源文件不存在或目标文件已存在';
    }
}

// 删除文件
if (isset($_POST['delete_file'])) {
    $fileId = $_POST['file_id'];
    $userId = $_SESSION['user_id'];
    
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT file_path FROM files WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $fileId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $file = $result->fetch_assoc();
            $ossPath = $file['file_path'];
            
            $ossClient = getOSSClient();
            $ossClient->deleteObject(OSS_BUCKET, $ossPath);
            
            $deleteStmt = $conn->prepare("DELETE FROM files WHERE id = ? AND user_id = ?");
            $deleteStmt->bind_param("ii", $fileId, $userId);
            $deleteStmt->execute();
            
            if ($deleteStmt->affected_rows > 0) {
                $successMessage = '文件删除成功';
            } else {
                $errorMessage = '文件删除失败';
            }
            $deleteStmt->close();
        } else {
            $errorMessage = '文件不存在或无权删除';
        }
        
        $stmt->close();
        $conn->close();
    } catch (\Exception $e) {
        $errorMessage = '删除失败: ' . $e->getMessage();
    }
}

// 移动文件
if (isset($_POST['move_file'])) {
    $fileName = $_POST['file_name'];
    $targetFolder = $_POST['target_folder'];
    $currentPath = getCurrentPath();
    $filePath = $currentPath . '/' . $fileName;
    
    // 构建目标路径
    $targetFolder = ltrim($targetFolder, '/');
    $targetDir = UPLOAD_DIR . '/' . $targetFolder;
    $targetPath = $targetDir . '/' . $fileName;
    
    // 确保目标目录存在
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // 安全检查：验证目标路径是否在上传目录内
    $uploadDirReal = realpath(UPLOAD_DIR);
    $targetDirReal = realpath(dirname($targetPath));
    
    if (!$uploadDirReal || !$targetDirReal) {
        $errorMessage = '路径解析失败，请检查目录权限';
    } else {
        // 不区分大小写的路径验证（适用于Windows）
        $isSubDir = stripos($targetDirReal, $uploadDirReal) === 0;
        
        if (!$isSubDir) {
            $errorMessage = '目标路径不在上传目录内，操作被拒绝';
        } elseif (file_exists($filePath) && !file_exists($targetPath)) {
            if (rename($filePath, $targetPath)) {
                $successMessage = '文件移动成功';
            } else {
                $errorMessage = '文件移动失败，请检查权限';
            }
        } else {
            $errorMessage = '操作失败，可能源文件不存在或目标文件已存在';
        }
    }
}

// 批量文件上传处理
if (isset($_FILES['files'])) {
    $currentPath = getCurrentPath(); 
    $uploadedFiles = $_FILES['files'];
    $totalFiles = count($uploadedFiles['name']);
    $successCount = 0;

    try {
        // 获取user_id
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        if (!$userId) {
            throw new \Exception('用户未登录或session已过期');
        }

        $ossClient = getOSSClient();
        
        for ($i = 0; $i < $totalFiles; $i++) {
            $fileName = $uploadedFiles['name'][$i];
            $fileTmpName = $uploadedFiles['tmp_name'][$i];
            $fileSize = $uploadedFiles['size'][$i];
            $fileError = $uploadedFiles['error'][$i];

            if ($fileError === 0) {
                // 检查文件是否已存在于OSS
                $ossFileName = OSS_PREFIX . $currentPath . '/' . $fileName;
                $ossFileName = str_replace(UPLOAD_DIR . '/', '', $ossFileName);
                $ossFileName = str_replace('//', '/', $ossFileName);
                
                // 检查文件是否已存在，如果存在则自动重命名
                if ($ossClient->doesObjectExist(OSS_BUCKET, $ossFileName)) {
                    $fileNameInfo = pathinfo($fileName);
                    $baseName = $fileNameInfo['filename'];
                    $extension = isset($fileNameInfo['extension']) ? '.' . $fileNameInfo['extension'] : '';
                    $counter = 1;

                    while ($ossClient->doesObjectExist(OSS_BUCKET, OSS_PREFIX . $currentPath . '/' . $baseName . '_' . $counter . $extension)) {
                        $counter++;
                    }

                    $fileName = $baseName . '_' . $counter . $extension;
                    $ossFileName = OSS_PREFIX . $currentPath . '/' . $fileName;
                    $ossFileName = str_replace('//', '/', $ossFileName);
                }

                // 上传到OSS
                $options = [
                    \OSS\OssClient::OSS_HEADERS => [
                        'Content-Type' => mime_content_type($fileTmpName)
                    ]
                ];
                
                try {
                    $result = $ossClient->uploadFile(OSS_BUCKET, $ossFileName, $fileTmpName, $options);
                    
                    if ($result) {
                        // 记录文件信息到数据库
                        $conn = getDBConnection();
                        $stmt = $conn->prepare("INSERT INTO files (user_id, file_name, file_path, file_size, file_type) VALUES (?, ?, ?, ?, ?)");
                        $fileType = 'file';
                        $stmt->bind_param("issis", $userId, $fileName, $ossFileName, $fileSize, $fileType);
                        
                        if ($stmt->execute()) {
                            $successCount++;
                        } else {
                            error_log("数据库插入失败: " . $conn->error);
                        }
                        
                        $stmt->close();
                        $conn->close();
                    }
                } catch (\Exception $e) {
                    error_log("OSS上传失败: " . $e->getMessage());
                }
            }
        }
    } catch (\Exception $e) {
        $_SESSION['errorMessage'] = '文件上传失败: ' . $e->getMessage();
        error_log("上传异常: " . $e->getMessage());
    }

    if ($successCount > 0) {
        $_SESSION['successMessage'] = "成功上传 {$successCount} 个文件";
    } else if (!isset($_SESSION['errorMessage'])) {
        $_SESSION['errorMessage'] = '文件上传失败';
    }

    // 刷新页面
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    exit;
}


// 获取文件列表
$currentPath = getCurrentPath();
$relativePath = getRelativePath($currentPath);

// 获取本地文件夹
$folders = [];
if (is_dir($currentPath)) {
    $directory = new DirectoryIterator($currentPath);
    foreach ($directory as $item) {
        if ($item->isDot()) continue;
        if ($item->isDir()) {
            $folders[] = [
                'name' => $item->getFilename(),
                'path' => getRelativePath($item->getPathname()),
                'time' => $item->getMTime(),
                'type' => 'folder'
            ];
        }
    }
}

// 从数据库获取文件信息
$files = [];
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$userId) {
    // 如果没有user_id，尝试重新获取用户信息
    if (isset($_SESSION['username'])) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $_SESSION['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $userId = $user['id'];
            $_SESSION['user_id'] = $userId;
        }
        $stmt->close();
        $conn->close();
    }
}

if ($userId) {
    $conn = getDBConnection();
    $pathPrefix = OSS_PREFIX;
    $pathPrefix = rtrim($pathPrefix, '/') . '/';
    
    // 根据当前浏览路径构建查询条件
    if ($relativePath) {
        $searchPattern = $pathPrefix . $relativePath . '/%';
    } else {
        $searchPattern = $pathPrefix . '%';
    }

    $stmt = $conn->prepare("SELECT id, file_name, file_path, file_size, upload_time FROM files WHERE user_id = ? AND file_path LIKE ? ORDER BY upload_time DESC");
    $stmt->bind_param("is", $userId, $searchPattern);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $fileName = $row['file_name'];
        $filePath = $row['file_path'];
        
        // 获取文件所在目录（相对于OSS_PREFIX）
        $fileDir = str_replace($pathPrefix, '', $filePath);
        $fileDir = dirname($fileDir);
        
        // 只显示当前路径下的文件（不在子目录中的文件）
        $isInCurrentPath = false;
        
        if ($relativePath === '' && $fileDir === '.') {
            // 根目录下的文件
            $isInCurrentPath = true;
        } elseif ($relativePath !== '' && $fileDir === $relativePath) {
            // 指定目录下的文件
            $isInCurrentPath = true;
        }
        
        if ($isInCurrentPath) {
            $files[] = [
                'id' => $row['id'],
                'name' => $fileName,
                'path' => $filePath,
                'time' => strtotime($row['upload_time']),
                'size' => $row['file_size'],
                'type' => 'application/octet-stream'
            ];
        }
    }

    $stmt->close();
    $conn->close();
}

// 搜索功能
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($searchTerm)) {
    $files = array_filter($files, function($file) use ($searchTerm) {
        return stripos($file['name'], $searchTerm) !== false;
    });
    $folders = array_filter($folders, function($folder) use ($searchTerm) {
        return stripos($folder['name'], $searchTerm) !== false;
    });
}

// 排序功能
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'asc';

// 排序
usort($files, function($a, $b) use ($sortBy, $sortOrder) {
    if ($sortBy === 'name') {
        $result = strcasecmp($a['name'], $b['name']);
    } elseif ($sortBy === 'time') {
        $result = $a['time'] - $b['time'];
    } elseif ($sortBy === 'size') {
        $result = $a['size'] - $b['size'];
    } elseif ($sortBy === 'type') {
        $result = strcasecmp($a['type'], $b['type']);
    } else {
        $result = 0;
    }
    
    return ($sortOrder === 'desc') ? -$result : $result;
});

usort($folders, function($a, $b) use ($sortBy, $sortOrder) {
    $result = strcasecmp($a['name'], $b['name']);
    return ($sortOrder === 'desc') ? -$result : $result;
});

// 分页功能
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, $currentPage);

// 分页
$totalFiles = count($files);
$totalPages = ceil($totalFiles / $itemsPerPage);
$currentPage = min($currentPage, max(1, $totalPages));
$offset = ($currentPage - 1) * $itemsPerPage;
$paginatedFiles = array_slice($files, $offset, $itemsPerPage);

// 获取所有文件夹用于移动操作
$allFolders = [];
function scanFolders($dir, &$result, $baseDir) {
    $directory = new DirectoryIterator($dir);
    foreach ($directory as $item) {
        if ($item->isDot()) continue;
        if ($item->isDir()) {
            $relativePath = substr($item->getPathname(), strlen($baseDir) + 1);
            $result[] = [
                'name' => $item->getFilename(),
                'relativePath' => $relativePath
            ];
            scanFolders($item->getPathname(), $result, $baseDir);
        }
    }
}
scanFolders(UPLOAD_DIR, $allFolders, UPLOAD_DIR);




// 批量移动文件
if (isset($_POST['move_selected_files'])) {
    $selectedFiles = $_POST['selected_files'];
    $targetFolder = $_POST['target_folder'];
    $currentPath = getCurrentPath();

    // 构建目标路径
    $targetFolder = ltrim($targetFolder, '/');
    $targetDir = UPLOAD_DIR . '/' . $targetFolder;

    // 确保目标目录存在
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // 安全检查：验证目标路径是否在上传目录内
    $uploadDirReal = realpath(UPLOAD_DIR);
    $targetDirReal = realpath(dirname($targetDir));

    if (!$uploadDirReal || !$targetDirReal) {
        $_SESSION['errorMessage'] = '路径解析失败，请检查目录权限';
        header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
        exit;
    } else {
        // 不区分大小写的路径验证（适用于Windows）
        $isSubDir = stripos($targetDirReal, $uploadDirReal) === 0;

        if (!$isSubDir) {
            $_SESSION['errorMessage'] = '目标路径不在上传目录内，操作被拒绝';
            header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
            exit;
        } else {
            $successCount = 0;
            foreach ($selectedFiles as $filePath) {
                $fileName = basename($filePath);
                $currentFilePath = $currentPath . '/' . $fileName;
                $targetFilePath = $targetDir . '/' . $fileName;

                if (file_exists($currentFilePath) && !file_exists($targetFilePath)) {
                    if (rename($currentFilePath, $targetFilePath)) {
                        $successCount++;
                    }
                }
            }

            if ($successCount > 0) {
                $_SESSION['successMessage'] = "成功移动 {$successCount} 个文件";
                header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
                exit;
            } else {
                $_SESSION['errorMessage'] = '文件移动失败，请检查权限';
                header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
                exit;
            }
        }
    }
}

















// index.php 文件中，添加以下代码
// 单个文件下载

if (isset($_GET['download_file'])) {
    $fileId = $_GET['file'];
    $userId = $_SESSION['user_id'];
    
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT file_name, file_path FROM files WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $fileId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $file = $result->fetch_assoc();
            $fileName = $file['file_name'];
            $ossPath = $file['file_path'];
            
            // 服务器端代理下载（避免Referer策略问题）
            $ossClient = getOSSClient();
            
            // 临时文件路径
            $tempFile = sys_get_temp_dir() . '/' . md5($ossPath . $userId) . '_' . basename($ossPath);
            
            // 从OSS下载文件到临时目录
            $options = [
                \OSS\OssClient::OSS_FILE_DOWNLOAD => $tempFile
            ];
            
            $ossClient->getObject(OSS_BUCKET, $ossPath, $options);
            
            if (file_exists($tempFile)) {
                // 清除输出缓冲区
                if (ob_get_level()) {
                    ob_end_clean();
                }
                
                // 设置下载头信息
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($tempFile));
                
                // 输出文件内容
                $file = fopen($tempFile, 'rb');
                if ($file) {
                    while (!feof($file)) {
                        print(fread($file, 1024 * 8));
                        ob_flush();
                        flush();
                    }
                    fclose($file);
                }
                
                // 删除临时文件
                unlink($tempFile);
                exit;
            } else {
                $errorMessage = '文件下载失败';
            }
        } else {
            $errorMessage = '文件不存在或无权访问';
        }
        
        $stmt->close();
        $conn->close();
    } catch (\Exception $e) {
        $errorMessage = '文件下载失败: ' . $e->getMessage();
    }
}


// index.php 文件中，批量文件下载部分

if (isset($_GET['download_selected_files'])) {
    $selectedFileIds = explode(',', $_GET['files']);
    $userId = $_SESSION['user_id'];
    
    try {
        $conn = getDBConnection();
        $zip = new ZipArchive();
        $zipFileName = sys_get_temp_dir() . '/download_' . time() . '_' . $userId . '.zip';
        $ossClient = getOSSClient();
        
        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            $addedFiles = 0;
            
            foreach ($selectedFileIds as $fileId) {
                $stmt = $conn->prepare("SELECT file_name, file_path FROM files WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $fileId, $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $file = $result->fetch_assoc();
                    $fileName = $file['file_name'];
                    $ossPath = $file['file_path'];
                    
                    // 从OSS下载文件到临时目录
                    $tempFile = sys_get_temp_dir() . '/' . $fileName;
                    $ossClient->getObject(OSS_BUCKET, $ossPath, [
                        'OssClient::OSS_FILE_DOWNLOAD' => $tempFile
                    ]);
                    
                    if (file_exists($tempFile)) {
                        $zip->addFile($tempFile, $fileName);
                        $addedFiles++;
                    }
                }
                
                $stmt->close();
            }
            
            $zip->close();
            $conn->close();
            
            if ($addedFiles > 0 && file_exists($zipFileName)) {
                ob_clean();
                flush();

                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="files_' . time() . '.zip"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($zipFileName));

                $file = fopen($zipFileName, 'rb');
                if ($file) {
                    while (!feof($file)) {
                        print(fread($file, 1024 * 8));
                        ob_flush();
                        flush();
                    }
                    fclose($file);
                }

                unlink($zipFileName);
                exit;
            } else {
                $errorMessage = '没有可下载的文件';
            }
        } else {
            $errorMessage = '无法创建压缩文件';
        }
    } catch (\Exception $e) {
        $errorMessage = '批量下载失败: ' . $e->getMessage();
    }
}


// 获取存储使用情况
function formatSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    
    $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes, 1024));
    
    return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
}

$totalSpace = disk_total_space(UPLOAD_DIR);
$freeSpace = disk_free_space(UPLOAD_DIR);
$usedSpace = $totalSpace - $freeSpace;
$spaceUsagePercent = ($totalSpace > 0) ? ($usedSpace / $totalSpace) * 100 : 0;

include 'template.php';
?>    