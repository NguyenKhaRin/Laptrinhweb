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
use App\Models\Product;

class OrderController extends Controller {
    private Order $orderModel;
    private Product $productModel;

    public function __construct() {
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    /**
     * Tạo đơn đặt mua sản phẩm
     */
    public function store(): void {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['shipping_address'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        $product = $this->productModel->find($productId);

        if (!$product || $product['status'] !== 'available') {
            $this->flash('error', 'Sản phẩm này hiện không còn khả dụng để đặt mua.');
            $this->redirect('/');
        }

        if ($product['user_id'] == $user['id']) {
            $this->flash('error', 'Bạn không thể tự mua sản phẩm do chính mình đăng bán!');
            $this->redirect('/products/detail/' . $productId);
        }

        if (empty($phone) || empty($address)) {
            $this->flash('error', 'Vui lòng cung cấp số điện thoại và địa chỉ nhận hàng.');
            $this->redirect('/products/detail/' . $productId);
        }

        $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        $orderId = $this->orderModel->create([
            'order_code'       => $orderCode,
            'buyer_id'         => $user['id'],
            'seller_id'        => $product['user_id'],
            'product_id'       => $productId,
            'total_price'      => $product['price'],
            'shipping_address' => $address,
            'phone'            => $phone,
            'notes'            => $notes,
            'status'           => 'pending'
        ]);

        // Cập nhật trạng thái sản phẩm sang 'reserved' (đã có người đặt)
        $this->productModel->update($productId, ['status' => 'reserved']);

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

        $order = $this->orderModel->find($orderId);
        if (!$order) {
            $this->redirect('/');
        }

        // Kiểm tra quyền cập nhật
        if ($order['seller_id'] != $user['id'] && $order['buyer_id'] != $user['id']) {
            $this->flash('error', 'Bạn không có quyền cập nhật đơn này.');
            $this->redirect('/orders/detail/' . $orderId);
        }

        $this->orderModel->updateStatus($orderId, $status);

        // Nếu hoàn thành đơn hàng -> chuyển sản phẩm thành sold
        if ($status === 'completed') {
            $this->productModel->update($order['product_id'], ['status' => 'sold']);
        } elseif ($status === 'cancelled') {
            // Nếu hủy -> trả sản phẩm về available
            $this->productModel->update($order['product_id'], ['status' => 'available']);
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

