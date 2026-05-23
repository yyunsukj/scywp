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
    // 获取用户列表
    $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $result->fetch_all(MYSQLI_ASSOC);
    
    $userList = array_map(function($user) {
        return [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'email' => '',
            'phone' => '',
            'avatar' => '',
            'createTime' => $user['created_at'],
            'status' => 'active',
            'roles' => ['user']
        ];
    }, $users);
    
    echo json_encode([
        'code' => 200,
        'message' => '获取成功',
        'data' => $userList
    ]);
}

$conn->close();
?>