<?php
// File: modules/print/accept_job.php

require_once __DIR__ . '/../../functions/service_functions.php';
$auth = new Auth();

// 1. Pastikan pengguna sudah login
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

// 2. Periksa apakah ID pekerjaan dikirim
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(BASE_URL . '/print'); // Diarahkan ke halaman terpadu
}

$jobId = (int)$_GET['id'];
$providerId = $auth->getUserId();
$service = new Service();

// 3. Coba terima pekerjaan
$result = $service->acceptPrintJob($jobId, $providerId);

// 4. Atur pesan dan arahkan kembali
if ($result) {
    $_SESSION['flash_message'] = [
        'type' => 'success', 
        'message' => 'Berhasil! Anda telah mengambil pekerjaan ini. Silakan hubungi pemesan.'
    ];
} else {
    $_SESSION['flash_message'] = [
        'type' => 'error', 
        'message' => 'Gagal mengambil pekerjaan. Mungkin sudah diambil orang lain atau Anda mencoba mengambil pekerjaan sendiri.'
    ];
}

// 5. Arahkan kembali ke halaman Jasa Cetak terpadu
redirect(BASE_URL . '/print');