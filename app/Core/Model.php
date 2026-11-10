<?php
/**
 * ========================================================
 * BASE MODEL - LỚP CƠ SỞ CHO TOÀN BỘ MODEL (SQL PDO)
 * ========================================================
 */

namespace App\Core;

use App\Config\Database;
use PDO;

abstract class Model {
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Lấy toàn bộ bản ghi
     */
    public function all(string $orderBy = 'id DESC'): array {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    /**
     * Tìm 1 bản ghi theo ID khóa chính
     */
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Tìm các bản ghi theo 1 điều kiện
     */
    public function where(string $column, $value, string $orderBy = 'id DESC'): array {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = :value ORDER BY {$orderBy}");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll();
    }

    /**
     * Tìm bản ghi đầu tiên khớp điều kiện
     */
    public function findOneBy(string $column, $value): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = :value LIMIT 1");
        $stmt->execute(['value' => $value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Tạo mới bản ghi
     */
    public function create(array $data): int {
        $columns = implode('`, `', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO `{$this->table}` (`{$columns}`) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Cập nhật bản ghi theo ID
     */
    public function update(int $id, array $data): bool {
        $fields = '';
        foreach (array_keys($data) as $key) {
            $fields .= "`{$key}` = :{$key}, ";
        }
        $fields = rtrim($fields, ', ');

        $sql = "UPDATE `{$this->table}` SET {$fields} WHERE `{$this->primaryKey}` = :pk_id";
        $stmt = $this->db->prepare($sql);
        $data['pk_id'] = $id;

        return $stmt->execute($data);
    }

    /**
     * Xóa bản ghi theo ID
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Thực thi câu lệnh SQL tùy biến (kèm tham số)
     */
    public function query(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}

