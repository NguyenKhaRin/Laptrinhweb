<?php
/**
 * ========================================================
 * PRODUCT MODEL - QUẢN LÝ SẢN PHẨM & TÌM KIẾM
 * ========================================================
 */

namespace App\Models;

use App\Core\Model;

class Product extends Model {
    protected string $table = 'products';

    /**
     * Lấy danh sách sản phẩm còn hàng kèm thông tin người bán & danh mục
     */
    public function getAvailable(int $limit = 20, int $offset = 0): array {
        $sql = "SELECT p.*, u.full_name as seller_name, u.avatar as seller_avatar, u.rating_avg as seller_rating, c.name as category_name 
                FROM `{$this->table}` p
                JOIN `users` u ON p.user_id = u.id
                JOIN `categories` c ON p.category_id = c.id
                WHERE p.status = 'available'
                ORDER BY p.id DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tìm kiếm và lọc sản phẩm theo nhiều tiêu chí
     */
    public function filter(array $filters = []): array {
        $sql = "SELECT p.*, u.full_name as seller_name, u.avatar as seller_avatar, u.rating_avg as seller_rating, c.name as category_name 
                FROM `{$this->table}` p
                JOIN `users` u ON p.user_id = u.id
                JOIN `categories` c ON p.category_id = c.id
                WHERE p.status = 'available'";

        $params = [];

        // Lọc theo từ khóa tìm kiếm (tiêu đề hoặc mô tả)
        if (!empty($filters['keyword'])) {
            $sql .= " AND (p.title LIKE :keyword OR p.description LIKE :keyword_desc)";
            $params['keyword'] = '%' . $filters['keyword'] . '%';
            $params['keyword_desc'] = '%' . $filters['keyword'] . '%';
        }

        // Lọc theo danh mục
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }

        // Lọc theo khoảng giá
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params['min_price'] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params['max_price'] = (float)$filters['max_price'];
        }

        // Lọc theo tình trạng (new, like_new, good, fair)
        if (!empty($filters['condition'])) {
            $sql .= " AND p.condition = :condition";
            $params['condition'] = $filters['condition'];
        }

        // Sắp xếp
        $sort = $filters['sort'] ?? 'newest';
        $orderBy = match ($sort) {
            'price_asc' => "p.price ASC",
            'price_desc' => "p.price DESC",
            'popular' => "p.view_count DESC",
            default => "p.id DESC"
        };

        $sql .= " ORDER BY {$orderBy}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết sản phẩm kèm thông tin chi tiết người bán
     */
    public function getDetail(int $id): ?array {
        $sql = "SELECT p.*, u.full_name as seller_name, u.email as seller_email, u.phone as seller_phone, 
                       u.avatar as seller_avatar, u.rating_avg as seller_rating, u.rating_count as seller_reviews_count,
                       c.name as category_name
                FROM `{$this->table}` p
                JOIN `users` u ON p.user_id = u.id
                JOIN `categories` c ON p.category_id = c.id
                WHERE p.id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Tăng số lượt xem của sản phẩm
     */
    public function incrementViews(int $id): void {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET `view_count` = `view_count` + 1 WHERE `id` = :id");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Lấy các sản phẩm do 1 người bán đăng
     */
    public function getBySeller(int $sellerId): array {
        $sql = "SELECT p.*, c.name as category_name 
                FROM `{$this->table}` p
                JOIN `categories` c ON p.category_id = c.id
                WHERE p.user_id = :seller_id
                ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['seller_id' => $sellerId]);
        return $stmt->fetchAll();
    }
}

