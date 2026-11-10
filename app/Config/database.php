<?php
/**
 * ========================================================
 * KẾT NỐI PDO TỚI MYSQL DATABASE
 * ========================================================
 */

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    /**
     * Khởi tạo và trả về kết nối PDO duy nhất (Singleton Pattern)
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $dbName = env('DB_NAME', 'secondhand_marketplace');
            $user = env('DB_USER', 'root');
            $pass = env('DB_PASS', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Nếu chưa tạo database hoặc sai thông tin đăng nhập
                if (env('APP_DEBUG', true)) {
                    die("<div style='font-family: Arial, sans-serif; padding: 20px; background: #fff2f0; border: 1px solid #ffccc7; border-radius: 8px; max-width: 600px; margin: 40px auto; color: #cf1322;'>"
                        . "<h3 style='margin-top: 0;'>⚠️ Lỗi kết nối Cơ sở dữ liệu:</h3>"
                        . "<p>" . htmlspecialchars($e->getMessage()) . "</p>"
                        . "<hr style='border: none; border-top: 1px solid #ffccc7;'/>"
                        . "<p style='color: #434343; font-size: 14px;'>Gợi ý: Hãy kiểm tra file <code>.env</code> và đảm bảo bạn đã tạo database <code>secondhand_marketplace</code> trong MySQL (XAMPP/Laragon).</p>"
                        . "</div>");
                } else {
                    die("Lỗi hệ thống! Vui lòng liên hệ quản trị viên.");
                }
            }
        }

        return self::$instance;
    }
}

