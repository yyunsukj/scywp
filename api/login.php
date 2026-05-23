<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();

// 简单的CORS预检处理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取POST数据
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // 如果JSON解析失败，尝试从POST表单获取
    if ($data === null) {
        $data = $_POST;
    }
    
    if (isset($data['username']) && isset($data['password'])) {
        // 验证登录
        if ($data['username'] === 'admin' && $data['password'] === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $data['username'];
            
            echo json_encode([
                'code' => 200,
                'message' => '登录成功',
                'data' => [
                    'token' => session_id(),
                    'userInfo' => [
                        'id' => 1,
                        'username' => 'admin',
                        'avatar' => '',
                        'email' => 'admin@example.com',
                        'phone' => '',
                        'roles' => ['admin'],
                        'permissions' => ['*']
                    ]
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'code' => 401,
                'message' => '账号或密码错误'
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'code' => 400,
            'message' => '缺少必要参数'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'code' => 405,
        'message' => '不支持的请求方法'
    ]);
}
?>