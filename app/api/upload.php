<?php
// 显示所有错误
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 允许跨域访问
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 增加超时时间
set_time_limit(3600);  // 1小时

// 设置上传目录
define('UPLOAD_FOLDER', dirname(dirname(__FILE__)) . '/uploads');

function securePath($path) {
    $path = str_replace(array('..', './'), '', $path);
    return trim($path, '/');
}

function sendJson($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function isAllowedFileType($filename) {
    // 扩展允许的文件类型列表
    $allowed = array(
        // 图片
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg',
        // 文档
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf',
        // 压缩文件
        'zip', 'rar', '7z', 'tar', 'gz',
        // 音视频
        'mp3', 'mp4', 'avi', 'mov', 'wmv',
        // 其他
        'iso', 'exe', 'apk', 'dmg'
    );
    
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $allowed);
}

try {
    if (!isset($_FILES['files'])) {
        sendJson(array('error' => 'No file uploaded'));
    }

    // 检查是否是多文件上传
    if (is_array($_FILES['files']['name'])) {
        $fileName = $_FILES['files']['name'][0];
        $fileSize = $_FILES['files']['size'][0];
        $fileTmp = $_FILES['files']['tmp_name'][0];
    } else {
        $fileName = $_FILES['files']['name'];
        $fileSize = $_FILES['files']['size'];
        $fileTmp = $_FILES['files']['tmp_name'];
    }
    
    // 文件类型检查
    if (!isAllowedFileType($fileName)) {
        sendJson(array('error' => 'Invalid file type: ' . pathinfo($fileName, PATHINFO_EXTENSION)));
    }
    
    $path = isset($_POST['path']) ? $_POST['path'] : '/';
    $filename = basename($fileName);
    $savePath = UPLOAD_FOLDER . '/' . securePath($path) . '/' . $filename;
    
    // 创建目录
    if (!file_exists(dirname($savePath))) {
        mkdir(dirname($savePath), 0777, true);
    }

    // 直接移动上传的文件
    if (move_uploaded_file($fileTmp, $savePath)) {
        chmod($savePath, 0644);
        sendJson(array('success' => true));
    } else {
        throw new Exception('Failed to move uploaded file');
    }
} catch (Exception $e) {
    error_log("Upload failed: " . $e->getMessage());
    sendJson(array('error' => 'Upload failed: ' . $e->getMessage()));
} 