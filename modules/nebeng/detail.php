<?php
// File: modules/nebeng/detail.php (Versi Final & Lengkap)

// Memastikan file-file inti sudah dimuat
require_once __DIR__ . '/../../functions/service_functions.php';

// --- PERBAIKAN: Inisialisasi kelas Auth ---
$auth = new Auth();
// -----------------------------------------

// Ambil ID tumpangan dari URL yang disediakan oleh router (variabel $param)
if (!isset($param) || !is_numeric($param)) {
    redirect(BASE_URL . '/nebeng/find_ride');
}

$service = new Service();
$ride = $service->getNebengRideDetailsById($param);

// Pemeriksaan yang ketat: jika $ride tidak ditemukan, hentikan eksekusi.
if (!$ride) {
    http_response_code(404);
    $pageTitle = "Tumpangan Tidak Ditemukan";
    include_once __DIR__ . '/../../templates/header.php';
    echo '<div class="text-center py-20"><h1 class="text-2xl font-bold">404 - Tumpangan Tidak Ditemukan</h1><p class="text-gray-600 mt-2">Tumpangan yang Anda cari mungkin sudah tidak tersedia atau telah dihapus.</p><a href="'.BASE_URL.'/nebeng/find_ride" class="mt-6 inline-block bg-indigo-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-indigo-700">Kembali ke Daftar Tumpangan</a></div>';
    include_once __DIR__ . '/../../templates/footer.php';
    exit(); // Hentikan eksekusi skrip sepenuhnya.
}

// Cek apakah pengguna yang sedang login sudah memiliki booking untuk tumpangan ini
$hasBooking = false;
if ($auth->isLoggedIn()) {
    $hasBooking = $service->hasNebengBooking($ride['id'], $auth->getUserId());
}

$pageTitle = "Detail Tumpangan";
include_once __DIR__ . '/../../templates/header.php';
?>

<section class="max-w-4xl mx-auto">
    <div class="bg-white p-8 rounded-2xl shadow-xl">
        <!-- Menampilkan pesan notifikasi (flash message) jika ada -->
        <?php
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            $alertTypeClass = $flash['type'] === 'success' 
                ? 'bg-green-100 border-green-500 text-green-700' 
                : 'bg-red-100 border-red-500 text-red-700';
            echo "
            <div class='border-l-4 {$alertTypeClass} p-4 mb-6 rounded-r-lg' role='alert'>
                <p class='font-bold'>" . ucfirst($flash['type']) . "</p>
                <p>{$flash['message']}</p>
            </div>";
            unset($_SESSION['flash_message']);
        }
        ?>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Info Pengemudi -->
            <div class="md:col-span-1 text-center">
                <img src="<?php echo BASE_URL . '/uploads/profiles/' . ($ride['driver_picture'] ?? 'default.png'); ?>" alt="Foto <?php echo e($ride['driver_name'] ?? ''); ?>" class="w-32 h-32 rounded-full object-cover mx-auto mb-4 border-4 border-white shadow-lg">
                <h2 class="text-xl font-bold"><?php echo e($ride['driver_name'] ?? 'Nama Pengemudi'); ?></h2>
                <p class="text-gray-600 text-sm">Pengemudi</p>
            </div>

            <!-- Kolom Kanan: Detail Perjalanan -->
            <div class="md:col-span-2">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Detail Perjalanan</h1>
                <div class="space-y-4 text-gray-700">
                    <div class="flex items-start">
                        <span class="w-24 font-semibold">Dari</span>
                        <span>: <?php echo e($ride['origin'] ?? '-'); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-24 font-semibold">Ke</span>
                        <span>: <?php echo e($ride['destination'] ?? '-'); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-24 font-semibold">Berangkat</span>
                        <span>: <?php echo format_indonesian_date($ride['departure_time'] ?? ''); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-24 font-semibold">Sisa Kursi</span>
                        <span class="font-bold text-xl text-indigo-600">: <?php echo $ride['available_seats'] ?? 0; ?></span>
                    </div>
                    <?php if (!empty($ride['notes'])): ?>
                        <div class="pt-2">
                            <p class="font-semibold">Catatan dari Pengemudi:</p>
                            <blockquote class="text-sm bg-gray-50 p-4 rounded-lg border-l-4 border-gray-200 mt-2">
                                <?php echo e($ride['notes']); ?>
                            </blockquote>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Aksi: Ambil Kursi, Chat, dan Donasi -->
        <div class="mt-10 border-t pt-8 grid sm:grid-cols-2 gap-6">
            <!-- Kolom Aksi Utama -->
            <div class="bg-blue-50 p-6 rounded-lg">
                <h4 class="text-lg font-bold text-blue-800 mb-2">Aksi Tumpangan</h4>
                
                <?php if ($hasBooking): ?>
                    <!-- Tampilan JIKA SUDAH booking -->
                    <p class="text-sm text-green-700 mb-4">Anda sudah mendapatkan kursi! Silakan hubungi pengemudi untuk koordinasi.</p>
                    <a href="<?php echo BASE_URL . '/nebeng/chat/' . $ride['id']; ?>" target="_blank" class="inline-block w-full text-center bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 font-bold shadow-sm transition-all">
                        Chat dengan Pengemudi
                    </a>
                <?php elseif (($ride['available_seats'] ?? 0) > 0): ?>
                    <!-- Tampilan JIKA BELUM booking dan kursi TERSEDIA -->
                    <p class="text-sm text-blue-700 mb-4">Ambil kursimu sekarang sebelum kehabisan. Anda bisa chat setelah ini.</p>
                    <form action="<?php echo BASE_URL; ?>/nebeng/booking" method="POST">
                        <input type="hidden" name="ride_id" value="<?php echo $ride['id']; ?>">
                        <button type="submit" class="inline-block w-full text-center bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 font-bold shadow-sm transition-all">
                            Ambil 1 Kursi
                        </button>
                    </form>
                <?php else: ?>
                    <!-- Tampilan jika kursi HABIS -->
                    <p class="text-sm font-semibold text-red-700 p-3 bg-red-100 rounded-lg text-center">Kursi sudah penuh!</p>
                <?php endif; ?>

            </div>
            <!-- Aksi Donasi -->
            <div class="bg-green-50 p-6 rounded-lg">
                <h4 class="text-lg font-bold text-green-800 mb-2">Beri Donasi</h4>
                <p class="text-sm text-green-700 mb-4">Dukung pengemudi dengan donasi seikhlasnya sebagai tanda terima kasih.</p>
                <form action="<?php echo BASE_URL; ?>/nebeng/payment_handler" method="POST">
                    <input type="hidden" name="ride_id" value="<?php echo $ride['id']; ?>">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                        <input type="number" name="amount" placeholder="Jumlah Donasi" class="form-input pl-8" required>
                    </div>
                    <button type="submit" class="mt-3 w-full text-center bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 font-bold shadow-sm transition-all">
                        Bayar Seikhlasnya
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php include_once __DIR__ . '/../../templates/footer.php'; ?>
