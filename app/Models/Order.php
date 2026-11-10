<?php
/**
 * ========================================================
 * ORDER MODEL - QUẢN LÝ ĐƠN HÀNG & GIAO DỊCH
 * ========================================================
 */

namespace App\Models;

use App\Core\Model;

class Order extends Model {
    protected string $table = 'orders';

    /**
     * Lấy danh sách đơn hàng đã mua của người mua
     */
    public function getBuyerOrders(int $buyerId): array {
        $sql = "SELECT o.*, p.title as product_title, p.image as product_image, u.full_name as seller_name, u.phone as seller_phone,
                       r.id as review_id, r.rating as review_rating
                FROM `{$this->table}` o
                JOIN `products` p ON o.product_id = p.id
                JOIN `users` u ON o.seller_id = u.id
                LEFT JOIN `reviews` r ON r.order_id = o.id
                WHERE o.buyer_id = :buyer_id
                ORDER BY o.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['buyer_id' => $buyerId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách đơn hàng bán của người bán
     */
    public function getSellerOrders(int $sellerId): array {
        $sql = "SELECT o.*, p.title as product_title, p.image as product_image, u.full_name as buyer_name, u.phone as buyer_phone 
                FROM `{$this->table}` o
                JOIN `products` p ON o.product_id = p.id
                JOIN `users` u ON o.buyer_id = u.id
                WHERE o.seller_id = :seller_id
                ORDER BY o.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['seller_id' => $sellerId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết đơn hàng đầy đủ
     */
    public function getDetail(int $orderId): ?array {
        $sql = "SELECT o.*, 
                       p.title as product_title, p.image as product_image, p.price as product_price, p.condition as product_condition,
                       buyer.full_name as buyer_name, buyer.phone as buyer_phone, buyer.email as buyer_email,
                       seller.full_name as seller_name, seller.phone as seller_phone, seller.email as seller_email,
                       r.rating as review_rating, r.comment as review_comment
                FROM `{$this->table}` o
                JOIN `products` p ON o.product_id = p.id
                JOIN `users` buyer ON o.buyer_id = buyer.id
                JOIN `users` seller ON o.seller_id = seller.id
                LEFT JOIN `reviews` r ON r.order_id = o.id
                WHERE o.id = :order_id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Cập nhật trạng thái đơn hàng (pending, confirmed, shipping, completed, cancelled)
     */
    public function updateStatus(int $orderId, string $status): bool {
        return $this->update($orderId, ['status' => $status]);
    }
}

