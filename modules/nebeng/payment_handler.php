<?php
// File: modules/nebeng/payment_handler.php
// Handler simulasi untuk donasi "Bayar Seikhlasnya".

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../functions/helper_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL);
}

// Di aplikasi nyata, di sini akan ada integrasi dengan SDK Payment Gateway (Midtrans, dll).
// Untuk simulasi, kita anggap pembayaran selalu berhasil dan tampilkan notifikasi.

$rideId = filter_input(INPUT_POST, 'ride_id', FILTER_VALIDATE_INT);
$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);

if (!$rideId || !$amount || $amount <= 0) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Jumlah donasi tidak valid.'];
} else {
    // Simulasi sukses
    $formattedAmount = 'Rp ' . number_format($amount, 0, ',', '.');
    $_SESSION['flash_message'] = ['type' => 'success', 'message' => "Terima kasih! Donasi Anda sebesar {$formattedAmount} sangat berarti bagi pengemudi."];
    
    // Di sini bisa ditambahkan logika untuk mencatat transaksi donasi ke tabel `transactions` jika perlu.
}

redirect(BASE_URL . '/nebeng/detail/' . $rideId);
?>
