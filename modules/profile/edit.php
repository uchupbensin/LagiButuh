<?php
// File: modules/profile/edit.php
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

$userId = $auth->getUserId();
$user = $auth->getUserById($userId);

$pageTitle = "Edit Profil";
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataToUpdate = [];
    
    // Sanitasi input
    $dataToUpdate['full_name'] = sanitize_input($_POST['full_name']);
    $dataToUpdate['phone_number'] = sanitize_input($_POST['phone_number']);
    $dataToUpdate['current_password'] = sanitize_input($_POST['current_password']);
    $dataToUpdate['new_password'] = sanitize_input($_POST['new_password']);

    // Logika upload foto profil
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $targetDir = __DIR__ . "/../../uploads/profiles/";
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }

        $fileName = $userId . '_' . uniqid() . '_' . basename($_FILES["profile_picture"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($imageFileType, ['jpg', 'png', 'jpeg'])) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
                $dataToUpdate['profile_picture'] = $fileName;
            } else {
                $error = "Gagal mengunggah foto profil.";
            }
        } else {
            $error = "Hanya file JPG, JPEG, & PNG yang diizinkan untuk foto profil.";
        }
    }

    if (empty($error)) {
        $result = $auth->updateUserProfile($userId, $dataToUpdate);
        if ($result === true) {
            $success = "Profil berhasil diperbarui!";
            $user = $auth->getUserById($userId); // Refresh data
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
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">Edit Profil</h1>
            <p class="mt-2 text-gray-600 text-base md:text-lg">Perbarui informasi pribadi dan keamanan akunmu.</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Sidebar Navigasi -->
            <div class="lg:col-span-1">
                <div class="bg-white p-4 rounded-xl shadow-md sticky top-28">
                    <ul class="space-y-2">
                        <li>
                            <a href="<?= BASE_URL ?>/profile/view" class="block px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                Profil Saya
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/profile/edit" class="block px-4 py-2 bg-indigo-100 text-indigo-700 font-semibold rounded-lg">
                                Edit Profil
                            </a>
                        </li>
                        <!-- Tambah link tambahan jika perlu -->
                    </ul>
                </div>
            </div>

            <!-- Form Edit Profil -->
            <div class="lg:col-span-2">
                <div class="bg-white p-8 rounded-xl shadow-md">
                    <?php if ($error): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
                            <p><?= $error; ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
                            <p><?= $success; ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/profile/edit" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <!-- Informasi Dasar -->
                        <div>
                            <h3 class="text-xl font-semibold mb-4">Informasi Dasar</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                    <input type="text" id="full_name" name="full_name" value="<?= e($user['full_name'] ?? '') ?>" class="form-input w-full">
                                </div>
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                    <input type="text" id="phone_number" name="phone_number" value="<?= e($user['phone_number'] ?? '') ?>" class="form-input w-full">
                                </div>
                                <div>
                                    <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">Ubah Foto Profil</label>
                                    <input type="file" id="profile_picture" name="profile_picture" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                            </div>
                        </div>

                        <!-- Ganti Password -->
                        <div class="border-t pt-6">
                            <h3 class="text-xl font-semibold mb-4">Ganti Password</h3>
                            <p class="text-sm text-gray-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>
                            <div class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                                    <input type="password" id="current_password" name="current_password" class="form-input w-full">
                                </div>
                                <div>
                                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                    <input type="password" id="new_password" name="new_password" class="form-input w-full">
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="border-t pt-6">
                            <button type="submit" class="bg-[#5C3AC7] text-white font-semibold px-6 py-3 rounded-xl hover:bg-[#47289f] transition w-full">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>
