<?php
/**
 * ========================================================
 * MESSAGE MODEL - QUẢN LÝ TIN NHẮN CHAT REAL-TIME
 * ========================================================
 */

namespace App\Models;

use App\Core\Model;

class Message extends Model {
    protected string $table = 'messages';

    /**
     * Lấy lịch sử hội thoại giữa 2 người dùng (kèm thông tin sản phẩm nếu có)
     */
    public function getConversation(int $user1Id, int $user2Id, ?int $productId = null): array {
        $sql = "SELECT m.*, u.full_name as sender_name, u.avatar as sender_avatar 
                FROM `{$this->table}` m
                JOIN `users` u ON m.sender_id = u.id
                WHERE ((m.sender_id = :u1 AND m.receiver_id = :u2) 
                   OR  (m.sender_id = :u2 AND m.receiver_id = :u1))";
        
        $params = ['u1' => $user1Id, 'u2' => $user2Id];

        if ($productId) {
            $sql .= " AND m.product_id = :product_id";
            $params['product_id'] = $productId;
        }

        $sql .= " ORDER BY m.created_at ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách các cuộc trò chuyện gần đây của 1 user
     */
    public function getRecentConversations(int $userId): array {
        $sql = "SELECT 
                    CASE 
                        WHEN m.sender_id = :uid THEN m.receiver_id 
                        ELSE m.sender_id 
                    END AS partner_id,
                    u.full_name AS partner_name,
                    u.avatar AS partner_avatar,
                    p.id AS product_id,
                    p.title AS product_title,
                    p.image AS product_image,
                    p.price AS product_price,
                    m.message AS last_message,
                    m.created_at AS last_time,
                    SUM(CASE WHEN m.receiver_id = :uid AND m.is_read = 0 THEN 1 ELSE 0 END) AS unread_count
                FROM `{$this->table}` m
                JOIN `users` u ON u.id = (CASE WHEN m.sender_id = :uid THEN m.receiver_id ELSE m.sender_id END)
                LEFT JOIN `products` p ON m.product_id = p.id
                WHERE m.sender_id = :uid OR m.receiver_id = :uid
                GROUP BY partner_id, product_id
                ORDER BY last_time DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Đánh dấu toàn bộ tin nhắn từ người gửi là đã đọc
     */
    public function markAsRead(int $senderId, int $receiverId, ?int $productId = null): bool {
        $sql = "UPDATE `{$this->table}` 
                SET `is_read` = 1 
                WHERE `sender_id` = :sender_id AND `receiver_id` = :receiver_id AND `is_read` = 0";
        $params = ['sender_id' => $senderId, 'receiver_id' => $receiverId];

        if ($productId) {
            $sql .= " AND `product_id` = :product_id";
            $params['product_id'] = $productId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}

