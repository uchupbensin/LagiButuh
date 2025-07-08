<?php
// File: config/database.php
// Menginisialisasi koneksi database menggunakan PDO (Singleton Pattern).

class Database {
    private static $instance = null;
    private $conn;

    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $name = DB_NAME;

    private function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->name . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
            
            // --- PERBAIKAN: Gunakan format offset UTC untuk kompatibilitas maksimal ---
            // Ini akan memastikan fungsi NOW() di MySQL menggunakan zona waktu yang benar (WIB/UTC+7).
            $this->conn->exec("SET time_zone = '+07:00'");
            // -------------------------------------------------------------------------

        } catch (PDOException $e) {
            die('Koneksi Database Gagal: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
