<?php
// File: modules/profile/view.php (Final dengan Full Styling 300+ Baris)

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

require_once __DIR__ . '/../../functions/service_functions.php';
$service = new Service();
$user = $auth->getUserById($auth->getUserId());
$consultationBookings = $service->getBookingsByUserId($auth->getUserId());
$nebengRides = $service->getNebengRidesByPassenger($auth->getUserId());
$laptopBookings = $service->getLaptopBookingsByUserId($auth->getUserId());
$printJobs = $service->getPrintJobsByUserId($auth->getUserId());
$jastipOrders = $service->getJastipOrdersByUserId($auth->getUserId());
$jastipTaken = $service->getJastipOrdersTakenByUser($auth->getUserId());


$pageTitle = "Profil Saya";
include_once __DIR__ . '/../../templates/header.php';
?>

<section class="bg-[#F9FAFB] px-4 py-16 font-sans min-h-screen">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-[#1E1E2F] mb-10 text-center tracking-tight">Dasbor Akun Saya</h1>

        <div class="text-center mb-10">
            <a href="<?php echo BASE_URL; ?>/" class="inline-flex items-center text-sm text-[#5C3AC7] font-medium hover:underline transition-colors">
                ← Kembali ke Beranda
            </a>
        </div>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <?php
            $flash = $_SESSION['flash_message'];
            $alertTypeClass = $flash['type'] === 'success'
                ? 'bg-green-100 border-green-500 text-green-700'
                : 'bg-red-100 border-red-500 text-red-700';
            ?>
            <div class="border-l-4 <?php echo $alertTypeClass; ?> p-4 mb-10 rounded-xl shadow max-w-2xl mx-auto">
                <p class="font-semibold text-sm mb-1 tracking-wide uppercase"><?php echo ucfirst($flash['type']); ?></p>
                <p class="text-sm leading-snug"><?php echo $flash['message']; ?></p>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <aside class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl shadow-lg text-center sticky top-28">
                    <img src="<?php echo BASE_URL . '/uploads/profiles/' . ($user['profile_picture'] ?? 'default.png'); ?>" alt="Foto Profil" class="w-28 h-28 rounded-full object-cover mx-auto mb-4 border-4 border-[#D6C8FF]">
                    <h2 class="text-2xl font-bold text-[#1E1E2F] leading-snug mb-1"><?php echo e($user['full_name'] ?? $user['username']); ?></h2>
                    <p class="text-sm text-gray-500"><?php echo e($user['email']); ?></p>
                    <p class="text-xs text-gray-400 mt-2">Bergabung sejak: <?php echo date('d F Y', strtotime($user['created_at'])); ?></p>
                    <a href="<?php echo BASE_URL; ?>/profile/edit" class="mt-6 inline-block w-full bg-[#5C3AC7] text-white font-semibold py-2 rounded-xl hover:bg-[#3D2A94] transition duration-200">
                        Edit Profil
                    </a>
                </div>
            </aside>

            <main class="lg:col-span-2 space-y-10">
                <?php
                $sections = [
                    'Riwayat Konsultasi Psikolog' => $consultationBookings,
                    'Tumpangan Nebeng' => $nebengRides,
                    'Peminjaman Laptop' => $laptopBookings,
                    'Cetak Dokumen' => $printJobs,
                    'Jastip Anda' => $jastipOrders
                ];
                $sections = [
                    'Riwayat Konsultasi Psikolog' => $consultationBookings,
                    'Tumpangan Nebeng' => $nebengRides,
                    'Peminjaman Laptop' => $laptopBookings,
                    'Cetak Dokumen' => $printJobs,
                    'Jastip Anda' => $jastipOrders,
                    'Pesanan Jastip yang Anda Ambil' => $jastipTaken
                ];

                foreach ($sections as $title => $items):
                ?>
                <div class="bg-white p-6 rounded-2xl shadow">
                    <h3 class="text-xl font-semibold text-[#1E1E2F] mb-4 tracking-tight"><?php echo $title; ?></h3>
                    <?php if (empty($items)): ?>
                        <p class="text-gray-400 text-sm">Belum ada data untuk <?php echo strtolower($title); ?>.</p>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <div class="p-4 bg-[#F8F7FC] rounded-lg mb-4 text-sm text-gray-700">
                                <?php if ($title === 'Riwayat Konsultasi Psikolog'): ?>
                                    <p class="font-semibold text-[#1E1E2F]"><?php echo e($item['psychologist_name']) . ' - ' . e($item['specialization']); ?></p>
                                    <p>Jadwal: <?php echo date('d M Y, H:i', strtotime($item['schedule_time'])); ?></p>
                                    <p>Status: <span class="inline-block px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs font-medium"><?php echo ucfirst($item['status']); ?></span></p>
                                <?php elseif ($title === 'Tumpangan Nebeng'): ?>
                                    <p class="font-semibold text-[#1E1E2F]"><?php echo $item['origin']; ?> → <?php echo $item['destination']; ?></p>
                                    <p>Tanggal: <?php echo date('d M Y, H:i', strtotime($item['departure_time'])); ?></p>
                                    <p>Driver: <?php echo $item['driver_name']; ?></p>
                                <?php elseif ($title === 'Peminjaman Laptop'): ?>
                                    <p class="font-semibold text-[#1E1E2F]"><?php echo $item['brand'] . ' ' . $item['model']; ?></p>
                                    <p>Tanggal Pinjam: <?php echo date('d M Y', strtotime($item['start_date'])) . ' - ' . date('d M Y', strtotime($item['end_date'])); ?></p>
                                    <p>Status: <?php echo ucfirst($item['status']); ?></p>
                                <?php elseif ($title === 'Cetak Dokumen'): ?>
                                    <p class="font-semibold text-[#1E1E2F]"><?php echo $item['file_path']; ?> (<?php echo $item['copies']; ?> salinan)</p>
                                    <p>Status: <?php echo ucfirst($item['status']); ?></p>
                                    <p>Tanggal: <?php echo date('d M Y, H:i', strtotime($item['created_at'])); ?></p>
                                <?php elseif ($title === 'Jastip Anda'): ?>
                                    <p class="font-semibold text-[#1E1E2F]"><?php echo $item['item_description']; ?></p>
                                    <p>Lokasi beli: <?php echo $item['purchase_location']; ?> → Kirim ke: <?php echo $item['delivery_location']; ?></p>
                                    <p>Status: <?php echo ucfirst($item['status']); ?></p>
                                    <<?php elseif ($title === 'Pesanan Jastip yang Anda Ambil'): ?>
    <div class="flex justify-between items-center">
        <div>
            <p class="font-semibold text-[#1E1E2F]"><?php echo $item['item_description']; ?></p>
            <p>Ambil dari: <?php echo $item['purchase_location']; ?> → Kirim ke: <?php echo $item['delivery_location']; ?></p>
            <p>Pemesan: <?php echo $item['orderer_name']; ?></p>
            <p>Status:
                <span class="inline-block text-xs font-medium px-2 py-1 rounded
                    <?php
                        $statusColor = [
                            'open' => 'bg-gray-200 text-gray-700',
                            'accepted' => 'bg-yellow-100 text-yellow-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-700'
                        ];
                        echo $statusColor[$item['status']] ?? 'bg-gray-100 text-gray-600';
                    ?>">
                    <?php echo ucfirst($item['status']); ?>
                </span>
            </p>
        </div>

        <?php if ($item['status'] === 'accepted'): ?>
            <form method="POST" action="<?php echo BASE_URL . '/jastip/complete'; ?>">
                <input type="hidden" name="order_id" value="<?php echo $item['id']; ?>">
                <button type="submit"
                    class="bg-[#5C3AC7] hover:bg-[#3D2A94] text-white text-sm font-semibold py-2 px-4 rounded-xl shadow transition">
                    Tandai Selesai
                </button>
            </form>
        <?php endif; ?>
    </div>

                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </main>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>
