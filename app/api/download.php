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

$path = isset($_GET['path']) ? $_GET['path'] : '';
if (empty($path)) {
    sendJson(array('error' => 'No path specified'));
}

$absPath = UPLOAD_FOLDER . '/' . securePath($path);
if (!file_exists($absPath) || is_dir($absPath)) {
    sendJson(array('error' => 'File not found'));
}

$filename = basename($path);
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($absPath));
readfile($absPath);
exit;