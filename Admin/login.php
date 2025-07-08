<?php
// File: admin/login.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/env.php';
require_once '../config/database.php';
require_once '../core/AdminAuth.php';
require_once '../functions/helper_functions.php';

$adminAuth = new AdminAuth();
if ($adminAuth->isLoggedIn()) {
    redirect(BASE_URL . '/admin/');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);

    if ($adminAuth->login($email, $password)) {
        redirect(BASE_URL . '/admin/');
    } else {
        $error = "Email atau password salah, atau Anda bukan admin.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LagiButuh</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
        <h1 class="text-3xl font-bold text-center text-gray-900 mb-2">Admin Panel</h1>
        <p class="text-center text-gray-500 mb-8">Silakan login untuk melanjutkan.</p>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-3 bg-gray-100 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-3 bg-gray-100 border-2 border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg">
                    Login
                </button>
            </div>
        </form>
    </div>
</body>
</html>
