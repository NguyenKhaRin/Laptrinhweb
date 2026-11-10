<?php
/**
 * ========================================================
 * ORDER MODEL - QUẢN LÝ ĐƠN HÀNG & GIAO DỊCH
 * ========================================================
 */

namespace App\Models;

use App\Core\Model;
use DomainException;
use Throwable;

class Order extends Model {
    protected string $table = 'orders';

    /**
     * Tạo đơn và giữ chỗ sản phẩm trong cùng một transaction.
     */
    public function createForAvailableProduct(
        int $productId,
        int $buyerId,
        string $orderCode,
        string $shippingAddress,
        string $phone,
        ?string $notes
    ): int {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "SELECT `user_id`, `price`, `status` FROM `products` WHERE `id` = :id FOR UPDATE"
            );
            $stmt->execute(['id' => $productId]);
            $product = $stmt->fetch();

            if (!$product || $product['status'] !== 'available') {
                throw new DomainException('Sản phẩm này hiện không còn khả dụng để đặt mua.');
            }

            if ((int)$product['user_id'] === $buyerId) {
                throw new DomainException('Bạn không thể tự mua sản phẩm do chính mình đăng bán.');
            }

            $stmt = $this->db->prepare(
                "INSERT INTO `{$this->table}`
                    (`order_code`, `buyer_id`, `seller_id`, `product_id`, `total_price`,
                     `shipping_address`, `phone`, `notes`, `status`)
                 VALUES
                    (:order_code, :buyer_id, :seller_id, :product_id, :total_price,
                     :shipping_address, :phone, :notes, 'pending')"
            );
            $stmt->execute([
                'order_code' => $orderCode,
                'buyer_id' => $buyerId,
                'seller_id' => $product['user_id'],
                'product_id' => $productId,
                'total_price' => $product['price'],
                'shipping_address' => $shippingAddress,
                'phone' => $phone,
                'notes' => $notes
            ]);

            $orderId = (int)$this->db->lastInsertId();
            $stmt = $this->db->prepare(
                "UPDATE `products` SET `status` = 'reserved' WHERE `id` = :id AND `status` = 'available'"
            );
            $stmt->execute(['id' => $productId]);

            if ($stmt->rowCount() !== 1) {
                throw new DomainException('Sản phẩm vừa được người khác đặt mua. Vui lòng tải lại trang.');
            }

            $this->db->commit();
            return $orderId;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

    /**
     * Chỉ cho phép chuyển trạng thái phù hợp với vai trò và trạng thái hiện tại.
     */
    public function transitionStatus(int $orderId, int $userId, string $nextStatus): void {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "SELECT `buyer_id`, `seller_id`, `product_id`, `status`
                 FROM `{$this->table}` WHERE `id` = :id FOR UPDATE"
            );
            $stmt->execute(['id' => $orderId]);
            $order = $stmt->fetch();

            if (!$order) {
                throw new DomainException('Không tìm thấy đơn hàng.');
            }

            $isSeller = (int)$order['seller_id'] === $userId;
            $isBuyer = (int)$order['buyer_id'] === $userId;
            $allowedTransitions = $isSeller
                ? [
                    'pending' => ['confirmed', 'cancelled'],
                    'confirmed' => ['shipping', 'cancelled'],
                    'shipping' => ['completed']
                ]
                : ($isBuyer
                    ? ['pending' => ['cancelled'], 'confirmed' => ['cancelled']]
                    : []);

            if (!in_array($nextStatus, $allowedTransitions[$order['status']] ?? [], true)) {
                throw new DomainException('Bạn không thể thực hiện chuyển trạng thái này.');
            }

            $stmt = $this->db->prepare(
                "UPDATE `{$this->table}` SET `status` = :status
                 WHERE `id` = :id AND `status` = :current_status"
            );
            $stmt->execute([
                'status' => $nextStatus,
                'id' => $orderId,
                'current_status' => $order['status']
            ]);

            if ($nextStatus === 'completed' || $nextStatus === 'cancelled') {
                $productStatus = $nextStatus === 'completed' ? 'sold' : 'available';
                $stmt = $this->db->prepare(
                    "UPDATE `products` SET `status` = :product_status
                     WHERE `id` = :id AND `status` = 'reserved'"
                );
                $stmt->execute([
                    'product_status' => $productStatus,
                    'id' => $order['product_id']
                ]);

                if ($stmt->rowCount() !== 1) {
                    throw new DomainException('Trạng thái sản phẩm không còn phù hợp với đơn hàng.');
                }
            }

            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }

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
