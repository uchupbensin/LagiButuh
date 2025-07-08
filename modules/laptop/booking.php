<?php
// File: modules/laptop/booking.php

require_once __DIR__ . '/../../functions/service_functions.php';
$auth = new Auth();

if (!$auth->isLoggedIn()) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Silakan login untuk melakukan booking.'];
    redirect(BASE_URL . '/login');
}

$service = new Service();

$laptopId = isset($_POST['laptop_id']) ? (int)$_POST['laptop_id'] : 0;
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';
$userId = $auth->getUserId();

// Validasi data awal
if (!$laptopId || !$startDate || !$endDate) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Data booking tidak lengkap.'];
    redirect(BASE_URL . "/laptop/detail/$laptopId");
}

// Cek apakah laptop valid
$laptop = $service->getLaptopDetailsById($laptopId);
if (!$laptop) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Laptop tidak ditemukan.'];
    redirect(BASE_URL . '/laptop/list');
}

// Proteksi: Tidak boleh booking laptop sendiri
if ($laptop['owner_id'] === $userId) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Anda tidak dapat menyewa laptop milik sendiri.'];
    redirect(BASE_URL . "/laptop/detail/$laptopId");
}

// Validasi tanggal
if (strtotime($startDate) > strtotime($endDate)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Tanggal mulai tidak boleh lebih dari tanggal selesai.'];
    redirect(BASE_URL . "/laptop/detail/$laptopId");
}

// TODO: Tambahkan validasi apakah tanggal bentrok dengan booking lain, jika diperlukan

// Simpan booking ke database
$bookingResult = $service->createLaptopBooking($userId, $laptopId, $startDate, $endDate);

if ($bookingResult === true) {
    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Booking berhasil dilakukan!'];
    redirect(BASE_URL . "/laptop/detail/$laptopId");
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Gagal melakukan booking. Silakan coba lagi.'];
    redirect(BASE_URL . "/laptop/detail/$laptopId");
}
