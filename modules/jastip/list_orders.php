<?php
// File: modules/jastip/list_orders.php
$pageTitle = "Daftar Pesanan Jastip";
require_once __DIR__ . '/../../functions/service_functions.php';

$service = new Service();
$orders = $service->getOpenJastipOrders();

include_once __DIR__ . '/../../templates/header.php';
?>

<section class="px-4 py-8 bg-[#F9FAFB] font-sans">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900">Titip Beli Makanan</h1>
                <p class="mt-2 text-lg text-gray-600">Lagi senggang? Bantu sesama dengan mengambil pesanan dan dapatkan tip!</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/jastip/create_order" class="hidden md:inline-block bg-[#5C3AC7] text-white font-semibold px-6 py-3 rounded-xl shadow hover:bg-[#47289f] transition duration-200 ease-in-out">
                + Buat Pesanan Jastip
            </a>
        </div>

        <!-- Daftar atau Pesan Kosong -->
        <div class="space-y-6">
            <?php if (empty($orders)): ?>
                <div class="text-center py-12 px-6 bg-white rounded-xl shadow-md">
                    <p class="text-gray-500 text-lg">Tidak ada pesanan jastip yang tersedia saat ini.</p>
                    <p class="text-gray-400 mt-2">Jadilah yang pertama membuat pesanan!</p>
                    <a href="<?php echo BASE_URL; ?>/jastip/create_order" class="mt-6 inline-block bg-[#5C3AC7] text-white font-semibold px-6 py-3 rounded-xl hover:bg-[#47289f] transition duration-200 ease-in-out">
                        + Buat Pesanan Jastip
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="bg-white rounded-xl shadow-lg p-5 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                        <div class="flex flex-col sm:flex-row items-start gap-5">
                            <!-- Foto Pemesan -->
                            <img src="<?php echo BASE_URL . '/uploads/profiles/' . ($order['user_picture'] ?? 'default.png'); ?>" 
                                 alt="Pemesan: <?php echo e($order['user_name']); ?>" 
                                 class="w-12 h-12 rounded-full object-cover border border-gray-200">

                            <!-- Deskripsi -->
                            <div class="flex-grow">
                                <p class="font-bold text-lg text-gray-800">
                                    <?php echo e(truncate_text($order['item_description'], 80)); ?>
                                </p>
                                <div class="flex items-center text-sm text-gray-500 mt-2 flex-wrap gap-x-4 gap-y-1">
                                    <span>Dipesan oleh: <span class="font-semibold text-gray-700"><?php echo e($order['user_name']); ?></span></span>
                                    <span class="hidden sm:inline">&bull;</span>
                                    <span>Beli di: <span class="font-semibold text-gray-700"><?php echo e($order['purchase_location']); ?></span></span>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="flex-shrink-0 w-full sm:w-auto text-right">
                                <a href="<?php echo BASE_URL . '/jastip/detail/' . $order['id']; ?>" 
                                   class="inline-block w-full sm:w-auto bg-indigo-600 text-white font-semibold px-5 py-3 rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                    Lihat & Ambil Pesanan
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>