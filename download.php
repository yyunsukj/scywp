<?php

//  * @LastEditors: Swk(葫芦侠:陌南尘。)
//  * @hitokoto: 先谋杀全世界的噪音 再审判心跳的供词.

require_once 'config.php';

session_start();

// 验证登录状态
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => '未登录']);
    exit;
}

// 单个文件下载
if (isset($_GET['file'])) {
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
            $tempFile = sys_get_temp_dir() . '/' . md5($ossPath . $userId . time()) . '_' . basename($ossPath);
            
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
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                    fclose($file);
                }
                
                // 删除临时文件
                unlink($tempFile);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => '文件下载失败']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => '文件不存在或无权访问']);
        }
        
        $stmt->close();
        $conn->close();
    } catch (\Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => '文件下载失败: ' . $e->getMessage()]);
    }
}

// 批量文件下载（打包成ZIP）
if (isset($_GET['files'])) {
    $selectedFileIds = explode(',', $_GET['files']);
    $userId = $_SESSION['user_id'];
    
    try {
        $conn = getDBConnection();
        $zip = new ZipArchive();
        $zipFileName = sys_get_temp_dir() . '/download_' . time() . '_' . $userId . '.zip';

        // 清除输出缓冲区
        if (ob_get_level()) {
            ob_end_clean();
        }

        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            $ossClient = getOSSClient();
            $addedFiles = 0;
            $tempFiles = [];
            
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
                    $tempFile = sys_get_temp_dir() . '/' . md5($ossPath . $userId . time()) . '_' . basename($ossPath);
                    $options = [
                        \OSS\OssClient::OSS_FILE_DOWNLOAD => $tempFile
                    ];
                    
                    $ossClient->getObject(OSS_BUCKET, $ossPath, $options);
                    
                    if (file_exists($tempFile)) {
                        $zip->addFile($tempFile, $fileName);
                        $tempFiles[] = $tempFile;
                        $addedFiles++;
                    }
                }
                
                $stmt->close();
            }
            
            $zip->close();
            $conn->close();
            
            if ($addedFiles > 0 && file_exists($zipFileName)) {
                // 设置下载头信息
                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="files_' . time() . '.zip"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($zipFileName));

                // 输出文件内容
                $file = fopen($zipFileName, 'rb');
                if ($file) {
                    while (!feof($file)) {
                        print(fread($file, 1024 * 8));
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                    fclose($file);
                }

                // 清理临时文件
                unlink($zipFileName);
                foreach ($tempFiles as $tempFile) {
                    if (file_exists($tempFile)) {
                        unlink($tempFile);
                    }
                }
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => '批量下载失败，没有可下载的文件']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => '无法创建压缩文件']);
        }
    } catch (\Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => '批量下载失败: ' . $e->getMessage()]);
    }
}

// 如果没有有效的下载参数，返回错误
header('Content-Type: application/json');
echo json_encode(['error' => '无效的下载请求']);
