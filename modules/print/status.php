<?php
// File: modules/print/status.php
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

$pageTitle = "Status Pekerjaan Cetak";
require_once __DIR__ . '/../../functions/service_functions.php';
$service = new Service();
$printJobs = $service->getPrintJobsByUserId($auth->getUserId());

include_once __DIR__ . '/../../templates/header.php';
?>

<section>
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-gray-900">Status Pekerjaan Cetak Saya</h1>
        <p class="mt-2 text-lg text-gray-600">Lacak semua dokumen yang pernah Anda titipkan untuk dicetak di sini.</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-6 font-semibold text-sm text-gray-600 uppercase tracking-wider">Nama File</th>
                        <th class="text-left py-3 px-6 font-semibold text-sm text-gray-600 uppercase tracking-wider">Tanggal Unggah</th>
                        <th class="text-center py-3 px-6 font-semibold text-sm text-gray-600 uppercase tracking-wider">Jumlah</th>
                        <th class="text-left py-3 px-6 font-semibold text-sm text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-6 font-semibold text-sm text-gray-600 uppercase tracking-wider">Dikerjakan oleh</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 divide-y divide-gray-200">
                    <?php if (empty($printJobs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500">
                                <p>Anda belum pernah menitipkan file untuk dicetak.</p>
                                <a href="<?php echo BASE_URL; ?>/print/upload" class="mt-2 inline-block text-indigo-600 font-semibold hover:underline">Mulai titip cetak sekarang!</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($printJobs as $job): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-6 font-medium"><?php echo e($job['file_path']); ?></td>
                                <td class="py-4 px-6 text-sm text-gray-600"><?php echo format_indonesian_date($job['created_at']); ?></td>
                                <td class="py-4 px-6 text-center"><?php echo $job['copies']; ?></td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full
                                        <?php 
                                            switch($job['status']) {
                                                case 'completed': echo 'bg-green-100 text-green-800'; break;
                                                case 'printing': echo 'bg-blue-100 text-blue-800'; break;
                                                case 'accepted': echo 'bg-yellow-100 text-yellow-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            }
                                        ?>">
                                        <?php echo ucfirst($job['status']); ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-600"><?php echo e($job['provider_name'] ?? 'Menunggu konfirmasi'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>
