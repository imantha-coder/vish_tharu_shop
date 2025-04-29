<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'vish_tharu_shop');
define('DB_USER', 'root');
define('DB_PASS', '');

// API configuration
define('API_URL', 'http://localhost/vish-tharu-shop/api');
define('UPLOAD_DIR', '../uploads/products/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', array('image/jpeg', 'image/png', 'image/gif'));

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'VISH_THARU_SHOP_SESSION');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Kathmandu');
?> 