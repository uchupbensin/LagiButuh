<?php
// File: modules/laptop/list.php
$pageTitle = "Pinjam Laptop";
require_once __DIR__ . '/../../functions/service_functions.php';

$service = new Service();
$laptops = $service->getAllAvailableLaptops();

include_once __DIR__ . '/../../templates/header.php';
?>

<section class="bg-[#F9FAFB] px-4 py-8 font-sans">
    <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">Pinjam Laptop Mendesak</h1>
                <p class="mt-1 md:mt-2 text-base md:text-lg text-gray-600">Perangkatmu bermasalah? Cari laptop pinjaman sementara dari komunitas.</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/laptop/add" class="bg-[#5C3AC7] text-white font-semibold px-6 py-3 rounded-xl shadow hover:bg-[#47289f] transition duration-200 ease-in-out w-full md:w-auto text-center">
                + Sewakan Laptopmu
            </a>
        </div>

        <!-- Laptops Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($laptops)): ?>
                <div class="col-span-full text-center py-12 px-6 bg-white rounded-xl shadow-md">
                    <p class="text-gray-500">Saat ini belum ada laptop yang bisa dipinjam.</p>
                    <p class="text-gray-400 mt-2">Jadilah yang pertama menyewakan laptopmu!</p>
                    <a href="<?php echo BASE_URL; ?>/laptop/add" class="bg-[#5C3AC7] text-white font-semibold px-6 py-3 rounded-xl hover:bg-[#47289f] transition duration-200 ease-in-out inline-block mt-4">
                        + Sewakan Laptopmu
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($laptops as $laptop): ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg">
                        <div class="h-48 overflow-hidden bg-gray-100">
                            <img src="<?php echo BASE_URL . '/uploads/laptop_images/' . ($laptop['image_path'] ?? 'default_laptop.png'); ?>" 
                                 alt="<?php echo e($laptop['brand'] . ' ' . $laptop['model']); ?>" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-gray-800 truncate"><?php echo e($laptop['brand'] . ' ' . $laptop['model']); ?></h3>
                            <p class="text-sm text-gray-500 mt-1">Milik: <?php echo e($laptop['owner_name']); ?></p>
                            
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-indigo-600 font-semibold text-sm">
                                    Rp <?php echo number_format($laptop['rental_rate_per_day'], 0, ',', '.'); ?>
                                    <span class="text-gray-500 font-normal">/hari</span>
                                </span>
                                <a href="<?php echo BASE_URL . '/laptop/detail/' . $laptop['id']; ?>" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                    Detail & Sewa
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