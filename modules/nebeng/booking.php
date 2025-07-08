<?php
// File: modules/nebeng/booking.php
// Handler untuk proses booking kursi nebeng.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../functions/helper_functions.php';
require_once __DIR__ . '/../../functions/service_functions.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    // Pengguna harus login untuk booking
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Anda harus login untuk mengambil kursi.'];
    redirect(BASE_URL . '/login');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL);
}

$passengerId = $auth->getUserId();
$rideId = filter_input(INPUT_POST, 'ride_id', FILTER_VALIDATE_INT);

if (!$rideId) {
    redirect(BASE_URL . '/nebeng/find_ride');
}

$service = new Service();
$result = $service->createNebengBooking($rideId, $passengerId);

if ($result === true) {
    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Berhasil! Anda sudah mendapatkan 1 kursi. Silakan hubungi pengemudi.'];
} else {
    // Jika ada error (string), tampilkan pesannya
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => $result];
}

redirect(BASE_URL . '/nebeng/detail/' . $rideId);
?>
