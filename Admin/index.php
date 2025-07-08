<?php
// File: admin/index.php
// Router utama untuk semua halaman di dalam admin panel.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Muat file-file konfigurasi dan inti
require_once '../config/env.php';
require_once '../config/database.php';
require_once '../core/AdminAuth.php';
require_once '../functions/helper_functions.php';

// Amankan semua halaman admin
$adminAuth = new AdminAuth();
$adminAuth->securePage();

// Routing sederhana di dalam folder admin
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$page = basename($page); // Mencegah directory traversal

// Muat header
include 'templates/header.php';

// Muat konten halaman yang diminta
$pagePath = $page . '.php';
if (file_exists($pagePath)) {
    include $pagePath;
} else {
    // Jika halaman tidak ditemukan, tampilkan dasbor sebagai default
    include 'dashboard.php';
}

// Muat footer
include 'templates/footer.php';
