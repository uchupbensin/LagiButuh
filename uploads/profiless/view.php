<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../core/Auth.php';

$db = new Database();
$auth = new Auth($db->getConnection());

if (!$auth->isLoggedIn()) {
    header('Location: /login');
    exit;
}

$user = $auth->getUser();

// Get user activities
$activities = $db->query("
    (SELECT 'nebeng' as type, id, created_at, status FROM nebeng_rides WHERE user_id = {$user['id']} ORDER BY created_at DESC LIMIT 3)
    UNION
    (SELECT 'print' as type, id, created_at, status FROM print_orders WHERE user_id = {$user['id']} ORDER BY created_at DESC LIMIT 3)
    UNION
    (SELECT 'psychologist' as type, id, created_at, status FROM psychologist_bookings WHERE user_id = {$user['id']} ORDER BY created_at DESC LIMIT 3)
    ORDER BY created_at DESC LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Get user reviews
$reviews = $db->query("
    SELECT r.*, u.name as reviewed_name 
    FROM reviews r
    JOIN users u ON r.reviewed_id = u.id
    WHERE r.reviewer_id = {$user['id']}
    ORDER BY r.created_at DESC
    LIMIT 3
")->fetch_all(MYSQLI_ASSOC);

include __DIR__.'/../../templates/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Profile Sidebar -->
        <div class="md:w-1/3">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex flex-col items-center">
                    <div class="relative mb-4">
                        <img src="<?= $user['avatar'] ? '/uploads/profiles/'.$user['avatar'] : '/assets/images/default-avatar.jpg' ?>" 
                             alt="Profile" class="w-32 h-32 rounded-full object-cover">
                        <a href="/profile/edit" class="absolute bottom-0 right-0 bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </a>
                    </div>
                    <h2 class="text-xl font-bold"><?= e($user['name']) ?></h2>
                    <p class="text-gray-600 mb-2"><?= e($user['email']) ?></p>
                    <?php if ($user['phone']): ?>
                        <p class="text-gray-600"><?= e($user['phone']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold mb-4">Statistik Saya</h3>
                <ul class="space-y-3">
                    <li class="flex justify-between">
                        <span class="text-gray-600">Nebeng</span>
                        <span class="font-medium">
                            <?= $db->query("SELECT COUNT(*) FROM nebeng_rides WHERE user_id = {$user['id']}")->fetch_row()[0] ?>
                        </span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Print</span>
                        <span class="font-medium">
                            <?= $db->query("SELECT COUNT(*) FROM print_orders WHERE user_id = {$user['id']}")->fetch_row()[0] ?>
                        </span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Konsultasi</span>
                        <span class="font-medium">
                            <?= $db->query("SELECT COUNT(*) FROM psychologist_bookings WHERE user_id = {$user['id']}")->fetch_row()[0] ?>
                        </span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-600">Member sejak</span>
                        <span class="font-medium">
                            <?= date('d M Y', strtotime($user['created_at'])) ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="md:w-2/3">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Aktivitas Terkini</h2>
                
                <?php if (empty($activities)): ?>
                    <p class="text-gray-500">Belum ada aktivitas</p>
                <?php else: ?>
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($activities as $activity): ?>
                            <li class="py-4">
                                <div class="flex items-center">
                                    <div class="mr-4">
                                        <?php 
                                            $iconClass = '';
                                            $icon = '';
                                            
                                            switch ($activity['type']) {
                                                case 'nebeng':
                                                    $iconClass = 'bg-blue-100 text-blue-600';
                                                    $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />';
                                                    break;
                                                case 'print':
                                                    $iconClass = 'bg-yellow-100 text-yellow-600';
                                                    $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />';
                                                    break;
                                                case 'psychologist':
                                                    $iconClass = 'bg-purple-100 text-purple-600';
                                                    $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />';
                                                    break;
                                            }
                                        ?>
                                        <div class="p-2 rounded-lg <?= $iconClass ?>">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <?= $icon ?>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between">
                                            <h3 class="font-medium">
                                                <?= ucfirst($activity['type']) ?> #<?= $activity['id'] ?>
                                            </h3>
                                            <span class="text-sm text-gray-500">
                                                <?= date('d M H:i', strtotime($activity['created_at'])) ?>
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Status: 
                                            <span class="<?= 
                                                $activity['status'] === 'completed' ? 'text-green-600' : 
                                                ($activity['status'] === 'pending' ? 'text-yellow-600' : 'text-gray-600')
                                            ?>">
                                                <?= ucfirst($activity['status']) ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="mt-4 text-center">
                        <a href="/profile/activities" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                            Lihat Semua Aktivitas →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Ulasan Saya</h2>
                
                <?php if (empty($reviews)): ?>
                    <p class="text-gray-500">Belum ada ulasan</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($reviews as $review): ?>
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-medium"><?= e($review['reviewed_name']) ?></h3>
                                        <p class="text-sm text-gray-500">
                                            <?= ucfirst($review['service_type']) ?> • 
                                            <?= date('d M Y', strtotime($review['created_at'])) ?>
                                        </p>
                                    </div>
                                    <div class="flex items-center">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <svg class="w-4 h-4 <?= $i <= $review['rating'] ? 'text-yellow-500' : 'text-gray-300' ?>" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <?php if ($review['comment']): ?>
                                    <p class="text-gray-700 mt-2"><?= e($review['comment']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="/profile/reviews" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                            Lihat Semua Ulasan →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__.'/../../templates/footer.php'; ?>