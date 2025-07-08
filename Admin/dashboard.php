<?php
// File: admin/dashboard.php
require_once 'AdminService.php';

$adminService = new AdminService();
$stats = $adminService->getDashboardStats();
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Dasbor</h1>

<!-- Grid untuk menampilkan kartu statistik -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Kartu Total Pengguna -->
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
        <div>
            <div class="text-sm font-medium text-gray-500">Total Pengguna</div>
            <div class="text-3xl font-bold text-gray-900"><?php echo $stats['total_users'] ?? 0; ?></div>
        </div>
        <div class="bg-blue-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 006-6v-1a4 4 0 00-4-4h-2a4 4 0 00-4 4v1a6 6 0 006 6z"></path></svg>
        </div>
    </div>

    <!-- Kartu Total Booking Konsultasi -->
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
        <div>
            <div class="text-sm font-medium text-gray-500">Booking Konsultasi</div>
            <div class="text-3xl font-bold text-gray-900"><?php echo $stats['total_bookings'] ?? 0; ?></div>
        </div>
        <div class="bg-green-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
        </div>
    </div>

    <!-- Kartu Total Tumpangan Nebeng -->
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
        <div>
            <div class="text-sm font-medium text-gray-500">Tumpangan Nebeng</div>
            <div class="text-3xl font-bold text-gray-900"><?php echo $stats['total_rides'] ?? 0; ?></div>
        </div>
        <div class="bg-indigo-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
    </div>

    <!-- Kartu Total Laptop Tersedia -->
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
        <div>
            <div class="text-sm font-medium text-gray-500">Laptop Disewakan</div>
            <div class="text-3xl font-bold text-gray-900"><?php echo $stats['total_laptops'] ?? 0; ?></div>
        </div>
        <div class="bg-purple-100 p-3 rounded-full">
           <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </div>
    </div>
</div>

<!-- Tambahan: Grafik atau tabel ringkasan lainnya bisa ditambahkan di sini -->
<div class="mt-8 bg-white p-6 rounded-xl shadow-lg">
    <h2 class="text-xl font-bold text-gray-800">Aktivitas Terbaru</h2>
    <p class="text-gray-500 mt-2">Fitur untuk menampilkan aktivitas terbaru akan segera hadir.</p>
</div>
