<?php
// File: modules/laptop/detail.php
if (!isset($param) || !is_numeric($param)) {
    redirect(BASE_URL . '/laptop/list');
}

require_once __DIR__ . '/../../functions/service_functions.php';
$auth = new Auth();
$service = new Service();
$laptop = $service->getLaptopDetailsById($param);

if (!$laptop) {
    http_response_code(404);
    echo "Laptop tidak ditemukan.";
    exit();
}

$pageTitle = "Detail Laptop " . e($laptop['brand'] . ' ' . $laptop['model']);
$isOwner = $auth->isLoggedIn() && $auth->getUserId() === $laptop['owner_id'];

include_once __DIR__ . '/../../templates/header.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/output.css">

<section class="max-w-5xl mx-auto px-4 py-8 font-sans">
    <div class="bg-white p-6 md:p-10 rounded-2xl shadow-xl">

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

        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Gambar Laptop -->
            <div>
                <div class="aspect-w-16 aspect-h-10 bg-gray-100 rounded-lg overflow-hidden shadow-inner">
                    <img src="<?= BASE_URL . '/uploads/laptop_images/' . ($laptop['image_path'] ?? 'default_laptop.png'); ?>" alt="Foto <?= e($laptop['brand']); ?>" class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Detail -->
            <div>
                <p class="text-sm text-gray-500 mb-1">Milik: <?= e($laptop['owner_name']); ?></p>
                <h1 class="text-4xl font-extrabold text-gray-900"><?= e($laptop['brand'] . ' ' . $laptop['model']); ?></h1>
                
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-2 text-gray-800">Spesifikasi:</h3>
                    <div class="text-gray-700 bg-gray-50 p-4 rounded-lg prose prose-sm max-w-none">
                        <pre class="bg-transparent p-0 m-0 font-sans whitespace-pre-wrap"><?= e($laptop['specifications']); ?></pre>
                    </div>
                </div>
                
                <div class="mt-6 flex items-center justify-between bg-indigo-50 p-4 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-indigo-800">Tarif Sewa</p>
                        <p class="text-3xl font-extrabold text-indigo-600">Rp <?= number_format($laptop['rental_rate_per_day'], 0, ',', '.'); ?><span class="text-lg font-medium"> /hari</span></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-indigo-800 text-right">Status</p>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full <?= $laptop['availability_status'] === 'available' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'; ?>">
                            <?= ucfirst($laptop['availability_status']); ?>
                        </span>
                    </div>
                </div>

                <?php if ($laptop['availability_status'] === 'available'): ?>
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-xl font-bold mb-4">Sewa Laptop Ini</h3>

                        <?php if (!$auth->isLoggedIn()): ?>
                            <div class="bg-yellow-50 text-yellow-900 p-4 text-sm rounded-lg text-center">
                                Silakan 
                                <a href="<?= BASE_URL ?>/login" class="text-[#5C3AC7] font-semibold underline hover:text-[#47289f]">login</a> 
                                atau 
                                <a href="<?= BASE_URL ?>/register" class="text-[#5C3AC7] font-semibold underline hover:text-[#47289f]">daftar</a> 
                                untuk menyewa.
                            </div>

                        <?php elseif ($isOwner): ?>
                            <div class="bg-blue-50 text-blue-900 p-4 text-sm rounded-lg text-center">
                                Anda tidak dapat menyewa laptop milik sendiri.
                            </div>

                        <?php else: ?>
                            <form action="<?= BASE_URL ?>/laptop/booking" method="POST" class="space-y-4">
                                <input type="hidden" name="laptop_id" value="<?= $laptop['id']; ?>">
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                        <input type="date" name="start_date" id="start_date" class="mt-1 form-input" required>
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                                        <input type="date" name="end_date" id="end_date" class="mt-1 form-input" required>
                                    </div>
                                </div>
                                <button type="submit" class="w-full btn-primary">Booking Sekarang</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="mt-8 text-center bg-red-100 p-4 rounded-lg">
                        <p class="font-semibold text-red-800">Laptop ini sedang tidak tersedia untuk disewa.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>