<?php
// File: admin/AdminService.php
// Kelas untuk menyediakan data yang dibutuhkan oleh Admin Panel.

class AdminService {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Mengambil statistik ringkas untuk dasbor.
     * @return array
     */
    public function getDashboardStats() {
        $stats = [];
        $tables = [
            'total_users' => 'users',
            'total_bookings' => 'consultation_bookings',
            'total_rides' => 'nebeng_rides',
            'total_laptops' => 'laptops'
        ];

        foreach ($tables as $key => $table) {
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM {$table}");
            $stats[$key] = $stmt->fetchColumn();
        }
        return $stats;
    }

    /**
     * Mengambil semua pengguna dengan paginasi.
     * @param int $limit Jumlah data per halaman.
     * @param int $offset Mulai dari data ke berapa.
     * @return array Daftar pengguna.
     */
    public function getAllUsers($limit = 10, $offset = 0) {
        $stmt = $this->conn->prepare("
            SELECT id, full_name, username, email, role, created_at 
            FROM users 
            ORDER BY id DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Menghitung total jumlah pengguna.
     * @return int
     */
    public function countAllUsers() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM users");
        return (int)$stmt->fetchColumn();
    }
}
?>
