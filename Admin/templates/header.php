<?php
// File: admin/templates/header.php
$currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
if (isset($_GET['page'])) {
    $currentPage = $_GET['page'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($currentPage); ?> - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="hidden md:flex flex-col w-64 bg-gray-800">
            <div class="flex items-center justify-center h-16 bg-gray-900">
                <span class="text-white font-bold uppercase">Admin Panel</span>
            </div>
            <div class="flex flex-col flex-1 overflow-y-auto">
                <nav class="flex-1 px-2 py-4 bg-gray-800">
                    <a href="index.php?page=dashboard" class="flex items-center px-4 py-2 text-gray-100 hover:bg-gray-700 <?php echo $currentPage == 'dashboard' ? 'bg-gray-900' : ''; ?>">
                        Dasbor
                    </a>
                    <a href="index.php?page=manage_users" class="flex items-center px-4 py-2 mt-2 text-gray-100 hover:bg-gray-700 <?php echo $currentPage == 'manage_users' ? 'bg-gray-900' : ''; ?>">
                        Manajemen Pengguna
                    </a>
                    <!-- Tambahkan link menu lain di sini -->
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-y-auto">
            <div class="flex items-center justify-between h-16 bg-white border-b border-gray-200">
                <div class="flex items-center px-4">
                    <!-- Tombol menu mobile bisa ditambahkan di sini -->
                </div>
                <div class="flex items-center pr-4">
                    <span class="text-gray-600 mr-4">Halo, <?php echo e($_SESSION['admin_name']); ?></span>
                    <a href="logout.php" class="text-sm text-red-600 hover:underline">Logout</a>
                </div>
            </div>
            <div class="p-4 md:p-8">
