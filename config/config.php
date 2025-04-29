<?php
// Database configuration
define('DB_HOST', 'sql307.byethost7.com');
define('DB_NAME', 'b7_38858058_shop');
define('DB_USER', 'b7_38858058');
define('DB_PASS', 'imantha123');

// API configuration
define('API_URL', 'http://www.vishtharushop.byethost7.com/api');  // Fixed double slash
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/uploads/products/');  // Absolute path
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', array('image/jpeg', 'image/png', 'image/gif'));

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'VISH_THARU_SHOP_SESSION');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/php_errors.log');

// Timezone
date_default_timezone_set('Asia/Kathmandu');
?>
