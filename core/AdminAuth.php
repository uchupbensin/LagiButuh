<?php
// File: core/AdminAuth.php
// Kelas khusus untuk menangani otentikasi admin.

class AdminAuth {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Memproses login untuk admin.
     * @param string $email
     * @param string $password
     * @return bool True jika login berhasil, false jika gagal.
     */
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Set session khusus untuk admin
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'] ?? $admin['username'];
            return true;
        }
        return false;
    }

    /**
     * Memeriksa apakah admin sudah login.
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }

    /**
     * Memproses logout admin.
     */
    public function logout() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        // session_destroy() bisa digunakan jika tidak ada session lain yang perlu dipertahankan.
    }

    /**
     * Mengamankan halaman agar hanya bisa diakses oleh admin yang sudah login.
     */
    public function securePage() {
        if (!$this->isLoggedIn()) {
            redirect(BASE_URL . '/admin/login.php');
        }
    }
}
?>
