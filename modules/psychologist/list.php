<?php
// File: modules/psychologist/list.php
$pageTitle = "Daftar Psikolog | LagiButuh";
$pageDescription = "Temukan psikolog profesional untuk konsultasi online. Pilih dari berbagai spesialisasi dengan harga terjangkau khusus mahasiswa.";
$pageKeywords = "konsultasi psikolog online, psikolog murah, psikolog mahasiswa, bantuan mental, kesehatan jiwa";

require_once __DIR__ . '/../../functions/service_functions.php';
$service = new Service();
$psychologists = $service->getAllPsychologists();

include_once __DIR__ . '/../../templates/header.php';
?>

<!-- Hero Section -->
<section class="bg-[#F9FAFB] py-12 font-sans">
    <div class="max-w-6xl mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Temukan Psikolog Profesional</h1>
            <p class="text-lg text-gray-600 mb-6">Pilih psikolog yang paling sesuai dengan kebutuhan Anda dan mulailah sesi konsultasi yang nyaman dan aman.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#psychologists" class="inline-block bg-[#5C3AC7] text-white font-semibold px-6 py-3 rounded-xl shadow hover:bg-[#47289f] transition duration-200 ease-in-out">
                    Lihat Psikolog
                </a>
                <a href="<?= BASE_URL ?>/faq#konsultasi" class="inline-block border border-[#5C3AC7] text-[#5C3AC7] font-semibold px-6 py-3 rounded-xl hover:bg-[#F3F0FF] transition duration-200 ease-in-out">
                    FAQ Konsultasi
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Psychologists List -->
<section id="psychologists" class="py-12 bg-white font-sans">
    <div class="max-w-6xl mx-auto px-4">
        <div class="mb-8 text-center">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-2">Psikolog Tersedia</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Tim profesional kami siap membantu Anda melalui berbagai tantangan mental dan emosional.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($psychologists)): ?>
                <div class="col-span-full bg-white p-8 rounded-xl shadow-md text-center border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Belum ada psikolog tersedia</h3>
                    <p class="text-gray-500 mb-4">Kami sedang memproses verifikasi psikolog baru.</p>
                    <a href="<?= BASE_URL ?>/contact" class="inline-block text-[#5C3AC7] hover:text-[#47289f] text-sm font-semibold">
                        Hubungi kami untuk info lebih lanjut
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($psychologists as $psy): ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-200 hover:shadow-lg border border-gray-200">
                        <div class="p-5">
                            <div class="flex items-start">
                                <div class="relative flex-shrink-0">
                                    <img src="<?= BASE_URL . '/uploads/profiles/' . ($psy['profile_picture'] ?? 'default.png'); ?>" 
                                         alt="Foto <?= e($psy['full_name']); ?>" 
                                         class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-sm">
                                    <?php if ($psy['is_online']): ?>
                                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-bold text-gray-800"><?= e($psy['full_name']); ?></h3>
                                    <p class="text-sm font-semibold text-[#5C3AC7] mb-1"><?= e($psy['specialization']); ?></p>
                                    <div class="flex items-center mb-1">
                                        <span class="ml-1 text-xs font-medium text-gray-500"><?= number_format($psy['avg_rating'] ?? 0, 1); ?> (<?= $psy['review_count'] ?? 0 ?>)</span>
                                    </div>
                                    <p class="text-xs text-gray-500"><?= $psy['experience_years']; ?> tahun</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-gray-200 flex justify-between items-center">
                                <div>
                                    <span class="text-xs text-gray-500 block">Mulai dari</span>
                                    <span class="text-base font-bold text-gray-800">Rp <?= number_format($psy['hourly_rate'], 0, ',', '.'); ?></span>
                                    <span class="text-xs text-gray-500">/jam</span>
                                </div>
                                <a href="<?= BASE_URL . '/psychologist/detail/' . $psy['id']; ?>" class="inline-block bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                    Lihat Detail
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