<?php
// 显示所有错误
ini_set('display_errors', 1);
error_reporting(E_ALL);


// 允许跨域访问
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 设置上传目录
define('UPLOAD_FOLDER', dirname(dirname(__FILE__)) . '/uploads');

// 辅助函数
function securePath($path) {
    // 移除路径中的 ../ 防止目录遍历
    $path = str_replace(array('..', './'), '', $path);
    return trim($path, '/');
}

function sendJson($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

try {
    // 确保上传目录存在
    if (!file_exists(UPLOAD_FOLDER)) {
        if (!mkdir(UPLOAD_FOLDER, 0777, true)) {
            error_log("Failed to create directory: " . UPLOAD_FOLDER);
            sendJson(array('error' => 'Failed to create upload directory'));
        }
        chmod(UPLOAD_FOLDER, 0777);
    }

    // 检查目录权限
    if (!is_readable(UPLOAD_FOLDER)) {
        error_log("Upload directory not readable: " . UPLOAD_FOLDER);
        sendJson(array('error' => 'Upload directory not readable'));
    }

    if (!is_writable(UPLOAD_FOLDER)) {
        error_log("Upload directory not writable: " . UPLOAD_FOLDER);
        sendJson(array('error' => 'Upload directory not writable'));
    }

    // 获取目录列表
    $path = isset($_GET['path']) ? $_GET['path'] : '/';
    $absPath = UPLOAD_FOLDER . '/' . securePath($path);
    
    // 检查目录是否存在和可访问
    if (!file_exists($absPath)) {
        error_log("Directory not found: " . $absPath);
        sendJson(array('error' => 'Path not found'));
    }
    
    if (!is_readable($absPath)) {
        error_log("Directory not readable: " . $absPath);
        sendJson(array('error' => 'Permission denied'));
    }
    
    // 确保是目录
    if (!is_dir($absPath)) {
        error_log("Not a directory: " . $absPath);
        sendJson(array('error' => 'Not a directory'));
    }
    
    $files = array();
    $dirContents = scandir($absPath);
    
    if ($dirContents === false) {
        error_log("Failed to scan directory: " . $absPath);
        sendJson(array('error' => 'Failed to read directory contents'));
    }
    
    foreach ($dirContents as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $itemPath = $absPath . '/' . $item;
        $isDir = is_dir($itemPath);
        
        if (!is_readable($itemPath)) {
            error_log("Item not readable: " . $itemPath);
            continue;
        }
        
        $size = $isDir ? null : filesize($itemPath);
        $relativePath = trim($path . '/' . $item, '/');
        if ($isDir) {
            $relativePath .= '/';
        }
        
        $files[] = array(
            'path' => '/' . $relativePath,
            'name' => $item,
            'size' => $size
        );
    }
    
    usort($files, function($a, $b) {
        $aIsDir = substr($a['path'], -1) === '/';
        $bIsDir = substr($b['path'], -1) === '/';
        
        if ($aIsDir && !$bIsDir) return -1;
        if (!$aIsDir && $bIsDir) return 1;
        
        return strcasecmp($a['name'], $b['name']);
    });
    
    sendJson($files);
    
} catch (Exception $e) {
    error_log("List directory failed: " . $e->getMessage());
    sendJson(array('error' => 'Internal server error'));
}