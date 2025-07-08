<?php
// File: core/Auth.php
// Kelas untuk menangani otentikasi pengguna

class Auth {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function register($username, $email, $password) {
        if ($this->isUserExists($username, $email)) {
            return "Username atau email sudah terdaftar.";
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        try {
            $stmt->execute([$username, $email, $hashedPassword]);
            return true;
        } catch (PDOException $e) {
            return "Registrasi gagal: " . $e->getMessage();
        }
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function getUserRole() {
        return $_SESSION['role'] ?? null;
    }
    
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, username, email, full_name, profile_picture, phone_number, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    private function isUserExists($username, $email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch() !== false;
    }

    public function updateUserProfile($userId, $data) {
        // Ambil data saat ini untuk perbandingan
        $currentUser = $this->getUserById($userId);

        // Bangun query secara dinamis
        $queryParts = [];
        $params = [];

        // Update Full Name
        if (!empty($data['full_name']) && $data['full_name'] !== $currentUser['full_name']) {
            $queryParts[] = "full_name = :full_name";
            $params[':full_name'] = $data['full_name'];
        }

        // Update Phone Number
        if (!empty($data['phone_number']) && $data['phone_number'] !== $currentUser['phone_number']) {
            $queryParts[] = "phone_number = :phone_number";
            $params[':phone_number'] = $data['phone_number'];
        }

        // Update Profile Picture
        if (!empty($data['profile_picture'])) {
            $queryParts[] = "profile_picture = :profile_picture";
            $params[':profile_picture'] = $data['profile_picture'];
        }
        
        // Update Password
        if (!empty($data['new_password'])) {
            if (empty($data['current_password']) || !password_verify($data['current_password'], $currentUser['password'])) {
                return "Password saat ini salah.";
            }
            if (strlen($data['new_password']) < 6) {
                return "Password baru minimal harus 6 karakter.";
            }
            $queryParts[] = "password = :password";
            $params[':password'] = password_hash($data['new_password'], PASSWORD_BCRYPT);
        }

        if (empty($queryParts)) {
            return "Tidak ada data yang diubah.";
        }

        $query = "UPDATE users SET " . implode(', ', $queryParts) . " WHERE id = :id";
        $params[':id'] = $userId;

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            error_log("Update Profile Error: " . $e->getMessage());
            return "Gagal memperbarui profil. Silakan coba lagi.";
        }
    }
}
?>
