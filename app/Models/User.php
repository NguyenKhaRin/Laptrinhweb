<?php
/**
 * ========================================================
 * USER MODEL - QUẢN LÝ THÔNG TIN NGƯỜI DÙNG & TÀI KHOẢN
 * ========================================================
 */

namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected string $table = 'users';

    /**
     * Tìm người dùng theo Email
     */
    public function findByEmail(string $email): ?array {
        return $this->findOneBy('email', $email);
    }

    /**
     * Cập nhật điểm đánh giá trung bình của người bán
     */
    public function recalculateRating(int $userId): void {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews 
                FROM `reviews` 
                WHERE `reviewee_id` = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $data = $stmt->fetch();

        if ($data) {
            $avg = round((float)($data['avg_rating'] ?? 5.0), 1);
            $count = (int)($data['total_reviews'] ?? 0);

            $this->update($userId, [
                'rating_avg' => $avg,
                'rating_count' => $count
            ]);
        }
    }
}

