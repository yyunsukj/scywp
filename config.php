<?php

//  * @LastEditors: Swk(葫芦侠:陌南尘。)
//  * @hitokoto: 先谋杀全世界的噪音 再审判心跳的供词.

// 只在第一次包含时清理缓冲区
if (!defined('CONFIG_PHP_LOADED')) {
    define('CONFIG_PHP_LOADED', true);
    if (ob_get_level() > 0) {
        ob_clean();
    }
}

// 加载环境变量
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// MySQL数据库配置
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'scyp');

// 阿里云OSS配置
define('OSS_ACCESS_KEY_ID', getenv('OSS_ACCESS_KEY_ID') ?: '');
define('OSS_ACCESS_KEY_SECRET', getenv('OSS_ACCESS_KEY_SECRET') ?: '');
define('OSS_BUCKET', getenv('OSS_BUCKET') ?: 'scywp');
define('OSS_ENDPOINT', getenv('OSS_ENDPOINT') ?: 'http://oss-cn-shenzhen.aliyuncs.com');
define('OSS_PREFIX', 'uploads/');

// 文件上传目录
define('UPLOAD_DIR', __DIR__ . '/uploads');

// 创建数据库连接
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("数据库连接失败: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

// 创建OSS客户端
function getOSSClient() {
    require_once __DIR__ . '/vendor/autoload.php';
    
    $ossClient = new \OSS\OssClient(
        OSS_ACCESS_KEY_ID,
        OSS_ACCESS_KEY_SECRET,
        OSS_ENDPOINT
    );
    
    return $ossClient;
}

// 确保上传目录存在
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

?>