<?php
// Lokasi: core/database.php

class Database {
    private static $instance = null;
    private $conn;

    private $host;
    private $db_name;
    private $username;
    private $password;

    // Constructor dibuat private agar tidak bisa dibuat objek baru dari luar
    private function __construct() {
        // Mengambil konfigurasi dari konstanta
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Hentikan aplikasi jika koneksi gagal
            die('Koneksi Database Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Metode statis untuk mendapatkan satu-satunya instance dari kelas Database.
     * @return Database
     */
    public static function getInstance(): Database {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Mengembalikan objek koneksi PDO yang aktif.
     * @return PDO
     */
    public function getConnection(): PDO {
        return $this->conn;
    }
}
?>