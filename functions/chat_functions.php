<?php
// File: functions/chat_functions.php
// Kelas untuk mengelola semua logika terkait chat.

class Chat {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Mengambil detail booking untuk verifikasi chat.
     * Memastikan pengguna yang mengakses adalah bagian dari konsultasi yang valid.
     * @param int $bookingId ID booking konsultasi.
     * @param int $currentUserId ID pengguna yang sedang login.
     * @return array|false Detail booking jika valid, atau false.
     */
    public function getBookingDetailsForChat($bookingId, $currentUserId) {
        $stmt = $this->conn->prepare("
            SELECT 
                cb.id as consultation_id, 
                cb.user_id as client_id, 
                cb.psychologist_id,
                client.full_name as client_name,
                psychologist.full_name as psychologist_name
            FROM consultation_bookings cb
            JOIN users client ON cb.user_id = client.id
            JOIN users psychologist ON cb.psychologist_id = psychologist.id
            WHERE cb.id = ? AND (cb.user_id = ? OR cb.psychologist_id = ?) AND cb.status = 'confirmed'
        ");
        $stmt->execute([$bookingId, $currentUserId, $currentUserId]);
        return $stmt->fetch();
    }

    /**
     * Mengambil riwayat pesan dari sebuah sesi konsultasi.
     * @param int $consultationId ID booking konsultasi.
     * @return array Daftar pesan.
     */
    public function getMessagesByConsultationId($consultationId) {
        $stmt = $this->conn->prepare("
            SELECT sender_id, message_text, sent_at 
            FROM messages 
            WHERE consultation_id = ? 
            ORDER BY sent_at ASC
        ");
        $stmt->execute([$consultationId]);
        return $stmt->fetchAll();
    }

    /**
     * Menyimpan pesan baru ke database.
     * @param int $consultationId
     * @param int $senderId
     * @param int $receiverId
     * @param string $messageText
     * @return bool True jika berhasil, false jika gagal.
     */
    public function saveMessage($consultationId, $senderId, $receiverId, $messageText) {
        $stmt = $this->conn->prepare(
            "INSERT INTO messages (consultation_id, sender_id, receiver_id, message_text) VALUES (?, ?, ?, ?)"
        );
        try {
            return $stmt->execute([$consultationId, $senderId, $receiverId, $messageText]);
        } catch (PDOException $e) {
            error_log("Gagal menyimpan pesan: " . $e->getMessage());
            return false;
        }
    }
}
?>
