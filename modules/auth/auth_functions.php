<?php
// Lokasi: functions/auth_functions.php

/**
 * Memverifikasi kredensial pengguna dan membuat sesi login.
 * @param PDO $db Objek koneksi database PDO.
 * @param string $email Email pengguna.
 * @param string $password Password pengguna.
 * @return array Hasil login ['success' => bool, 'message' => string].
 */
function loginUser(PDO $db, string $email, string $password): array
{
    // Validasi input dasar
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email dan password tidak boleh kosong.'];
    }

    try {
        // Cari pengguna berdasarkan email
        $stmt = $db->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Jika pengguna tidak ditemukan atau password tidak cocok
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Email atau password salah.'];
        }

        // Jika berhasil, regenerasi session ID untuk keamanan
        session_regenerate_id(true);

        // Simpan informasi pengguna ke dalam sesi
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_logged_in'] = true;

        return ['success' => true, 'message' => 'Login berhasil!'];

    } catch (PDOException $e) {
        // Catat error database ke log server (jangan tampilkan ke pengguna)
        error_log("Login Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.'];
    }
}
?>