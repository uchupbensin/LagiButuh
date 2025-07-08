<?php
// File: modules/psychologist/detail.php
// Menampilkan detail psikolog, ulasan, dan form booking.

// Ambil ID psikolog dari URL (dari $param di index.php)
if (!isset($param) || !is_numeric($param)) {
    redirect(BASE_URL . '/psychologist/list');
}

require_once __DIR__ . '/../../functions/service_functions.php';
$service = new Service();
$psychologist = $service->getPsychologistDetailsById($param);

if (!$psychologist) {
    http_response_code(404);
    include_once __DIR__ . '/../../templates/errors/404.php'; // Tampilkan halaman 404
    exit();
}

$pageTitle = "Detail " . e($psychologist['full_name']);
include_once __DIR__ . '/../../templates/header.php';
?>

<div class="bg-white p-6 md:p-10 rounded-2xl shadow-xl">
    <div class="grid lg:grid-cols-3 gap-8 lg:gap-12">
        <!-- Kolom Kiri: Info & Booking -->
        <div class="lg:col-span-1">
            <div class="sticky top-28">
                <div class="text-center">
                    <img src="<?php echo BASE_URL . '/uploads/profiles/' . ($psychologist['profile_picture'] ?? 'default.png'); ?>" alt="Foto <?php echo e($psychologist['full_name']); ?>" class="w-32 h-32 rounded-full object-cover mx-auto mb-4 border-4 border-white shadow-lg">
                    <h2 class="text-2xl font-bold"><?php echo e($psychologist['full_name']); ?></h2>
                    <p class="text-indigo-600 font-semibold"><?php echo e($psychologist['specialization']); ?></p>
                </div>
                
                <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-bold text-center mb-4">Booking Jadwal Konsultasi</h3>
                    <?php if ($auth->isLoggedIn()): ?>
                        <form action="<?php echo BASE_URL; ?>/psychologist/booking" method="POST" class="space-y-4">
                            <input type="hidden" name="psychologist_id" value="<?php echo $psychologist['id']; ?>">
                            <div>
                                <label for="schedule_time" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal & Jam</label>
                                <input type="datetime-local" id="schedule_time" name="schedule_time" class="form-input" required>
                            </div>
                            <div class="text-center text-gray-800">
                                <p class="font-semibold">Tarif:</p>
                                <p class="text-2xl font-bold text-green-600">Rp <?php echo number_format($psychologist['hourly_rate'], 0, ',', '.'); ?></p>
                            </div>
                            <button type="submit" class="w-full btn-primary">
                                Booking Sekarang
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-r-lg" role="alert">
                            <p>Anda harus <a href="<?php echo BASE_URL; ?>/login" class="font-bold underline">login</a> untuk booking.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

<h3 class="text-2xl font-bold border-b pb-3 my-6">Ulasan dari Klien (<?php echo count($reviews); ?>)</h3>
<div class="space-y-8">
    <?php if (empty($reviews)): ?>
        <p class="text-gray-500 italic">Belum ada ulasan untuk psikolog ini.</p>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="flex items-start gap-4">
                <img src="<?php echo BASE_URL . '/uploads/profiles/' . ($review['reviewer_picture'] ?? 'default.png'); ?>" alt="Foto" class="w-12 h-12 rounded-full object-cover">
                <div>
                    <div class="flex items-center mb-1">
                        <span class="font-semibold"><?php echo e($review['reviewer_name']); ?></span>
                        <span class="text-gray-400 mx-2">&bull;</span>
                        <span class="text-sm text-gray-500"><?php echo date('d F Y', strtotime($review['created_at'])); ?></span>
                    </div>
                    <div class="flex items-center">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <svg class="w-5 h-5 <?php echo $i < $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <?php endfor; ?>
                    </div>
                    <p class="text-gray-600 mt-2"><?php echo e($review['review_text']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>
