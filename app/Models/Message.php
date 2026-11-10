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
                WHERE ((m.sender_id = :sender_one AND m.receiver_id = :receiver_one)
                   OR  (m.sender_id = :sender_two AND m.receiver_id = :receiver_two))";
        
        $params = [
            'sender_one' => $user1Id,
            'receiver_one' => $user2Id,
            'sender_two' => $user2Id,
            'receiver_two' => $user1Id
        ];

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
        $sql = "SELECT latest.partner_id,
                       u.full_name AS partner_name,
                       u.avatar AS partner_avatar,
                       p.id AS product_id,
                       p.title AS product_title,
                       p.image AS product_image,
                       p.price AS product_price,
                       m.message AS last_message,
                       m.created_at AS last_time,
                       (SELECT COUNT(*)
                        FROM `{$this->table}` unread
                        WHERE unread.sender_id = latest.partner_id
                          AND unread.receiver_id = :uid_unread
                          AND unread.is_read = 0
                          AND unread.product_id <=> latest.product_id) AS unread_count
                FROM (
                    SELECT CASE WHEN sender_id = :uid_case THEN receiver_id ELSE sender_id END AS partner_id,
                           product_id,
                           MAX(id) AS last_message_id
                    FROM `{$this->table}`
                    WHERE sender_id = :uid_sender OR receiver_id = :uid_receiver
                    GROUP BY partner_id, product_id
                ) latest
                JOIN `{$this->table}` m ON m.id = latest.last_message_id
                JOIN `users` u ON u.id = latest.partner_id
                LEFT JOIN `products` p ON p.id = latest.product_id
                ORDER BY m.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'uid_unread' => $userId,
            'uid_case' => $userId,
            'uid_sender' => $userId,
            'uid_receiver' => $userId
        ]);
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
