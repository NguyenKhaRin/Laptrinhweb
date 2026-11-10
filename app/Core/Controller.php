<?php
/**
 * ========================================================
 * BASE CONTROLLER - LỚP CƠ SỞ CHO TOÀN BỘ CONTROLLER
 * ========================================================
 */

namespace App\Core;

class Controller {
    /**
     * Render giao diện View kèm dữ liệu và Layout
     */
    protected function view(string $viewPath, array $data = [], bool $withLayout = true): void {
        // Biến các key của mảng $data thành biến độc lập
        extract($data);

        $viewFile = APP_PATH . '/Views/' . ltrim($viewPath, '/') . '.php';

        if (!file_exists($viewFile)) {
            die("Lỗi View: Không tìm thấy file view tại [{$viewFile}]");
        }

        if ($withLayout) {
            require_once APP_PATH . '/Views/layouts/header.php';
            require_once APP_PATH . '/Views/layouts/navbar.php';
            require_once $viewFile;
            require_once APP_PATH . '/Views/layouts/footer.php';
        } else {
            require_once $viewFile;
        }
    }

    /**
     * Trả về kết quả JSON (dùng cho AJAX / Fetch API)
     */
    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Chuyển hướng trang (Redirect)
     */
    protected function redirect(string $path): void {
        $url = str_starts_with($path, 'http') ? $path : BASE_URL . '/' . ltrim($path, '/');
        header("Location: {$url}");
        exit;
    }

    /**
     * Lấy thông tin tài khoản đang đăng nhập từ Session
     */
    protected function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Thiết lập hoặc lấy Flash Message (thông báo 1 lần)
     */
    protected function flash(string $key, ?string $message = null) {
        if ($message !== null) {
            $_SESSION['flash'][$key] = $message;
            return null;
        }

        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
}

