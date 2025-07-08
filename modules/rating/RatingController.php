<?php
class RatingController {
    private $db;
    private $auth;

    public function __construct() {
        $this->db = new Database();
        $this->auth = new Auth();
    }

    public function submitRating($serviceType, $serviceId, $targetId, $rating, $review) {
        $user = $this->auth->getUser();
        if (!$user) {
            return ['success' => false, 'message' => 'Anda harus login'];
        }

        // Validasi rating
        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Rating harus antara 1-5'];
        }

        // Cek apakah user sudah memberikan rating untuk layanan ini
        $existingRating = $this->db->query(
            "SELECT id FROM ratings 
            WHERE service_type = ? AND service_id = ? AND reviewer_id = ?",
            [$serviceType, $serviceId, $user['id']]
        )->fetch();

        if ($existingRating) {
            return ['success' => false, 'message' => 'Anda sudah memberikan rating untuk layanan ini'];
        }

        // Simpan rating
        $this->db->query(
            "INSERT INTO ratings 
            (service_type, service_id, reviewer_id, target_id, rating, review) 
            VALUES (?, ?, ?, ?, ?, ?)",
            [$serviceType, $serviceId, $user['id'], $targetId, $rating, $review]
        );

        // Update rating summary
        $this->updateUserRatingSummary($targetId);

        return ['success' => true, 'message' => 'Terima kasih atas rating Anda!'];
    }

    private function updateUserRatingSummary($userId) {
        // Hitung ulang rating
        $stats = $this->db->query(
            "SELECT COUNT(*) as total, AVG(rating) as average 
            FROM ratings 
            WHERE target_id = ?",
            [$userId]
        )->fetch(PDO::FETCH_ASSOC);

        // Update atau insert summary
        $this->db->query(
            "INSERT INTO user_rating_summary (user_id, total_ratings, average_rating) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            total_ratings = VALUES(total_ratings), 
            average_rating = VALUES(average_rating)",
            [$userId, $stats['total'], round($stats['average'], 2)]
        );

        // Update rating di tabel users untuk kemudahan query
        $this->db->query(
            "UPDATE users SET rating = ? WHERE id = ?",
            [round($stats['average'], 2), $userId]
        );
    }

    public function getRatingsForUser($userId) {
        return $this->db->query(
            "SELECT r.*, u.name as reviewer_name, u.profile_picture as reviewer_photo,
            CASE r.service_type
                WHEN 'nebeng' THEN CONCAT('Nebeng #', r.service_id)
                WHEN 'print' THEN CONCAT('Print #', r.service_id)
                WHEN 'psychologist' THEN 'Sesi Psikolog'
                WHEN 'laptop' THEN CONCAT('Pinjam Laptop #', r.service_id)
                WHEN 'food' THEN CONCAT('Titip Makanan #', r.service_id)
            END as service_name
            FROM ratings r
            JOIN users u ON r.reviewer_id = u.id
            WHERE r.target_id = ?
            ORDER BY r.created_at DESC",
            [$userId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRatingSummary($userId) {
        return $this->db->query(
            "SELECT total_ratings, average_rating 
            FROM user_rating_summary 
            WHERE user_id = ?",
            [$userId]
        )->fetch(PDO::FETCH_ASSOC);
    }

    public function hasRatedService($userId, $serviceType, $serviceId) {
        return $this->db->query(
            "SELECT id FROM ratings 
            WHERE reviewer_id = ? AND service_type = ? AND service_id = ?",
            [$userId, $serviceType, $serviceId]
        )->fetch();
    }
    // Di method submitRating, setelah berhasil menyimpan rating
$notificationController = new NotificationController();
$notificationController->notifyNewRating(
    $targetId,
    $user['id'],
    $rating,
    $serviceType
);
}