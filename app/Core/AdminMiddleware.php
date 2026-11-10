<?php
/**
 * ========================================================
 * ADMIN MIDDLEWARE - CHẶN TRUY CẬP DÀNH RIÊNG CHO QUẢN TRỊ VIÊN
 * ========================================================
 */

namespace App\Core;

class AdminMiddleware {
    /**
     * Kiểm tra quyền Admin
     */
    public function handle(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Kiểm tra đã đăng nhập chưa
        if (empty($_SESSION['user'])) {
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                      || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

            if ($isAjax) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Yêu cầu đăng nhập'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $_SESSION['flash']['error'] = 'Vui lòng đăng nhập với tài khoản Quản trị viên!';
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/admin';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // 2. Kiểm tra vai trò có phải admin không
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            $_SESSION['flash']['error'] = 'Truy cập bị từ chối! Bạn không có quyền quản trị viên.';
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    }
}
