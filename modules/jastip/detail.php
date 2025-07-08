<?php
// File: modules/jastip/detail.php
if (!isset($param) || !is_numeric($param)) {
    redirect(BASE_URL . '/jastip/list_orders');
}

require_once __DIR__ . '/../../functions/service_functions.php';
$auth = new Auth();
$service = new Service();

$order = $service->getJastipOrderById($param);
if (!$order) {
    http_response_code(404);
    echo "Pesanan tidak ditemukan.";
    exit();
}

$pageTitle = "Detail Pesanan Jastip";
include_once __DIR__ . '/../../templates/header.php';
?>

<section class="max-w-3xl mx-auto px-4 py-10 font-sans">
    <div class="bg-white shadow-md rounded-2xl p-6">

        <!-- Toast Notifikasi -->
        <?php if (isset($_GET['success']) || isset($_GET['error'])): ?>
            <div id="toast-alert" class="fixed top-6 right-6 z-50 px-4 py-3 rounded-xl shadow-lg text-sm transition-all duration-300
                <?= isset($_GET['success']) ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300' ?>">
                <?= isset($_GET['success']) ? '✅ ' . e($_GET['success']) : '⚠️ ' . e($_GET['error']) ?>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('toast-alert');
                    if (toast) toast.classList.add('opacity-0');
                }, 3500);
            </script>
        <?php endif; ?>

        <h1 class="text-2xl font-bold text-gray-800 mb-4">🧾 Detail Pesanan</h1>

        <div class="text-sm text-gray-700 space-y-2">
            <p><strong>Deskripsi Barang:</strong> <?= e($order['item_description']); ?></p>
            <p><strong>Lokasi Pembelian:</strong> <?= e($order['purchase_location']); ?></p>
            <p><strong>Tujuan Pengantaran:</strong> <?= e($order['delivery_location']); ?></p>
            <p><strong>Estimasi Harga:</strong> Rp<?= number_format($order['estimated_price'], 0, ',', '.'); ?></p>
            <p><strong>Nama Pemesan:</strong> <?= e($order['orderer_name']); ?></p>
            <p><strong>Runner:</strong> <?= $order['runner_name'] ?? '<em>Belum diambil</em>'; ?></p>
            <p>
                <strong>Status:</strong>
                <?php
                $colorMap = [
                    'open' => 'bg-yellow-100 text-yellow-700',
                    'accepted' => 'bg-blue-100 text-blue-700',
                    'delivered' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                    'completed' => 'bg-green-200 text-green-800'
                ];
                $status = strtolower($order['status']);
                ?>
                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-xl <?= $colorMap[$status] ?? 'bg-gray-100 text-gray-700' ?>">
                    <?= strtoupper($status) ?>
                </span>
            </p>
        </div>

        <div class="mt-6 flex flex-wrap gap-4 items-center">
            
            <?php if ($auth->isLoggedIn()): ?>
    <?php $userId = $auth->getUserId(); ?>

    <?php if ($order['status'] === 'open' && $order['user_id'] != $userId): ?>
        <!-- Ambil Pesanan -->
        <form action="<?= BASE_URL ?>/jastip/accept" method="POST">
            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
            <button type="submit" class="bg-[#5C3AC7] hover:bg-[#4A2DA5] text-white font-semibold px-6 py-3 rounded-xl shadow transition">
                ✅ Ambil Pesanan
            </button>
        </form>
    <?php endif; ?>

    <?php if ($order['status'] !== 'cancelled' && ($order['user_id'] == $userId || $order['runner_id'] == $userId)): ?>
        <!-- Batalkan Pesanan -->
        <form action="<?= BASE_URL ?>/jastip/cancel" method="POST" onsubmit="return confirm('Yakin ingin membatalkan?');">
            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-xl shadow transition">
                ❌ Batalkan Pesanan
            </button>
        </form>
    <?php endif; ?>
<?php endif; ?>


            <!-- TOMBOL TANDAI DIKIRIM (oleh runner) -->
            <?php if ($auth->getUserId() == $order['runner_id'] && $order['status'] === 'accepted'): ?>
                <form action="<?= BASE_URL ?>/jastip/deliver" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl shadow transition">
                        📦 Tandai Sudah Dikirim
                    </button>
                </form>
            <?php endif; ?>

            <!-- TOMBOL KONFIRMASI SELESAI (oleh pemesan) -->
            <?php if ($auth->getUserId() == $order['user_id'] && $order['status'] === 'delivered'): ?>
                <form action="<?= BASE_URL ?>/jastip/complete" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-xl shadow transition">
                        ✅ Tandai Selesai
                    </button>
                </form>
            <?php endif; ?>

            <!-- Link kembali -->
            <a href="<?= BASE_URL ?>/jastip/list_orders" class="text-sm text-[#5C3AC7] hover:underline">
                &larr; Kembali ke Daftar Pesanan
            </a>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>
