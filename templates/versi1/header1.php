<?php
// File: templates/header.php
$auth = new Auth();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - LagiButuh' : 'LagiButuh - Platform Komunitas Serbaguna'; ?></title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS Kustom untuk Tampilan Elegan -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    
    <script>
        // Konfigurasi Tailwind CSS untuk font Inter
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <header class="bg-white/80 backdrop-blur-lg shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="<?php echo BASE_URL; ?>" class="text-2xl font-extrabold text-indigo-600">
                LagiButuh
            </a>
            <div class="hidden lg:flex items-center space-x-8">
                <a href="<?php echo BASE_URL; ?>/psychologist/list" class="text-gray-600 hover:text-indigo-600 transition-colors">Konsultasi</a>
                <a href="<?php echo BASE_URL; ?>/nebeng/find_ride" class="text-gray-600 hover:text-indigo-600 transition-colors">Nebeng</a>
                <a href="<?php echo BASE_URL; ?>/print/printers" class="text-gray-600 hover:text-indigo-600 transition-colors">Titip Print</a>
                <a href="<?php echo BASE_URL; ?>/laptop/list" class="text-gray-600 hover:text-indigo-600 transition-colors">Pinjam Laptop</a>
                <a href="<?php echo BASE_URL; ?>/jastip/list_orders" class="text-gray-600 hover:text-indigo-600 transition-colors">Jasa Titip</a>
            </div>
            <div class="flex items-center space-x-4">
                <?php if ($auth->isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>/profile" class="text-gray-700 font-medium hover:text-indigo-600 transition-colors">Profil</a>
                    <a href="<?php echo BASE_URL; ?>/logout" class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-600 transition-colors shadow-sm">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login" class="text-gray-700 font-medium hover:text-indigo-600 transition-colors">Login</a>
                    <a href="<?php echo BASE_URL; ?>/register" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors shadow-sm">Daftar</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="container mx-auto px-6 py-10 md:py-16">

    <!DOCTYPE html>
<!-- ... (head section) ... -->
<body>
    <!-- ... (header/navigasi) ... -->
    <main class="container mx-auto px-6 py-10 md:py-16">

    <!-- Kontainer untuk Notifikasi Toast -->
    <div id="notification-container" class="fixed top-5 right-5 z-[100] w-full max-w-xs space-y-3">
        <!-- Notifikasi akan muncul di sini -->
    </div>

    <!-- ... (konten utama halaman) ... -->

<!-- ... (sebelum tag </body>) ... -->
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
    <?php if ($auth->isLoggedIn()): ?>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk menampilkan notifikasi toast
        function showToast(title, message, link) {
            const container = document.getElementById('notification-container');
            const toast = document.createElement('div');
            toast.className = 'bg-white rounded-xl shadow-lg p-4 transform transition-all duration-300 translate-x-full opacity-0';
            
            toast.innerHTML = `
                <a href="${link}" class="block">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-indigo-100 p-2 rounded-full">
                           <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-bold text-gray-900">${title}</p>
                            <p class="mt-1 text-sm text-gray-600">${message}</p>
                        </div>
                    </div>
                </a>
            `;
            
            container.appendChild(toast);

            // Animasi masuk
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
            }, 100);

            // Animasi keluar setelah 7 detik
            setTimeout(() => {
                toast.classList.add('opacity-0');
                toast.addEventListener('transitionend', () => toast.remove());
            }, 7000);
        }

        // Konfigurasi Pusher
        const pusher = new Pusher('<?php echo PUSHER_APP_KEY; ?>', {
            cluster: '<?php echo PUSHER_APP_CLUSTER; ?>',
            authEndpoint: '<?php echo BASE_URL; ?>/pusher_auth.php',
            auth: {
                headers: { 'X-CSRF-Token': 'some-csrf-token' } // Tambahkan CSRF token di aplikasi nyata
            }
        });

        // Subscribe ke channel privat pengguna
        const channel = pusher.subscribe('private-user-<?php echo $auth->getUserId(); ?>');

        // Bind ke event notifikasi
        channel.bind('new-notification', function(data) {
            console.log('Notifikasi diterima:', data);
            showToast(data.title, data.message, data.link);
        });

        // Untuk testing, Anda bisa memanggil fungsi ini dari console browser
        // showToast('Contoh Notifikasi', 'Ini adalah contoh pesan notifikasi yang muncul.', '#');
    });
    <?php endif; ?>
</script>
</body>
</html>