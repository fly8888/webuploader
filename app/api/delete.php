<?php
// 显示所有错误
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

function removeDirectory($path) {
    $files = array_diff(scandir($path), array('.', '..'));
    foreach ($files as $file) {
        $curPath = $path . '/' . $file;
        is_dir($curPath) ? removeDirectory($curPath) : unlink($curPath);
    }
    return rmdir($path);
}

$path = isset($_POST['path']) ? $_POST['path'] : '';
if (empty($path)) {
    sendJson(array('error' => 'No path specified'));
}

$absPath = UPLOAD_FOLDER . '/' . securePath($path);
if (!file_exists($absPath)) {
    sendJson(array('error' => 'Path not found'));
}

$success = is_dir($absPath) ? 
    removeDirectory($absPath) : 
    unlink($absPath);
    
if ($success) {
    sendJson(array('success' => true));
} else {
    sendJson(array('error' => 'Failed to delete'));
} 