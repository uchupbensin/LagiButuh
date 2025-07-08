<?php
// File: modules/laptop/add.php
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

$pageTitle = "Sewakan Laptop Anda";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../functions/service_functions.php';
    $service = new Service();

    $ownerId = $auth->getUserId();
    $brand = sanitize_input($_POST['brand']);
    $model = sanitize_input($_POST['model']);
    $specifications = sanitize_input($_POST['specifications']);
    $rentalRate = filter_input(INPUT_POST, 'rental_rate', FILTER_VALIDATE_FLOAT);

    // Upload gambar
    $imagePath = 'default_laptop.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = __DIR__ . "/../../uploads/laptop_images/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $error = "File yang diunggah bukan gambar valid.";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $error = "Ukuran gambar maksimal 2MB.";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            $error = "Maaf, hanya file JPG, JPEG, & PNG yang diizinkan.";
        } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $error = "Maaf, terjadi kesalahan saat mengunggah file.";
        } else {
            $imagePath = $fileName;
        }
    }

    if (empty($error)) {
        $result = $service->createLaptopListing($ownerId, $brand, $model, $specifications, $imagePath, $rentalRate);
        if (is_numeric($result)) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Laptop berhasil didaftarkan untuk disewakan!'];
            redirect(BASE_URL . '/laptop/list');
        } else {
            $error = $result;
        }
    }
}

include_once __DIR__ . '/../../templates/header.php';
?>
<!-- Ganti ini biar backgorund putih otomatis menyesuaikan -->
<section class="bg-[#F9FAFB] py-12 font-sans">
    <div class="max-w-6xl mx-auto px-4">
<!-- Hero Section -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">Sewakan Laptop Anda</h1>
            <p class="mt-2 text-gray-600 text-base md:text-lg">Punya laptop nganggur? Sewakan dan bantu mahasiswa lain sekaligus dapat penghasilan tambahan.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/laptop/add" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-xl shadow-md space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Merek</label>
                    <input type="text" id="brand" name="brand" placeholder="Contoh: Dell, Apple" class="form-input w-full" required>
                </div>
                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                    <input type="text" id="model" name="model" placeholder="Contoh: XPS 13, Macbook Pro" class="form-input w-full" required>
                </div>
            </div>

            <div>
                <label for="specifications" class="block text-sm font-medium text-gray-700 mb-1">Spesifikasi</label>
                <textarea id="specifications" name="specifications" rows="4" placeholder="Contoh: Core i5, RAM 8GB, SSD 256GB" class="form-input w-full" required></textarea>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="rental_rate" class="block text-sm font-medium text-gray-700 mb-1">Tarif Sewa per Hari (Rp)</label>
                    <input type="number" id="rental_rate" name="rental_rate" placeholder="Contoh: 50000" class="form-input w-full" required>
                </div>
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Foto Laptop</label>
                    <input type="file" id="image" name="image" accept="image/png, image/jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <a href="<?= BASE_URL ?>/laptop/list" class="text-sm text-gray-600 hover:underline">← Kembali ke Daftar</a>
                <button type="submit" class="bg-[#5C3AC7] text-white font-semibold px-6 py-3 rounded-xl hover:bg-[#47289f] transition w-full md:w-auto">
                    Daftarkan Laptop
                </button>
            </div>
        </form>
    </div>
</section>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>
