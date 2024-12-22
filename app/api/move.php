<?php
// 显示所有错误
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 设置上传目录
define('UPLOAD_FOLDER', dirname(dirname(__FILE__)) . '/uploads');
function securePath($path) {
    // 改进路径安全处理
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('/\/+/', '/', $path);  // 替换多个斜杠为单个
    $path = str_replace('../', '', $path);
    $path = trim($path, '/');
    return $path;
}

function sendJson($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

$oldPath = isset($_POST['oldPath']) ? $_POST['oldPath'] : '';
$newPath = isset($_POST['newPath']) ? $_POST['newPath'] : '';

if (empty($oldPath) || empty($newPath)) {
    sendJson(array('error' => 'Path not specified'));
}

// 保持目录结尾的斜杠
$isDir = substr($oldPath, -1) === '/';
$absOld = UPLOAD_FOLDER . '/' . securePath($oldPath);
$absNew = UPLOAD_FOLDER . '/' . securePath($newPath);

if ($isDir) {
    $absOld = rtrim($absOld, '/') . '/';
    $absNew = rtrim($absNew, '/') . '/';
}

if (!file_exists($absOld)) {
    sendJson(array('error' => 'Source path not found'));
}

if (file_exists($absNew)) {
    sendJson(array('error' => 'Destination path already exists'));
}

// 创建目标目录
$targetDir = dirname($absNew);
if (!file_exists($targetDir)) {
    if (!mkdir($targetDir, 0755, true)) {
        sendJson(array('error' => 'Failed to create target directory'));
    }
}

if (rename($absOld, $absNew)) {
    sendJson(array('success' => true));
} else {
    sendJson(array('error' => 'Failed to move file'));
} 