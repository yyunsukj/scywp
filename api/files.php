<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

session_start();

require_once 'config.php';

// 验证登录状态
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['code' => 401, 'message' => '未登录']);
    exit;
}

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 获取文件列表
    $result = $conn->query("SELECT f.*, u.username FROM files f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.upload_time DESC");
    $files = $result->fetch_all(MYSQLI_ASSOC);
    
    $fileList = array_map(function($file) {
        return [
            'id' => (int)$file['id'],
            'fileName' => $file['file_name'],
            'fileSize' => (int)$file['file_size'],
            'fileSizeDisplay' => formatFileSize($file['file_size']),
            'uploadTime' => $file['upload_time'],
            'username' => $file['username'],
            'filePath' => $file['file_path']
        ];
    }, $files);
    
    echo json_encode([
        'code' => 200,
        'message' => '获取成功',
        'data' => $fileList
    ]);
}

// 处理删除文件
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (isset($data['id'])) {
        $fileId = (int)$data['id'];
        
        // 获取文件信息
        $stmt = $conn->prepare("SELECT * FROM files WHERE id = ?");
        $stmt->bind_param("i", $fileId);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();
        
        if ($file) {
            // 从OSS删除文件
            try {
                $ossClient = getOSSClient();
                $ossClient->deleteObject(OSS_BUCKET, $file['file_path']);
            } catch (Exception $e) {
                error_log("OSS删除失败: " . $e->getMessage());
            }
            
            // 从数据库删除
            $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            
            echo json_encode([
                'code' => 200,
                'message' => '删除成功'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'code' => 404,
                'message' => '文件不存在'
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'code' => 400,
            'message' => '缺少文件ID'
        ]);
    }
}

$conn->close();
?>