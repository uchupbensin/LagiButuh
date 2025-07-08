<?php
// File: modules/psychologist/booking.php
// Handler untuk proses booking.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Muat semua file yang diperlukan
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../functions/helper_functions.php';
require_once __DIR__ . '/../../functions/service_functions.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL);
}

// Validasi dan sanitasi input
$userId = $auth->getUserId();
$psychologistId = filter_input(INPUT_POST, 'psychologist_id', FILTER_VALIDATE_INT);
$scheduleTime = sanitize_input($_POST['schedule_time']);

if (!$psychologistId || empty($scheduleTime)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Data booking tidak lengkap.'];
    redirect(BASE_URL . '/psychologist/detail/' . $psychologistId);
}

// Proses booking
$service = new Service();
$result = $service->createConsultationBooking($userId, $psychologistId, $scheduleTime);

if (is_numeric($result)) {
    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Booking berhasil dibuat! Silakan tunggu konfirmasi dari psikolog.'];
    redirect(BASE_URL . '/profile'); // Redirect ke halaman profil untuk melihat booking
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => $result];
    redirect(BASE_URL . '/psychologist/detail/' . $psychologistId);
}
?>