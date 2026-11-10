<?php
/**
 * ========================================================
 * ORDER CONTROLLER - TẠO ĐƠN & QUẢN LÝ GIAO DỊCH
 * Phụ trách bởi: Thành viên phụ trách Module Đơn hàng & Đánh giá
 * ========================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use DomainException;

class OrderController extends Controller {
    private Order $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    /**
     * Tạo đơn đặt mua sản phẩm
     */
    public function store(): void {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['shipping_address'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (!$productId || $productId <= 0 || $phone === '' || $address === ''
            || strlen($phone) > 20 || strlen($address) > 255) {
            $this->flash('error', 'Vui lòng cung cấp số điện thoại và địa chỉ nhận hàng.');
            $this->redirect($productId ? '/products/detail/' . $productId : '/');
        }

        $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
        try {
            $orderId = $this->orderModel->createForAvailableProduct(
                $productId,
                (int)$user['id'],
                $orderCode,
                $address,
                $phone,
                $notes !== '' ? $notes : null
            );
        } catch (DomainException $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/products/detail/' . $productId);
        }

        $this->flash('success', "Đặt hàng thành công! Mã đơn: {$orderCode}");
        $this->redirect('/orders/detail/' . $orderId);
    }

    /**
     * Danh sách đơn hàng tôi đã mua (Dành cho Người mua)
     */
    public function buyerOrders(): void {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $orders = $this->orderModel->getBuyerOrders($user['id']);

        $this->view('orders/buyer_orders', [
            'pageTitle' => 'Đơn hàng tôi đã mua',
            'orders'    => $orders,
            'success'   => $this->flash('success'),
            'error'     => $this->flash('error')
        ]);
    }

    /**
     * Danh sách đơn hàng khách đặt mua (Dành cho Người bán)
     */
    public function sellerOrders(): void {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $orders = $this->orderModel->getSellerOrders($user['id']);

        $this->view('orders/seller_orders', [
            'pageTitle' => 'Quản lý đơn bán hàng',
            'orders'    => $orders,
            'success'   => $this->flash('success'),
            'error'     => $this->flash('error')
        ]);
    }

    /**
     * Chi tiết đơn hàng
     */
    public function detail(int|string $id): void {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $id = (int)$id;
        $order = $this->orderModel->getDetail($id);

        if (!$order || ($order['buyer_id'] != $user['id'] && $order['seller_id'] != $user['id'] && ($user['role'] ?? '') !== 'admin')) {
            $this->flash('error', 'Bạn không có quyền xem đơn hàng này.');
            $this->redirect('/');
        }

        $this->view('orders/detail', [
            'pageTitle' => 'Chi tiết đơn hàng #' . $order['order_code'],
            'order'     => $order,
            'isBuyer'   => ($order['buyer_id'] == $user['id']),
            'isSeller'  => ($order['seller_id'] == $user['id']),
            'success'   => $this->flash('success'),
            'error'     => $this->flash('error')
        ]);
    }

    /**
     * Cập nhật trạng thái đơn hàng (Xử lý AJAX hoặc POST)
     */
    public function updateStatus(): void {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        $allowedStatuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            $this->flash('error', 'Trạng thái không hợp lệ.');
            $this->redirect('/orders/detail/' . $orderId);
        }

        try {
            $this->orderModel->transitionStatus($orderId, (int)$user['id'], $status);
        } catch (DomainException $exception) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->json(['success' => false, 'message' => $exception->getMessage()], 409);
                return;
            }
            $this->flash('error', $exception->getMessage());
            $this->redirect($orderId > 0 ? '/orders/detail/' . $orderId : '/orders/buyer');
        }

        // Hỗ trợ AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $this->json(['success' => true, 'status' => $status, 'message' => 'Cập nhật trạng thái thành công!']);
            return;
        }

        $this->flash('success', 'Cập nhật trạng thái đơn hàng thành công!');
        $this->redirect('/orders/detail/' . $orderId);
    }
}
