public function notifyNewRating($targetId, $reviewerId, $rating, $serviceType) {
    $reviewer = $this->db->query(
        "SELECT name FROM users WHERE id = ?",
        [$reviewerId]
    )->fetch(PDO::FETCH_ASSOC);

    $serviceNames = [
        'nebeng' => 'Nebeng',
        'print' => 'Print',
        'psychologist' => 'Konsultasi Psikolog',
        'laptop' => 'Peminjaman Laptop',
        'food' => 'Titip Makanan'
    ];

    $message = "{$reviewer['name']} memberikan rating {$rating} bintang untuk layanan {$serviceNames[$serviceType]} Anda";
    
    $this->createNotification(
        $targetId,
        'new_rating',
        $message,
        "/profile/ratings"
    );
}