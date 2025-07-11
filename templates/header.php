<?php
// File: templates/header.php
$auth = new Auth();
$pageTitle = isset($pageTitle) ? $pageTitle : "LagiButuh - Platform Gotong Royong untuk Kebutuhan Darurat";
$pageDescription = isset($pageDescription) ? $pageDescription : "Butuh bantuan cepat? LagiButuh menghubungkan Anda dengan penyedia jasa terdekat untuk konsultasi psikolog, nebeng kendaraan, pinjam laptop, titip print, dan titip makanan.";
$pageKeywords = isset($pageKeywords) ? $pageKeywords : "bantuan darurat, konsultasi psikolog online, nebeng kampus, pinjam laptop darurat, titip print tugas, titip makanan, komunitas mahasiswa";
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="keywords" content="<?= htmlspecialchars($pageKeywords, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="author" content="LagiButuh Team">
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= BASE_URL ?>">
  <meta property="og:image" content="<?= BASE_URL ?>/assets/images/lagibutuh-social-preview.jpg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-kWu8RtIjJe1QOe2tq9gY+IqR6vKXBe3RIXNIKN3Y4+UGhG6qZ0jXovN79xNi5SmDZk+OlvNsYIPQxAWG1z+F7A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#5C3AC7',
            'primary-dark': '#47289f',
            secondary: '#FF9E9E',
            accent: '#7FBC8D',
            light: '#F0F4FA',
            dark: '#1F2937',
            gray: '#E5E7EB',
            'text-muted': '#6B7280'
          },
          fontFamily: {
            sans: ['Poppins', 'sans-serif']
          }
        }
      }
    }
  </script>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/style.css">
  <style>
    .nav-link {
      position: relative;
      font-weight: 500;
      color: #1F2937;
      transition: color 0.3s ease;
    }
    .nav-link:hover {
      color: #5C3AC7;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -4px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #5C3AC7;
      transition: width 0.3s ease;
    }
    .nav-link:hover::after {
      width: 100%;
    }
  </style>
</head>
<body class="bg-light font-sans text-dark antialiased">
  <header class="bg-white/95 backdrop-blur-xl shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
      <nav class="flex justify-between items-center py-4">
        <a href="<?= BASE_URL ?>" class="flex items-center space-x-2">
          <img src="<?= BASE_URL ?>/assets/img/logobaru.png" alt="Logo LagiButuhh" class="h-10 w-auto object-contain">
          <span class="text-2xl font-bold text-primary">LagiButuh</span>
        </a>
        
        <div class="hidden lg:flex items-center space-x-8">
          <a href="<?= BASE_URL ?>/psychologist/list" class="nav-link">Konsultasi</a>
          <a href="<?= BASE_URL ?>/nebeng/find_ride" class="nav-link">Nebeng</a>
          <a href="<?= BASE_URL ?>/laptop/list" class="nav-link">Pinjam Laptop</a>
          <a href="<?= BASE_URL ?>/jastip/list_orders" class="nav-link">Jasa Titip</a>
          <a href="<?= BASE_URL ?>/print" class="nav-link">Jasa Cetak</a>
        </div>
        
        <div class="flex items-center space-x-4">
          <?php if ($auth->isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>/profile" class="text-dark font-medium hover:text-primary">Profil</a>
            <a href="<?= BASE_URL ?>/logout" class="bg-red-500 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-red-600 active:bg-red-700 shadow transition-colors duration-200">Logout</a>
          <?php else: ?>
            <a href="<?= BASE_URL ?>/login" class="text-dark font-medium hover:text-primary">Login</a>
            <a href="<?= BASE_URL ?>/register" class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-primary-dark active:bg-primary-dark shadow transition-colors duration-200">Daftar</a>
          <?php endif; ?>
        </div>
      </nav>
    </div>
  </header>

  <div id="notification-container" class="fixed top-5 right-5 z-[100] w-full max-w-xs space-y-3"></div>
  <main class="max-w-7xl mx-auto px-4 py-8">

  <?php if ($auth->isLoggedIn()): ?>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
      const PUSHER_APP_KEY = '<?= defined('PUSHER_APP_KEY') ? PUSHER_APP_KEY : '' ?>';
      const PUSHER_APP_CLUSTER = '<?= defined('PUSHER_APP_CLUSTER') ? PUSHER_APP_CLUSTER : '' ?>';
      
      document.addEventListener('DOMContentLoaded', function () {
        function showToast(title, message, link) {
          const container = document.getElementById('notification-container');
          const toast = document.createElement('div');
          toast.className = 'bg-white rounded-xl shadow-lg p-4 transform transition-all duration-300 translate-x-full opacity-0';
          toast.innerHTML = `
            <a href="${link}" class="block">
              <div class="flex items-start">
                <div class="flex-shrink-0 bg-primary/10 p-2 rounded-full">
                  <i class="fa-solid fa-bell text-primary text-xl"></i>
                </div>
                <div class="ml-3 w-0 flex-1">
                  <p class="text-sm font-bold text-dark">${title}</p>
                  <p class="mt-1 text-sm text-text-muted">${message}</p>
                </div>
              </div>
            </a>
          `;
          container.appendChild(toast);
          setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
          }, 100);
          setTimeout(() => {
            toast.classList.add('opacity-0');
            toast.addEventListener('transitionend', () => toast.remove());
          }, 7000);
        }
        
        if (PUSHER_APP_KEY) {
            const pusher = new Pusher(PUSHER_APP_KEY, {
              cluster: PUSHER_APP_CLUSTER,
              authEndpoint: '<?= BASE_URL ?>/pusher_auth.php'
            });

            const channel = pusher.subscribe('private-user-<?= $auth->getUserId() ?>');
            channel.bind('new-notification', function (data) {
              showToast(data.title, data.message, data.link);
            });
        }
      });
    </script>
  <?php endif; ?>