<?php
// File: modules/auth/login.php
$pageTitle = "Login - LagiButuh";
$auth = new Auth();

// Redirect jika sudah login
if ($auth->isLoggedIn()) {
    redirect(BASE_URL . '/profile');
}

$errors = [];
$oldInput = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = sanitize_input($_POST['password'] ?? '');
    
    // Validasi input
    if (empty($email)) {
        $errors['email'] = 'Email wajib diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password wajib diisi';
    }
    
    // Jika validasi berhasil
    if (empty($errors)) {
        if ($auth->login($email, $password)) {
            // Redirect ke halaman yang diminta sebelumnya atau ke profile
            $redirectUrl = $_SESSION['redirect_url'] ?? BASE_URL . '/profile';
            unset($_SESSION['redirect_url']);
            redirect($redirectUrl);
        } else {
            $errors['general'] = "Email atau password salah";
        }
    }
    
    // Simpan input lama untuk keperluan tampilan
    $oldInput = [
        'email' => $email
    ];
}

include_once __DIR__ . '/../../templates/header.php';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<div class="flex items-center justify-center min-h-[calc(100vh-160px)] px-4 py-8">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-100">
        <!-- Logo dan Judul -->
        <div class="flex justify-center mb-6">
            <a href="<?= BASE_URL ?>" class="flex items-center">
                <svg class="w-8 h-8 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span class="text-2xl font-bold text-indigo-600">LagiButuh</span>
            </a>
        </div>
        
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Masuk ke Akun Anda</h2>
        <p class="text-center text-gray-500 mb-6">Gunakan email dan password Anda untuk login</p>
        
        <!-- Error Message -->
        <?php if (!empty($errors['general'])): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p><?= $errors['general'] ?></p>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="<?= BASE_URL ?>/login" method="POST" class="space-y-4" novalidate>
            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= e($oldInput['email'] ?? '') ?>"
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
                <div class="flex justify-between items-center mb-1">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <a href="<?= BASE_URL ?>/forgot-password" class="text-sm text-indigo-600 hover:underline">Lupa Password?</a>
                </div>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full px-4 py-2 border <?= !empty($errors['password']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    placeholder="••••••••"
                    required
                >
                <?php if (!empty($errors['password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['password'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Remember Me -->
            <div class="flex items-center">
                <input 
                    id="remember" 
                    name="remember" 
                    type="checkbox" 
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                >
                <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
            </div>
            
            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
            >
                Masuk
            </button>
        </form>
        
        <!-- Social Login -->
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Atau masuk dengan</span>
                </div>
            </div>
            
            <div class="mt-6 grid grid-cols-2 gap-3">
                <a href="<?= BASE_URL ?>/auth/google" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fab fa-google text-red-500 mr-2 mt-0.5"></i>
                    Google
                </a>
                <a href="<?= BASE_URL ?>/auth/facebook" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fab fa-facebook-f text-blue-600 mr-2 mt-0.5"></i>
                    Facebook
                </a>
            </div>
        </div>
        
        <!-- Register Link -->
        <div class="mt-8 text-center text-sm text-gray-600">
            <p>Belum punya akun? 
                <a href="<?= BASE_URL ?>/register" class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline transition-colors">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>