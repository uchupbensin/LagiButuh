<?php
$userId = $_GET['id'] ?? 0;
$db = new Database();

// Get user details
$user = $db->query(
    "SELECT u.*, 
    (SELECT COUNT(*) FROM nebeng_rides WHERE user_id = u.id) as total_rides,
    (SELECT COUNT(*) FROM nebeng_bookings WHERE passenger_id = u.id) as total_passenger,
    (SELECT COUNT(*) FROM print_orders WHERE user_id = u.id) as total_print_orders,
    (SELECT COUNT(*) FROM psychology_sessions WHERE user_id = u.id) as total_psych_sessions
    FROM users u 
    WHERE u.id = ?",
    [$userId]
)->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo '<div class="bg-white rounded-lg shadow p-6">User tidak ditemukan</div>';
    return;
}

// Get user ratings
$ratingController = new RatingController();
$ratings = $ratingController->getRatingsForUser($userId);
$ratingSummary = $ratingController->getRatingSummary($userId);

// Get user services
$services = $db->query(
    "SELECT us.*, s.name as service_name, s.icon 
    FROM user_services us
    JOIN services s ON us.service_id = s.id
    WHERE us.user_id = ?",
    [$userId]
)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Detail Pengguna</h2>
        <a href="?page=users" class="text-blue-600 hover:underline">Kembali ke daftar pengguna</a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col items-center">
                    <img src="<?php echo $user['profile_picture'] ? BASE_URL.'/uploads/profiles/'.$user['profile_picture'] : BASE_URL.'/assets/images/default-profile.png'; ?>" 
                        alt="<?php echo e($user['name']); ?>" 
                        class="w-32 h-32 rounded-full mb-4 object-cover">
                    
                    <h3 class="text-xl font-semibold"><?php echo e($user['name']); ?></h3>
                    <div class="text-gray-600 mb-2"><?php echo e($user['email']); ?></div>
                    
                    <?php if ($user['university']): ?>
                        <div class="flex items-center text-gray-600 mb-4">
                            <i class="fas fa-university mr-2"></i>
                            <?php echo e($user['university']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex items-center mb-4">
                        <?php 
                        $rating = $user['rating'] ?? 0;
                        $fullStars = floor($rating);
                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                        
                        for ($i = 1; $i <= 5; $i++): 
                            if ($i <= $fullStars): ?>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-path="url(#half-star)"/><defs><clipPath id="half-star"><rect x="0" y="0" width="10" height="20"/></clipPath></defs></svg>
                            <?php else: ?>
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <?php endif;
                        endfor; ?>
                        <span class="ml-2 text-gray-700"><?php echo number_format($rating, 1); ?> (<?php echo $ratingSummary['total_ratings']; ?> ulasan)</span>
                    </div>
                    
                    <div class="text-sm text-gray-500 mb-4">
                        Bergabung pada <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                    </div>
                    
                    <div class="flex space-x-2">
                        <?php if ($user['is_banned']): ?>
                            <a href="?page=users&action=unban&id=<?php echo $user['id']; ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Unban</a>
                        <?php else: ?>
                            <a href="?page=users&action=ban&id=<?php echo $user['id']; ?>" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">Ban</a>
                        <?php endif; ?>
                        <a href="?page=users&action=delete&id=<?php echo $user['id']; ?>" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h4 class="font-semibold mb-4">Statistik Pengguna</h4>
                
                <div class="space-y-4">
                    <div>
                        <div class="text-gray-600 text-sm">Total Nebeng sebagai Driver</div>
                        <div class="font-semibold"><?php echo $user['total_rides']; ?></div>
                    </div>
                    
                    <div>
                        <div class="text-gray-600 text-sm">Total Nebeng sebagai Penumpang</div>
                        <div class="font-semibold"><?php echo $user['total_passenger']; ?></div>
                    </div>
                    
                    <div>
                        <div class="text-gray-600 text-sm">Total Cetak Dokumen</div>
                        <div class="font-semibold"><?php echo $user['total_print_orders']; ?></div>
                    </div>
                    
                    <div>
                        <div class="text-gray-600 text-sm">Total Sesi Psikolog</div>
                        <div class="font-semibold"><?php echo $user['total_psych_sessions']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="font-semibold mb-4">Layanan yang Ditawarkan</h4>
                
                <?php if (empty($services)): ?>
                    <div class="text-center py-8 text-gray-500">
                        Pengguna ini belum menawarkan layanan apapun
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($services as $service): ?>
                            <div class="border rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <img src="<?php echo BASE_URL; ?>/assets/images/icons/<?php echo $service['icon']; ?>" alt="<?php echo $service['service_name']; ?>" class="w-8 h-8 mr-2">
                                    <div class="font-medium"><?php echo $service['service_name']; ?></div>
                                </div>
                                <div class="text-sm text-gray-600"><?php echo $service['details'] ?? 'Tidak ada deskripsi'; ?></div>
                                <div class="mt-2 text-sm <?php echo $service['is_active'] ? 'text-green-600' : 'text-gray-500'; ?>">
                                    <?php echo $service['is_active'] ? 'Aktif' : 'Tidak aktif'; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h4 class="font-semibold mb-4">Ulasan untuk Pengguna Ini</h4>
                
                <?php if (empty($ratings)): ?>
                    <div class="text-center py-8 text-gray-500">
                        Belum ada ulasan untuk pengguna ini
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($ratings as $rating): ?>
                            <div class="border-b pb-4 last:border-0">
                                <div class="flex justify-between">
                                    <div class="font-medium"><?php echo $rating['service_name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo date('d M Y', strtotime($rating['created_at'])); ?></div>
                                </div>
                                <div class="flex items-center mt-1">
                                    <?php 
                                    $fullStars = floor($rating['rating']);
                                    $hasHalfStar = ($rating['rating'] - $fullStars) >= 0.5;
                                    
                                    for ($i = 1; $i <= 5; $i++): 
                                        if ($i <= $fullStars): ?>
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-path="url(#half-star)"/><defs><clipPath id="half-star"><rect x="0" y="0" width="10" height="20"/></clipPath></defs></svg>
                                        <?php else: ?>
                                            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <?php endif;
                                    endfor; ?>
                                </div>
                                <?php if (!empty($rating['review'])): ?>
                                    <div class="mt-2 text-gray-700"><?php echo $rating['review']; ?></div>
                                <?php endif; ?>
                                <div class="mt-2 text-sm text-gray-500">
                                    Oleh: <?php echo $rating['reviewer_name']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>