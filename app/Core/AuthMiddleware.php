<?php
/**
 * ========================================================
 * AUTH MIDDLEWARE - KIỂM TRA ĐĂNG NHẬP VÀ PHÂN QUYỀN
 * ========================================================
 */

namespace App\Core;

class AuthMiddleware {
    /**
     * Kiểm tra trạng thái đăng nhập
     */
    public function handle(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user'])) {
            // Kiểm tra xem có phải request AJAX / API không
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                      || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

            if ($isAjax) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để thực hiện chức năng này.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $_SESSION['flash']['error'] = 'Vui lòng đăng nhập để tiếp tục!';
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}

