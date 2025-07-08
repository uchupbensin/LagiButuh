<?php
// File: modules/jastip/create_order.php
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

$pageTitle = "Titip Beli Makanan";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../functions/service_functions.php';
    $service = new Service();

    $userId = $auth->getUserId();
    $itemDescription = sanitize_input($_POST['item_description']);
    $purchaseLocation = sanitize_input($_POST['purchase_location']);
    $deliveryLocation = sanitize_input($_POST['delivery_location']);
    $estimatedPrice = filter_input(INPUT_POST, 'estimated_price', FILTER_VALIDATE_FLOAT);

    if (empty($itemDescription) || empty($purchaseLocation) || empty($deliveryLocation)) {
        $error = "Semua field wajib diisi.";
    } else {
        $result = $service->createJastipOrder($userId, $itemDescription, $purchaseLocation, $deliveryLocation, $estimatedPrice);
        if (is_numeric($result)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Pesanan jastip berhasil dibuat!'];
            redirect(BASE_URL . '/jastip/list_orders');
        } else {
            $error = $result;
        }
    }
}

include_once __DIR__ . '/../../templates/header.php';
?>

<section class="bg-[#F9FAFB] py-12 font-sans">
    <div class="max-w-6xl mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">Buat Pesanan Jastip</h1>
            <p class="mt-2 text-gray-600 text-base md:text-lg">Lapar tapi mager? Biarkan komunitas yang bantu titip belikan makananmu.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/jastip/create_order" method="POST" class="bg-white p-8 rounded-xl shadow-md space-y-6">
            <div>
                <label for="item_description" class="block text-sm font-medium text-gray-700 mb-1">Apa yang ingin kamu beli?</label>
                <textarea id="item_description" name="item_description" rows="4" placeholder="Contoh: 2x Nasi Goreng Gila, 1x Es Teh Manis" class="form-input w-full" required></textarea>
            </div>
            <div>
                <label for="purchase_location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Pembelian</label>
                <input type="text" id="purchase_location" name="purchase_location" placeholder="Contoh: Warung Nasi Goreng Gila, Jl. Merdeka No. 5" class="form-input w-full" required>
            </div>
            <div>
                <label for="delivery_location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Pengantaran</label>
                <input type="text" id="delivery_location" name="delivery_location" placeholder="Contoh: Kosan Pelangi, Kamar 201" class="form-input w-full" required>
            </div>
            <div>
                <label for="estimated_price" class="block text-sm font-medium text-gray-700 mb-1">Perkiraan Harga Barang (Rp, opsional)</label>
                <input type="number" id="estimated_price" name="estimated_price" placeholder="Contoh: 35000" class="form-input w-full">
            </div>
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <a href="<?= BASE_URL ?>/jastip/list_orders" class="text-sm text-gray-600 hover:underline">← Kembali ke Daftar</a>
                <button type="submit" class="bg-[#5C3AC7] text-white font-semibold px-6 py-3 rounded-xl hover:bg-[#47289f] transition w-full md:w-auto">
                    Posting Pesanan
                </button>
            </div>
        </form>
    </div>
</section>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>
