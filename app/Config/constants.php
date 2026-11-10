<?php
/**
 * ========================================================
 * HẰNG SỐ CẤU HÌNH HỆ THỐNG (SYSTEM CONSTANTS)
 * ========================================================
 */

// Định nghĩa đường dẫn thư mục gốc
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('PUBLIC_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'public');
define('UPLOAD_PATH', PUBLIC_PATH . DIRECTORY_SEPARATOR . 'uploads');

// Tự động load file .env nếu tồn tại
$envFile = ROOT_PATH . DIRECTORY_SEPARATOR . '.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Loại bỏ dấu nháy kép hoặc đơn ở hai đầu nếu có
            $value = trim($value, "\"'");
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

// Hàm lấy biến môi trường có giá trị mặc định
if (!function_exists('env')) {
    function env(string $key, $default = null) {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

// Cấu hình Base URL
$appUrl = env('APP_URL');
if (!$appUrl) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $appUrl = rtrim($protocol . $host . $scriptDir, '/');
}
define('BASE_URL', rtrim($appUrl, '/'));
define('ASSET_URL', BASE_URL . '/assets');
define('UPLOAD_URL', BASE_URL . '/uploads');

// Tên ứng dụng
define('APP_NAME', env('APP_NAME', 'Secondhand Marketplace'));

