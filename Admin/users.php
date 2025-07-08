<?php
$db = new Database();
$auth = new AdminAuth();

// Handle actions
if (isset($_GET['action'])) {
    if (!$auth->hasPermission('admin')) {
        $_SESSION['admin_error'] = 'Anda tidak memiliki izin untuk tindakan ini';
        header('Location: ?page=users');
        exit();
    }

    $userId = $_GET['id'] ?? 0;
    
    switch ($_GET['action']) {
        case 'ban':
            $db->query("UPDATE users SET is_banned = TRUE WHERE id = ?", [$userId]);
            $_SESSION['admin_success'] = 'Pengguna berhasil dibanned';
            break;
            
        case 'unban':
            $db->query("UPDATE users SET is_banned = FALSE WHERE id = ?", [$userId]);
            $_SESSION['admin_success'] = 'Pengguna berhasil diunban';
            break;
            
        case 'delete':
            $db->query("DELETE FROM users WHERE id = ?", [$userId]);
            $_SESSION['admin_success'] = 'Pengguna berhasil dihapus';
            break;
    }
    
    header('Location: ?page=users');
    exit();
}

// Get users with pagination
$page = max(1, $_GET['p'] ?? 1);
$perPage = 20;
$offset = ($page - 1) * $perPage;

$search = $_GET['search'] ?? '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE name LIKE ? OR email LIKE ? OR university LIKE ?";
    $params = array_fill(0, 3, "%$search%");
}

$users = $db->query(
    "SELECT id, name, email, university, profile_picture, rating, is_banned, created_at 
    FROM users $where 
    ORDER BY created_at DESC 
    LIMIT $offset, $perPage",
    $params
)->fetchAll(PDO::FETCH_ASSOC);

$totalUsers = $db->query("SELECT COUNT(*) as count FROM users $where", $params)->fetch()['count'];
$totalPages = ceil($totalUsers / $perPage);
?>

<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Manajemen Pengguna</h2>
        <div class="relative">
            <form method="get" action="">
                <input type="hidden" name="page" value="users">
                <input type="text" name="search" placeholder="Cari pengguna..." 
                    value="<?php echo e($search); ?>"
                    class="border rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </form>
        </div>
    </div>
    
    <?php if (isset($_SESSION['admin_success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['admin_error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Universitas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                <tr class="<?php echo $user['is_banned'] ? 'bg-red-50' : ''; ?>">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" 
                                    src="<?php echo $user['profile_picture'] ? BASE_URL.'/uploads/profiles/'.$user['profile_picture'] : BASE_URL.'/assets/images/default-profile.png'; ?>" 
                                    alt="<?php echo e($user['name']); ?>">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo e($user['name']); ?>
                                    <?php if ($user['is_banned']): ?>
                                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full ml-2">Banned</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo e($user['email']); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo e($user['university'] ?? '-'); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <?php 
                            $rating = $user['rating'] ?? 0;
                            $fullStars = floor($rating);
                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                            
                            for ($i = 1; $i <= 5; $i++): 
                                if ($i <= $fullStars): ?>
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-path="url(#half-star)"/><defs><clipPath id="half-star"><rect x="0" y="0" width="10" height="20"/></clipPath></defs></svg>
                                <?php else: ?>
                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <?php endif;
                            endfor; ?>
                            <span class="text-xs text-gray-500 ml-1">(<?php echo number_format($rating, 1); ?>)</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="?page=user_detail&id=<?php echo $user['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                        <?php if ($user['is_banned']): ?>
                            <a href="?page=users&action=unban&id=<?php echo $user['id']; ?>" class="text-green-600 hover:text-green-900 mr-3">Unban</a>
                        <?php else: ?>
                            <a href="?page=users&action=ban&id=<?php echo $user['id']; ?>" class="text-yellow-600 hover:text-yellow-900 mr-3">Ban</a>
                        <?php endif; ?>
                        <a href="?page=users&action=delete&id=<?php echo $user['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium"><?php echo ($offset + 1); ?></span> sampai <span class="font-medium"><?php echo min($offset + $perPage, $totalUsers); ?></span> dari <span class="font-medium"><?php echo $totalUsers; ?></span> hasil
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=users&p=<?php echo $page - 1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=users&p=<?php echo $i; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="<?php echo $i == $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=users&p=<?php echo $page + 1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>