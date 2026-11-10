<?php
/**
 * ========================================================
 * ADMIN CONTROLLER - TRANG QUẢN TRỊ VIÊN HỆ THỐNG
 * ========================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Config\Database;

class AdminController extends Controller {
    private \PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Bảng điều khiển tổng quan (Dashboard)
     */
    public function dashboard(): void {
        // Thống kê số liệu
        $totalUsers = $this->db->query("SELECT COUNT(id) FROM `users`")->fetchColumn();
        $totalProducts = $this->db->query("SELECT COUNT(id) FROM `products`")->fetchColumn();
        $totalOrders = $this->db->query("SELECT COUNT(id) FROM `orders`")->fetchColumn();
        $totalRevenue = $this->db->query("SELECT COALESCE(SUM(total_price), 0) FROM `orders` WHERE `status` = 'completed'")->fetchColumn();

        // 5 đơn hàng mới nhất
        $recentOrders = $this->db->query("SELECT o.*, p.title as product_title, u.full_name as buyer_name 
                                          FROM `orders` o 
                                          JOIN `products` p ON o.product_id = p.id 
                                          JOIN `users` u ON o.buyer_id = u.id 
                                          ORDER BY o.id DESC LIMIT 5")->fetchAll();

        // 5 bài đăng đồ cũ mới nhất
        $recentProducts = $this->db->query("SELECT p.*, u.full_name as seller_name, c.name as category_name 
                                            FROM `products` p 
                                            JOIN `users` u ON p.user_id = u.id 
                                            JOIN `categories` c ON p.category_id = c.id 
                                            ORDER BY p.id DESC LIMIT 5")->fetchAll();

        $this->view('admin/dashboard', [
            'pageTitle'      => 'Bảng Điều Khiển Quản Trị',
            'totalUsers'     => $totalUsers,
            'totalProducts'  => $totalProducts,
            'totalOrders'    => $totalOrders,
            'totalRevenue'   => $totalRevenue,
            'recentOrders'   => $recentOrders,
            'recentProducts' => $recentProducts
        ]);
    }

    /**
     * Quản lý danh sách người dùng
     */
    public function users(): void {
        $users = $this->db->query("SELECT * FROM `users` ORDER BY `id` DESC")->fetchAll();

        $this->view('admin/users', [
            'pageTitle' => 'Quản lý Người Dùng',
            'users'     => $users,
            'success'   => $this->flash('success'),
            'error'     => $this->flash('error')
        ]);
    }

    /**
     * Cập nhật vai trò / phân quyền người dùng (buyer / seller / admin)
     */
    public function updateRole(): void {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newRole = $_POST['role'] ?? 'buyer';

        if (!in_array($newRole, ['buyer', 'seller', 'admin'])) {
            $this->flash('error', 'Vai trò không hợp lệ.');
            $this->redirect('/admin/users');
        }

        $stmt = $this->db->prepare("UPDATE `users` SET `role` = :role WHERE `id` = :id");
        $stmt->execute(['role' => $newRole, 'id' => $userId]);

        $this->flash('success', "Đã cập nhật phân quyền người dùng #{$userId} thành [{$newRole}].");
        $this->redirect('/admin/users');
    }

    /**
     * Quản lý danh sách bài đăng sản phẩm
     */
    public function products(): void {
        $sql = "SELECT p.*, u.full_name as seller_name, c.name as category_name 
                FROM `products` p 
                JOIN `users` u ON p.user_id = u.id 
                JOIN `categories` c ON p.category_id = c.id 
                ORDER BY p.id DESC";
        $products = $this->db->query($sql)->fetchAll();

        $this->view('admin/products', [
            'pageTitle' => 'Quản lý Bài Đăng Đồ Cũ',
            'products'  => $products,
            'success'   => $this->flash('success'),
            'error'     => $this->flash('error')
        ]);
    }

    /**
     * Xóa sản phẩm vi phạm (Admin xóa)
     */
    public function deleteProduct(int|string $id): void {
        $id = (int)$id;
        $stmt = $this->db->prepare("DELETE FROM `products` WHERE `id` = :id");
        $stmt->execute(['id' => $id]);

        $this->flash('success', "Đã gỡ bài đăng vi phạm #{$id} thành công!");
        $this->redirect('/admin/products');
    }
}
