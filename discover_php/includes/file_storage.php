<?php
require_once __DIR__ . '/../config.php';

/**
 * Simple file-based persistence, mirrored from Energy implementation
 */
function saveUserData($filename, $data) {
    $dir = DATA_STORAGE_PATH;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $filepath = $dir . '/' . $filename;
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] " . json_encode($data) . "\n";
    return file_put_contents($filepath, $entry, FILE_APPEND | LOCK_EX);
}
