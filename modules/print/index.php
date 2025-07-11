<?php
// File: modules/print/index.php
$pageTitle = "Jasa Cetak Komunitas";
require_once __DIR__ . '/../../functions/service_functions.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

$service = new Service();
$error = '';

// --- LOGIKA UNTUK UPLOAD (TITIP PRINT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_print_job'])) {
    $userId = $auth->getUserId();
    $copies = filter_input(INPUT_POST, 'copies', FILTER_VALIDATE_INT);
    $notes = sanitize_input($_POST['notes']);

    if (!$copies || !isset($_FILES['document']) || $_FILES['document']['error'] != 0) {
        $error = "Mohon unggah file dan isi jumlah salinan dengan benar.";
    } else {
        $originalFilePath = $_FILES['document']['tmp_name'];
        $originalFileName = basename($_FILES['document']['name']);
        
        // Menggunakan ?? untuk memberikan nilai default yang aman
        $deliveryMethod = sanitize_input($_POST['delivery_method'] ?? 'diambil');
        $deliveryAddress = ($deliveryMethod === 'diantar') ? sanitize_input($_POST['delivery_address'] ?? '') : null;
        $pickupNotes = ($deliveryMethod === 'diambil') ? sanitize_input($_POST['pickup_notes'] ?? '') : null;
        
        $result = $service->createPrintJob($userId, $originalFilePath, $originalFileName, $copies, $notes, $deliveryMethod, $deliveryAddress, $pickupNotes);

        if (is_numeric($result)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Pekerjaan berhasil diposting! Statusnya bisa Anda lihat di profil.'];
            redirect(BASE_URL . '/print');
        } else {
            $error = $result;
        }
    }
}

// --- LOGIKA UNTUK MENAMPILKAN PAPAN LOWONGAN ---
$openJobs = $service->getOpenPrintJobs();

include_once __DIR__ . '/../../templates/header.php';
?>

<section class="bg-white py-16 px-4 font-sans rounded-2xl shadow-md">
  <div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-extrabold text-center text-gray-900 mb-2">Butuh Cetak Dokumen?</h1>
    <p class="text-center text-gray-600 mb-8">Posting pekerjaan Anda di sini dan biarkan komunitas yang mengerjakan.</p>

    <?php if ($error): ?>
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
        <p><?php echo $error; ?></p>
      </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['flash_message']) && $_SESSION['flash_message']['type'] === 'success'): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <p><?php echo $_SESSION['flash_message']['message']; ?></p>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/print" method="POST" enctype="multipart/form-data" class="space-y-6">
      <div>
        <label for="document" class="block text-sm font-medium text-gray-700 mb-1">Pilih Dokumen</label>
        <input type="file" id="document" name="document" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#E9D8FD] file:text-primary hover:file:bg-[#d8c1ff]" required>
        <p class="text-xs text-gray-500 mt-1">Tipe file yang didukung: PDF, DOC, DOCX, PPT, PPTX.</p>
      </div>
      <div>
        <label for="copies" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Salinan</label>
        <input type="number" id="copies" name="copies" min="1" value="1" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" required>
      </div>
      <div>
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
        <textarea id="notes" name="notes" rows="3" placeholder="Contoh: Jilid spiral, print warna halaman 1-5." class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
      </div>
      
      <div class="border-t pt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Penyerahan Hasil</label>
        <div class="flex items-center space-x-6">
            <div class="flex items-center">
                <input id="method_diambil" name="delivery_method" type="radio" value="diambil" class="h-4 w-4 text-primary focus:ring-primary border-gray-300" checked>
                <label for="method_diambil" class="ml-2 block text-sm text-gray-900">Diambil Sendiri</label>
            </div>
            <div class="flex items-center">
                <input id="method_diantar" name="delivery_method" type="radio" value="diantar" class="h-4 w-4 text-primary focus:ring-primary border-gray-300">
                <label for="method_diantar" class="ml-2 block text-sm text-gray-900">Diantar</label>
            </div>
        </div>
      </div>

      <div id="pickup_notes_field" class="mt-4">
          <label for="pickup_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Lokasi Ambil (Opsional)</label>
          <input type="text" name="pickup_notes" id="pickup_notes" placeholder="Contoh: Ketemuan di lobi rektorat" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
      </div>

      <div id="delivery_address_field" class="mt-4" style="display: none;">
          <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengantaran Lengkap</label>
          <textarea name="delivery_address" id="delivery_address" rows="3" placeholder="Contoh: Jl. Kenangan No. 10, RT 01/RW 02. (Patokan: Sebelah warung kopi)" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm"></textarea>
      </div>
      
      <div>
        <button type="submit" name="submit_print_job" class="w-full bg-primary text-white font-semibold py-3 rounded-full shadow hover:bg-primary-dark transition duration-200">
          Posting Pekerjaan Cetak
        </button>
      </div>
    </form>
  </div>
</section>

<div class="border-t my-16"></div>

<section class="px-4 font-sans">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">Papan Lowongan Cetak</h1>
            <p class="mt-2 text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
                Ambil pekerjaan dari anggota komunitas lain untuk mendapatkan penghasilan tambahan.
            </p>
        </div>

        <?php if (isset($_SESSION['flash_message']) && $_SESSION['flash_message']['type'] === 'error'): ?>
            <div class="border-l-4 bg-red-100 border-red-500 text-red-700 p-4 mb-8 rounded-lg shadow-sm max-w-3xl mx-auto text-sm">
                <p><?php echo $_SESSION['flash_message']['message']; ?></p>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($openJobs)): ?>
                <div class="col-span-full text-center py-12 px-6 bg-white rounded-xl shadow-md">
                    <p class="text-gray-500 font-semibold">Luar biasa!</p>
                    <p class="text-gray-400 mt-2">Saat ini tidak ada pekerjaan cetak yang tersedia untuk diambil.</p>
                </div>
            <?php else: ?>
                <?php foreach ($openJobs as $job): ?>
                    <div class="bg-white p-6 rounded-2xl shadow-md flex flex-col">
                        <div class="flex items-center mb-4">
                            <img src="<?php echo BASE_URL . '/uploads/profiles/' . htmlspecialchars($job['requester_picture'] ?? 'default.png'); ?>" alt="Foto" class="w-12 h-12 rounded-full object-cover mr-4">
                            <div>
                                <h3 class="font-bold text-gray-800"><?php echo htmlspecialchars($job['requester_name'] ?? 'Pengguna'); ?></h3>
                                <p class="text-xs text-gray-500">Diposting: <?php echo date('d M Y, H:i', strtotime($job['created_at'])); ?></p>
                            </div>
                        </div>
                        <div class="border-t border-b py-4 my-4 flex-grow">
                            <p class="text-sm text-gray-600"><strong>Jumlah:</strong> <?php echo htmlspecialchars($job['copies'] ?? '0'); ?> salinan</p>
                            <p class="text-sm text-gray-600 mt-2"><strong>Catatan:</strong></p>
                            <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded-md"><?php echo !empty($job['notes']) ? nl2br(htmlspecialchars($job['notes'])) : 'Tidak ada catatan.'; ?></p>
                            
                        <div class="mt-3 pt-3 border-t">
                                <strong class="text-sm text-gray-500">Metode Penyerahan:</strong>
                                <?php if (isset($job['delivery_method']) && $job['delivery_method'] == 'diantar'): ?>
                                    <p class="text-sm font-semibold text-blue-600">Diantar</p>
                                    <p class="text-xs text-gray-600 break-words"><?= htmlspecialchars($job['delivery_address'] ?? 'Alamat tidak spesifik.') ?></p>
                                <?php else: ?>
                                    <p class="text-sm font-semibold text-green-600">Diambil Sendiri</p>
                                    <p class="text-xs text-gray-600 break-words"><?= htmlspecialchars($job['pickup_notes'] ?? 'Lokasi akan disepakati.') ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/print/accept_job?id=<?php echo $job['id']; ?>" class="mt-auto block w-full text-center bg-primary text-white font-semibold px-6 py-3 rounded-full shadow hover:bg-primary-dark transition duration-200">
                           Ambil Pekerjaan Ini
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deliveryMethodRadios = document.querySelectorAll('input[name="delivery_method"]');
        const deliveryAddressField = document.getElementById('delivery_address_field');
        const pickupNotesField = document.getElementById('pickup_notes_field');

        function toggleFields() {
            if (document.querySelector('input[name="delivery_method"]:checked').value === 'diantar') {
                deliveryAddressField.style.display = 'block';
                pickupNotesField.style.display = 'none';
            } else {
                deliveryAddressField.style.display = 'none';
                pickupNotesField.style.display = 'block';
            }
        }

        deliveryMethodRadios.forEach(radio => {
            radio.addEventListener('change', toggleFields);
        });

        // Initial check on page load
        toggleFields();
    });
</script>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>