<?php
// File: admin/manage_users.php
require_once 'AdminService.php';

$adminService = new AdminService();

// Logika Paginasi
$pageNumber = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$limit = 10; // Jumlah user per halaman
$offset = ($pageNumber - 1) * $limit;
$totalUsers = $adminService->countAllUsers();
$totalPages = ceil($totalUsers / $limit);

$users = $adminService->getAllUsers($limit, $offset);
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Pengguna</h1>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left py-3 px-6 font-semibold text-sm text-gray-600 uppercase">Nama Lengkap</th>
                    <th class="text-left py-3 px-6 font-semibold text-sm text-gray-600 uppercase">Email</th>
                    <th class="text-left py-3 px-6 font-semibold text-sm text-gray-600 uppercase">Role</th>
                    <th class="text-left py-3 px-6 font-semibold text-sm text-gray-600 uppercase">Tanggal Bergabung</th>
                    <th class="text-center py-3 px-6 font-semibold text-sm text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y divide-gray-200">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-500">Tidak ada data pengguna.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6 font-medium"><?php echo e($user['full_name'] ?? $user['username']); ?></td>
                            <td class="py-4 px-6"><?php echo e($user['email']); ?></td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $user['role'] === 'admin' ? 'bg-red-200 text-red-800' : 'bg-blue-200 text-blue-800'; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-sm"><?php echo date('d F Y', strtotime($user['created_at'])); ?></td>
                            <td class="py-4 px-6 text-center">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                <span class="text-gray-300 mx-1">|</span>
                                <a href="#" class="text-red-600 hover:text-red-900 font-medium">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Paginasi -->
    <?php if ($totalPages > 1): ?>
    <div class="p-4 flex justify-between items-center">
        <span class="text-sm text-gray-600">Menampilkan <?php echo $offset + 1; ?>-<?php echo min($offset + $limit, $totalUsers); ?> dari <?php echo $totalUsers; ?> pengguna</span>
        <div class="flex space-x-1">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=manage_users&p=<?php echo $i; ?>" class="px-3 py-1 rounded-md text-sm font-medium <?php echo $i == $pageNumber ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
