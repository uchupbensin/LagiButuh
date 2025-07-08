<?php
// File: modules/print/upload.php
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

$pageTitle = "Titip Cetak Dokumen";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../functions/service_functions.php';
    $service = new Service();

    $userId = $auth->getUserId();
    $copies = filter_input(INPUT_POST, 'copies', FILTER_VALIDATE_INT);
    $notes = sanitize_input($_POST['notes']);

    if (!$copies || !isset($_FILES['document']) || $_FILES['document']['error'] != 0) {
        $error = "Mohon unggah file dan isi jumlah salinan dengan benar.";
    } else {
        $originalFilePath = $_FILES['document']['tmp_name'];
        $originalFileName = basename($_FILES['document']['name']);
        $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
        $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedTypes)) {
            $error = "Tipe file tidak diizinkan. Hanya PDF, DOC, DOCX, PPT, PPTX.";
        } else {
            $result = $service->createPrintJob($userId, $originalFilePath, $originalFileName, $copies, $notes);

            if (is_numeric($result)) {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Dokumen berhasil diunggah! Silakan pilih penyedia jasa cetak.'];
                redirect(BASE_URL . '/print/printers');
            } else {
                $error = $result;
            }
        }
    }
}

include_once __DIR__ . '/../../templates/header.php';
?>

<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg">
    <h1 class="text-3xl font-bold text-center mb-2">Titip Cetak Dokumen</h1>
    <p class="text-center text-gray-600 mb-8">Butuh cetak tugas mendadak? Unggah file Anda di sini dan biarkan komunitas membantu.</p>

    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/print/upload" method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="document" class="block text-sm font-medium text-gray-700 mb-1">Pilih Dokumen</label>
            <input type="file" id="document" name="document" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
            <p class="text-xs text-gray-500 mt-1">Tipe file yang didukung: PDF, DOC, DOCX, PPT, PPTX.</p>
        </div>
        <div>
            <label for="copies" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Salinan (Copies)</label>
            <input type="number" id="copies" name="copies" min="1" value="1" class="form-input" required>
        </div>
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Contoh: Jilid spiral, print warna halaman 1-5, sisanya hitam putih." class="form-input"></textarea>
        </div>
        <div>
            <button type="submit" class="w-full btn-primary">
                Unggah dan Cari Penyedia
            </button>
        </div>
    </form>
</div>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>