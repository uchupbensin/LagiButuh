<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../core/Auth.php';

$db = new Database();
$auth = new Auth($db->getConnection());

$query = $_GET['q'] ?? '';
$type = $_GET['type'] ?? 'all';
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Prepare search query
$searchQuery = $db->escape("%$query%");

$results = [];
$totalResults = 0;

if ($type === 'all' || $type === 'nebeng') {
    // Search nebeng rides
    $nebengQuery = "SELECT 
                    nr.*, 
                    u.name as user_name,
                    u.avatar as user_avatar
                  FROM nebeng_rides nr
                  JOIN users u ON nr.user_id = u.id
                  WHERE (nr.departure LIKE '$searchQuery' OR 
                        nr.destination LIKE '$searchQuery' OR
                        nr.notes LIKE '$searchQuery' OR
                        u.name LIKE '$searchQuery')
                    AND nr.status = 'active'
                    AND nr.departure_time > NOW()
                  ORDER BY nr.departure_time ASC
                  LIMIT $limit OFFSET $offset";
    
    $nebengResult = $db->query($nebengQuery);
    $nebengCount = $db->query("SELECT FOUND_ROWS()")->fetch_row()[0];
    
    while ($row = $nebengResult->fetch_assoc()) {
        $row['type'] = 'nebeng';
        $results[] = $row;
    }
    
    $totalResults += $nebengCount;
}

if ($type === 'all' || $type === 'print') {
    // Search print services
    $printQuery = "SELECT 
                    ps.*, 
                    u.name as user_name,
                    u.avatar as user_avatar
                  FROM print_services ps
                  JOIN users u ON ps.user_id = u.id
                  WHERE (ps.service_name LIKE '$searchQuery' OR
                        ps.location LIKE '$searchQuery' OR
                        ps.description LIKE '$searchQuery' OR
                        u.name LIKE '$searchQuery')
                    AND ps.is_available = 1
                  ORDER BY ps.rating DESC
                  LIMIT $limit OFFSET $offset";
    
    $printResult = $db->query($printQuery);
    $printCount = $db->query("SELECT FOUND_ROWS()")->fetch_row()[0];
    
    while ($row = $printResult->fetch_assoc()) {
        $row['type'] = 'print';
        $results[] = $row;
    }
    
    $totalResults += $printCount;
}

if ($type === 'all' || $type === 'psychologist') {
    // Search psychologists
    $psyQuery = "SELECT 
                    p.*, 
                    u.name as user_name,
                    u.avatar as user_avatar
                  FROM psychologists p
                  JOIN users u ON p.user_id = u.id
                  WHERE (u.name LIKE '$searchQuery' OR
                        p.specialization LIKE '$searchQuery' OR
                        p.qualifications LIKE '$searchQuery')
                    AND p.is_available = 1
                  ORDER BY p.rating DESC
                  LIMIT $limit OFFSET $offset";
    
    $psyResult = $db->query($psyQuery);
    $psyCount = $db->query("SELECT FOUND_ROWS()")->fetch_row()[0];
    
    while ($row = $psyResult->fetch_assoc()) {
        $row['type'] = 'psychologist';
        $results[] = $row;
    }
    
    $totalResults += $psyCount;
}

include __DIR__.'/../../templates/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Search Form -->
        <form method="GET" action="/search" class="mb-8">
            <div class="flex shadow-lg rounded-lg overflow-hidden">
                <input type="text" name="q" value="<?= e($query) ?>" 
                       placeholder="Cari layanan (nebeng, print, psikolog)..."
                       class="flex-1 px-4 py-3 focus:outline-none">
                <select name="type" class="border-l px-3 bg-white focus:outline-none">
                    <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>Semua Layanan</option>
                    <option value="nebeng" <?= $type === 'nebeng' ? 'selected' : '' ?>>Nebeng</option>
                    <option value="print" <?= $type === 'print' ? 'selected' : '' ?>>Print</option>
                    <option value="psychologist" <?= $type === 'psychologist' ? 'selected' : '' ?>>Psikolog</option>
                </select>
                <button type="submit" 
                        class="bg-blue-500 text-white px-6 hover:bg-blue-600 transition">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
        
        <!-- Search Results -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4">
                <?= $totalResults ?> hasil ditemukan untuk "<?= e($query) ?>"
            </h2>
            
            <?php if (empty($results)): ?>
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-search fa-3x"></i>
                    </div>
                    <p class="text-gray-600">Tidak ada hasil yang ditemukan. Coba dengan kata kunci lain.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($results as $item): ?>
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="p-4">
                                <div class="flex items-start">
                                    <div class="mr-4">
                                        <?php if ($item['type'] === 'nebeng'): ?>
                                            <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                                                <i class="fas fa-car"></i>
                                            </div>
                                        <?php elseif ($item['type'] === 'print'): ?>
                                            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
                                                <i class="fas fa-print"></i>
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                                                <i class="fas fa-brain"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold">
                                            <?php if ($item['type'] === 'nebeng'): ?>
                                                Nebeng dari <?= e($item['departure']) ?> ke <?= e($item['destination']) ?>
                                            <?php elseif ($item['type'] === 'print'): ?>
                                                <?= e($item['service_name']) ?> - <?= e($item['location']) ?>
                                            <?php else: ?>
                                                <?= e($item['user_name']) ?> - <?= e($item['specialization']) ?>
                                            <?php endif; ?>
                                        </h3>
                                        
                                        <?php if ($item['type'] === 'nebeng'): ?>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="far fa-clock mr-1"></i>
                                                <?= date('d M Y H:i', strtotime($item['departure_time'])) ?>
                                                • <?= $item['available_seats'] ?> kursi tersedia
                                            </p>
                                        <?php elseif ($item['type'] === 'print'): ?>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                                <?= number_format($item['rating'], 1) ?> • 
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                <?= e($item['location']) ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                                <?= number_format($item['rating'], 1) ?> • 
                                                <?= e($item['specialization']) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <div class="flex items-center mt-3">
                                            <img src="<?= $item['user_avatar'] ? '/uploads/profiles/'.$item['user_avatar'] : '/assets/images/default-avatar.jpg' ?>" 
                                                 alt="<?= e($item['user_name']) ?>" 
                                                 class="w-6 h-6 rounded-full mr-2">
                                            <span class="text-sm text-gray-600"><?= e($item['user_name']) ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="/<?= $item['type'] ?>/detail?id=<?= $item['id'] ?>" 
                                           class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalResults > $limit): ?>
                    <div class="flex justify-center mt-8">
                        <nav class="flex items-center space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?q=<?= urlencode($query) ?>&type=<?= $type ?>&page=<?= $page - 1 ?>" 
                                   class="px-3 py-1 border rounded-lg hover:bg-gray-100 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php 
                                $totalPages = ceil($totalResults / $limit);
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                if ($startPage > 1) {
                                    echo '<span class="px-3 py-1">...</span>';
                                }
                                
                                for ($i = $startPage; $i <= $endPage; $i++): 
                            ?>
                                <a href="?q=<?= urlencode($query) ?>&type=<?= $type ?>&page=<?= $i ?>" 
                                   class="px-3 py-1 border rounded-lg <?= $i === $page ? 'bg-blue-500 text-white' : 'hover:bg-gray-100' ?> transition">
                                    <?= $i ?>
                                </a>
                            <?php endfor; 
                            
                                if ($endPage < $totalPages) {
                                    echo '<span class="px-3 py-1">...</span>';
                                }
                            ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?q=<?= urlencode($query) ?>&type=<?= $type ?>&page=<?= $page + 1 ?>" 
                                   class="px-3 py-1 border rounded-lg hover:bg-gray-100 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__.'/../../templates/footer.php'; ?>