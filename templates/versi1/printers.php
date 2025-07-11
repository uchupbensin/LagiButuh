<?php
// File: modules/print/printers.php
$pageTitle = "Pilih Anggota Komunitas";

// Memuat semua fungsi inti
require_once __DIR__ . '/../../functions/service_functions.php';
$auth = new Auth(); // <-- DITAMBAHKAN: Untuk memeriksa sesi login

// Pastikan pengguna sudah login untuk bisa melihat halaman ini
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

$service = new Service();
$currentUserId = $auth->getUserId(); // <-- DITAMBAHKAN: Ambil ID pengguna yang sedang login
$providers = $service->getAvailablePrinterProviders($currentUserId); // <-- DIPERBARUI: Kirim ID pengguna ke fungsi

include_once __DIR__ . '/../../templates/header.php';
?>

<section class="bg-[#F9FAFB] px-4 py-16 font-sans">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">Pilih Anggota Komunitas</h1>
            <p class="mt-2 text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
                Berikut anggota komunitas yang siap membantu mencetak dokumenmu. Hubungi mereka untuk konfirmasi harga dan pengambilan.
            </p>
        </div>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <?php
            $flash = $_SESSION['flash_message'];
            $alertClass = $flash['type'] === 'success' 
                ? 'bg-green-100 border-green-500 text-green-700' 
                : 'bg-red-100 border-red-500 text-red-700';
            ?>
            <div class="border-l-4 <?php echo $alertClass; ?> p-4 mb-8 rounded-lg shadow-sm max-w-3xl mx-auto text-sm">
                <p class="font-bold"><?php echo ucfirst($flash['type']); ?></p>
                <p><?php echo $flash['message']; ?></p>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($providers)): ?>
                <div class="col-span-full text-center py-12 px-6 bg-white rounded-xl shadow-md">
                    <p class="text-gray-500">Saat ini belum ada pengguna lain yang terdaftar di sistem.</p>
                </div>
            <?php else: ?>
                <?php foreach ($providers as $provider): ?>
                    <div class="bg-white p-6 rounded-2xl shadow-md text-center transition duration-300 hover:shadow-lg">
                        <img src="<?php echo BASE_URL . '/uploads/profiles/' . ($provider['profile_picture'] ?? 'default.png'); ?>" 
                             alt="Foto <?php echo htmlspecialchars($provider['full_name'] ?? 'Pengguna'); ?>" 
                             class="w-20 h-20 rounded-full object-cover mx-auto mb-4 border-2 border-[#D6C8FF]">

                        <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($provider['full_name'] ?? 'Nama Tidak Tersedia'); ?></h3>
                        <p class="text-sm text-gray-500 mb-4">Anggota Komunitas</p> <div class="space-y-3">
                            <a href="https://wa.me/<?php echo htmlspecialchars($provider['phone'] ?? ''); ?>" target="_blank"
                               class="inline-flex items-center justify-center w-full bg-[#25D366] text-white font-semibold px-6 py-3 rounded-full shadow hover:bg-green-700 transition duration-200 text-sm">
                                <i class="fab fa-whatsapp mr-2"></i> Hubungi via WhatsApp
                            </a>
                            <a href="mailto:<?php echo htmlspecialchars($provider['email'] ?? ''); ?>"
                               class="inline-flex items-center justify-center w-full bg-[#5C3AC7] text-white font-semibold px-6 py-3 rounded-full shadow hover:bg-[#47289f] transition duration-200 text-sm">
                                <i class="fas fa-envelope mr-2"></i> Hubungi via Email
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="mt-12 text-center bg-yellow-50 border border-yellow-200 p-6 rounded-xl max-w-4xl mx-auto text-sm">
            <p class="text-yellow-800">
                <strong>Langkah Selanjutnya:</strong> Setelah menghubungi salah satu anggota, mereka dapat mengonfirmasi pekerjaan cetak Anda. 
                Lihat status pesanan di halaman 
                <a href="<?php echo BASE_URL; ?>/print/status" class="font-semibold text-yellow-900 hover:underline">
                    Status Cetak
                </a>.
            </p>
        </div>
    </div>
</section>

<?php 
include_once __DIR__ . '/../../templates/footer.php'; 
?>