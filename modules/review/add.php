<?php
// File: modules/review/add.php
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

// URL format: /review/add/psychologist/123 (123 adalah ID booking)
$serviceType = $param ?? null;
$serviceId = !empty($urlParts[3]) ? (int)$urlParts[3] : null;

if (!$serviceType || !$serviceId) {
    redirect(BASE_URL . '/profile');
}

require_once __DIR__ . '/../../functions/service_functions.php';
$service = new Service();
$userId = $auth->getUserId();

// Verifikasi apakah layanan ini bisa di-review oleh user
$isReviewable = false;
$providerId = null;
$serviceName = '';

if ($serviceType === 'psychologist') {
    $booking = $service->getBookingDetailsForChat($serviceId, $userId); // Fungsi ini bisa kita pakai ulang
    if ($booking) {
        $isReviewable = $service->isConsultationReviewable($serviceId, $userId);
        $providerId = $booking['psychologist_id'];
        $serviceName = 'Konsultasi dengan ' . $booking['psychologist_name'];
    }
}
// Tambahkan logika untuk tipe layanan lain (nebeng, laptop, dll) di sini

if (!$isReviewable) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Layanan ini tidak dapat diulas atau sudah pernah Anda ulas.'];
    redirect(BASE_URL . '/profile');
}

$pageTitle = "Beri Ulasan";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $reviewText = sanitize_input($_POST['review_text']);

    if ($rating < 1 || $rating > 5) {
        $error = "Rating harus antara 1 sampai 5.";
    } else {
        $result = $service->addReview($serviceType, $serviceId, $providerId, $userId, $rating, $reviewText);
        if ($result === true) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Terima kasih! Ulasan Anda telah disimpan.'];
            redirect(BASE_URL . '/profile');
        } else {
            $error = $result;
        }
    }
}

include_once __DIR__ . '/../../templates/header.php';
?>

<style>
.rating input { display: none; }
.rating label {
    float: right;
    cursor: pointer;
    color: #ddd;
    transition: color 0.2s;
}
.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label { color: #f5b301; }
</style>

<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg">
    <h1 class="text-3xl font-bold text-center mb-2">Beri Ulasan</h1>
    <p class="text-center text-gray-600 mb-8">Bagaimana pengalamanmu saat <?php echo e($serviceName); ?>?</p>

    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/review/add/<?php echo $serviceType . '/' . $serviceId; ?>" method="POST" class="space-y-6">
        <div class="text-center">
            <label class="block text-sm font-medium text-gray-700 mb-2">Rating Anda</label>
            <div class="rating inline-block text-4xl">
                <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="Luar biasa">&#9733;</label>
                <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Bagus">&#9733;</label>
                <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Cukup">&#9733;</label>
                <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Kurang">&#9733;</label>
                <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Buruk">&#9733;</label>
            </div>
        </div>
        <div>
            <label for="review_text" class="block text-sm font-medium text-gray-700 mb-1">Ulasan Anda</label>
            <textarea id="review_text" name="review_text" rows="5" placeholder="Ceritakan pengalaman Anda secara detail..." class="form-input" required></textarea>
        </div>
        <div>
            <button type="submit" class="w-full btn-primary">Kirim Ulasan</button>
        </div>
    </form>
</div>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>
