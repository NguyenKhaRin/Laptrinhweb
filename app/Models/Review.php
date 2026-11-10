<?php
/**
 * ========================================================
 * REVIEW MODEL - QUẢN LÝ ĐÁNH GIÁ & PHẢN HỒI NGƯỜI BÁN
 * ========================================================
 */

namespace App\Models;

use App\Core\Model;

class Review extends Model {
    protected string $table = 'reviews';

    /**
     * Lấy toàn bộ đánh giá của 1 người bán kèm thông tin người đánh giá
     */
    public function getReviewsForSeller(int $sellerId): array {
        $sql = "SELECT r.*, u.full_name as reviewer_name, u.avatar as reviewer_avatar, p.title as product_title 
                FROM `{$this->table}` r
                JOIN `users` u ON r.reviewer_id = u.id
                JOIN `orders` o ON r.order_id = o.id
                JOIN `products` p ON o.product_id = p.id
                WHERE r.reviewee_id = :seller_id
                ORDER BY r.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['seller_id' => $sellerId]);
        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra đơn hàng này đã được đánh giá chưa
     */
    public function isOrderReviewed(int $orderId): bool {
        $sql = "SELECT id FROM `{$this->table}` WHERE `order_id` = :order_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
        return (bool) $stmt->fetch();
    }
}

