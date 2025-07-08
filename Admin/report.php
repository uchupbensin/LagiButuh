<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../core/Auth.php';

$db = new Database();
$auth = new Auth($db->getConnection());

if (!$auth->isAdmin()) {
    header('Location: /login');
    exit;
}

// Date range filter
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');

// Get report data
$reports = [
    'user_registrations' => $this->getUserRegistrations($startDate, $endDate),
    'service_usage' => $this->getServiceUsage($startDate, $endDate),
    'revenue' => $this->getRevenueData($startDate, $endDate),
    'top_users' => $this->getTopUsers($startDate, $endDate)
];

function getUserRegistrations($startDate, $endDate) {
    global $db;
    
    $query = "SELECT 
                DATE(created_at) as date, 
                COUNT(*) as count,
                SUM(CASE WHEN role = 'psychologist' THEN 1 ELSE 0 END) as psychologists
              FROM users
              WHERE created_at BETWEEN '$startDate' AND '$endDate 23:59:59'
              GROUP BY DATE(created_at)
              ORDER BY date";
    
    $result = $db->query($query);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

function getServiceUsage($startDate, $endDate) {
    global $db;
    
    $query = "SELECT 
                'nebeng' as service, 
                COUNT(*) as count 
              FROM nebeng_rides
              WHERE created_at BETWEEN '$startDate' AND '$endDate 23:59:59'
              
              UNION ALL
              
              SELECT 
                'print' as service, 
                COUNT(*) as count 
              FROM print_orders
              WHERE created_at BETWEEN '$startDate' AND '$endDate 23:59:59'
              
              UNION ALL
              
              SELECT 
                'psychologist' as service, 
                COUNT(*) as count 
              FROM psychologist_bookings
              WHERE created_at BETWEEN '$startDate' AND '$endDate 23:59:59'";
    
    $result = $db->query($query);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[$row['service']] = $row['count'];
    }
    
    return $data;
}

function getRevenueData($startDate, $endDate) {
    global $db;
    
    $query = "SELECT 
                DATE(created_at) as date,
                SUM(amount) as total
              FROM payments
              WHERE status = 'success' 
                AND created_at BETWEEN '$startDate' AND '$endDate 23:59:59'
              GROUP BY DATE(created_at))
              ORDER BY date";
    
    $result = $db->query($query);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

function getTopUsers($startDate, $endDate) {
    global $db;
    
    $query = "SELECT 
                u.id, u.name, u.email,
                COUNT(DISTINCT nr.id) as nebeng_count,
                COUNT(DISTINCT po.id) as print_count,
                COUNT(DISTINCT pb.id) as psychologist_count
              FROM users u
              LEFT JOIN nebeng_rides nr ON nr.user_id = u.id 
                AND nr.created_at BETWEEN '$startDate' AND '$endDate 23:59:59'
              LEFT JOIN print_orders po ON po.user_id = u.id 
                AND po.created_at BETWEEN '$startDate' AND '$endDate 23:59:59'
              LEFT JOIN psychologist_bookings pb ON pb.user_id = u.id 
                AND pb.created_at BETWEEN '$startDate' AND '$endDate 23:59:59'
              GROUP BY u.id
              ORDER BY (nebeng_count + print_count + psychologist_count) DESC
              LIMIT 10";
    
    $result = $db->query($query);
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

include __DIR__.'/../templates/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Laporan dan Analitik</h1>
    
    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="<?= $startDate ?>" 
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex-1">
                <label class="block text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="<?= $endDate ?>" 
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="self-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 mb-2">Pengguna Baru</h3>
            <p class="text-2xl font-bold">
                <?= array_sum(array_column($reports['user_registrations'], 'count')) ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 mb-2">Total Layanan</h3>
            <p class="text-2xl font-bold">
                <?= array_sum($reports['service_usage']) ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 mb-2">Pendapatan</h3>
            <p class="text-2xl font-bold">
                Rp<?= number_format(array_sum(array_column($reports['revenue'], 'total')), 0, ',', '.') ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 mb-2">Psikolog Baru</h3>
            <p class="text-2xl font-bold">
                <?= array_sum(array_column($reports['user_registrations'], 'psychologists')) ?>
            </p>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- User Registrations Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold mb-4">Registrasi Pengguna</h3>
            <canvas id="userRegistrationsChart" height="300"></canvas>
        </div>
        
        <!-- Service Usage Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold mb-4">Penggunaan Layanan</h3>
            <canvas id="serviceUsageChart" height="300"></canvas>
        </div>
        
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold mb-4">Pendapatan</h3>
            <canvas id="revenueChart" height="300"></canvas>
        </div>
        
        <!-- Top Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold mb-4">Pengguna Teraktif</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nebeng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Print</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Psikolog</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($reports['top_users'] as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900"><?= $user['name'] ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $user['nebeng_count'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $user['print_count'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $user['psychologist_count'] ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Registrations Chart
    const userRegCtx = document.getElementById('userRegistrationsChart').getContext('2d');
    const userRegChart = new Chart(userRegCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($reports['user_registrations'], 'date')) ?>,
            datasets: [
                {
                    label: 'Total Pengguna',
                    data: <?= json_encode(array_column($reports['user_registrations'], 'count')) ?>,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Psikolog',
                    data: <?= json_encode(array_column($reports['user_registrations'], 'psychologists')) ?>,
                    borderColor: 'rgba(139, 92, 246, 1)',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Service Usage Chart
    const serviceUsageCtx = document.getElementById('serviceUsageChart').getContext('2d');
    const serviceUsageChart = new Chart(serviceUsageCtx, {
        type: 'bar',
        data: {
            labels: ['Nebeng', 'Print', 'Psikolog'],
            datasets: [{
                label: 'Jumlah Penggunaan',
                data: <?= json_encode(array_values($reports['service_usage'])) ?>,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(234, 179, 8, 0.7)',
                    'rgba(139, 92, 246, 0.7)'
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(234, 179, 8, 1)',
                    'rgba(139, 92, 246, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($reports['revenue'], 'date')) ?>,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: <?= json_encode(array_column($reports['revenue'], 'total')) ?>,
                borderColor: 'rgba(16, 185, 129, 1)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php include __DIR__.'/../templates/footer.php'; ?>