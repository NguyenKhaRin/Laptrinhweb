<?php
/**
 * ========================================================
 * REVIEW CONTROLLER - ĐÁNH GIÁ NGƯỜI BÁN & XEM PHẢN HỒI
 * Phụ trách bởi: Thành viên phụ trách Module Đơn hàng & Đánh giá
 * ========================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Review;
use App\Models\Order;
use App\Models\User;

class ReviewController extends Controller {
    private Review $reviewModel;
    private Order $orderModel;
    private User $userModel;

    public function __construct() {
        $this->reviewModel = new Review();
        $this->orderModel = new Order();
        $this->userModel = new User();
    }

    /**
     * Gửi đánh giá cho người bán sau khi đơn hàng hoàn thành
     */
    public function store(): void {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $orderId = filter_var($_POST['order_id'] ?? null, FILTER_VALIDATE_INT);
        $rating = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
        $comment = trim($_POST['comment'] ?? '');

        if (!$orderId || $orderId <= 0 || !$rating || $rating < 1 || $rating > 5 || strlen($comment) > 8000) {
            $this->flash('error', 'Đơn hàng, số sao hoặc nội dung đánh giá không hợp lệ.');
            $this->redirect('/orders/buyer');
        }

        $order = $this->orderModel->find($orderId);

        if (!$order || $order['buyer_id'] != $user['id']) {
            $this->flash('error', 'Bạn không có quyền đánh giá đơn hàng này.');
            $this->redirect('/orders/buyer');
        }

        if ($order['status'] !== 'completed') {
            $this->flash('error', 'Chỉ có thể đánh giá sau khi đơn hàng đã hoàn tất.');
            $this->redirect('/orders/detail/' . $orderId);
        }

        if ($this->reviewModel->isOrderReviewed($orderId)) {
            $this->flash('error', 'Đơn hàng này đã được đánh giá trước đó.');
            $this->redirect('/orders/detail/' . $orderId);
        }

        $this->reviewModel->create([
            'order_id'     => $orderId,
            'reviewer_id'  => $user['id'],
            'reviewee_id'  => $order['seller_id'],
            'rating'       => $rating,
            'comment'      => $comment
        ]);

        // Tính toán lại điểm trung bình cho người bán
        $this->userModel->recalculateRating($order['seller_id']);

        $this->flash('success', 'Cảm ơn bạn đã gửi đánh giá cho người bán!');
        $this->redirect('/orders/detail/' . $orderId);
    }

    /**
     * Xem danh sách đánh giá của 1 người bán
     */
    public function sellerReviews(int|string $sellerId): void {
        $sellerId = (int)$sellerId;
        $seller = $this->userModel->find($sellerId);

        if (!$seller) {
            $this->redirect('/');
        }

        $reviews = $this->reviewModel->getReviewsForSeller($sellerId);

        $this->view('profile/reviews', [
            'pageTitle' => 'Đánh giá uy tín: ' . $seller['full_name'],
            'seller'    => $seller,
            'reviews'   => $reviews
        ]);
    }
}
