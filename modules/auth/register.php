<?php
// File: modules/auth/register.php
$pageTitle = "Daftar Akun - LagiButuh";
$auth = new Auth();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    redirect(BASE_URL . '/profile');
}

$errors = [];
$success = '';
$oldInput = [
    'username' => '',
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = sanitize_input($_POST['username'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = sanitize_input($_POST['password'] ?? '');
    $confirm_password = sanitize_input($_POST['confirm_password'] ?? '');

    // Validate inputs
    if (empty($username)) {
        $errors['username'] = 'Username wajib diisi';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username minimal 3 karakter';
    }

    if (empty($email)) {
        $errors['email'] = 'Email wajib diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid';
    }

    if (empty($password)) {
        $errors['password'] = 'Password wajib diisi';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password minimal 8 karakter';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Password harus mengandung huruf besar dan angka';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Konfirmasi password tidak cocok';
    }

    // If validation passes
    if (empty($errors)) {
        $result = $auth->register($username, $email, $password);
        
        if ($result === true) {
            $success = "Registrasi berhasil! Silakan cek email Anda untuk verifikasi.";
            
            // Clear old input on success
            $oldInput = [
                'username' => '',
                'email' => ''
            ];
        } else {
            $errors['general'] = $result;
        }
    }
    
    // Preserve input for form
    $oldInput = [
        'username' => $username,
        'email' => $email
    ];
}

include_once __DIR__ . '/../../templates/header.php';
?>

<div class="flex items-center justify-center min-h-[calc(100vh-160px)] px-4 py-8">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-100">
        <!-- Logo and Header -->
        <div class="flex justify-center mb-6">
            <a href="<?= BASE_URL ?>" class="flex items-center">
                <svg class="w-8 h-8 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span class="text-2xl font-bold text-indigo-600">LagiButuh</span>
            </a>
        </div>
        
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Buat Akun Baru</h2>
        <p class="text-center text-gray-500 mb-6">Bergabunglah dengan komunitas kami</p>
        
        <!-- Error Message -->
        <?php if (!empty($errors['general'])): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p><?= $errors['general'] ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <p><?= $success ?></p>
                <p class="mt-2">Sudah verifikasi? <a href="<?= BASE_URL ?>/login" class="font-medium text-green-600 hover:underline">Login sekarang</a></p>
            </div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form action="<?= BASE_URL ?>/register" method="POST" class="space-y-4" novalidate>
            <!-- Username Field -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="<?= htmlspecialchars($oldInput['username']) ?>"
                    class="w-full px-4 py-2 border <?= !empty($errors['username']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    placeholder="Masukkan username"
                    required
                >
                <?php if (!empty($errors['username'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['username'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($oldInput['email']) ?>"
                    class="w-full px-4 py-2 border <?= !empty($errors['email']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    placeholder="email@contoh.com"
                    required
                >
                <?php if (!empty($errors['email'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['email'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-2 border <?= !empty($errors['password']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        placeholder="Minimal 8 karakter"
                        required
                    >
                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="togglePassword('password')">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
                <?php if (!empty($errors['password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['password'] ?></p>
                <?php endif; ?>
                <p class="mt-1 text-xs text-gray-500">Gunakan minimal 8 karakter dengan kombinasi huruf besar dan angka</p>
            </div>
            
            <!-- Confirm Password Field -->
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="w-full px-4 py-2 border <?= !empty($errors['confirm_password']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        placeholder="Ketik ulang password"
                        required
                    >
                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="togglePassword('confirm_password')">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['confirm_password'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Terms Agreement -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        id="terms" 
                        name="terms" 
                        type="checkbox" 
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        required
                    >
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms" class="font-medium text-gray-700">Saya menyetujui</label>
                    <p class="text-gray-500">Dengan mendaftar, saya setuju dengan <a href="<?= BASE_URL ?>/terms" class="text-indigo-600 hover:underline">Syarat & Ketentuan</a> dan <a href="<?= BASE_URL ?>/privacy" class="text-indigo-600 hover:underline">Kebijakan Privasi</a> LagiButuh</p>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors mt-2"
            >
                Daftar Sekarang
            </button>
        </form>
        
        <!-- Social Register -->
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Atau daftar dengan</span>
                </div>
            </div>
            
            <div class="mt-6 grid grid-cols-2 gap-3">
                <a href="<?= BASE_URL ?>/auth/google/register" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fab fa-google text-red-500 mr-2 mt-0.5"></i>
                    Google
                </a>
                <a href="<?= BASE_URL ?>/auth/facebook/register" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fab fa-facebook-f text-blue-600 mr-2 mt-0.5"></i>
                    Facebook
                </a>
            </div>
        </div>
        
        <!-- Login Link -->
        <div class="mt-8 text-center text-sm text-gray-600">
            <p>Sudah punya akun? 
                <a href="<?= BASE_URL ?>/login" class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline transition-colors">Login disini</a>
            </p>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>