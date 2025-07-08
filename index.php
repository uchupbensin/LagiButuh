<?php
// File: index.php (Router Diperbaiki & Lebih Canggih)

// --- KODE PALING PENTING UNTUK MELACAK ERROR ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ---------------------------------------------

// Mulai session di setiap halaman
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Muat semua file yang dibutuhkan menggunakan path absolut
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/functions/helper_functions.php';

// --- Routing Sederhana ---
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

$controller = !empty($urlParts[0]) ? $urlParts[0] : 'home';
$method = !empty($urlParts[1]) ? $urlParts[1] : 'index';
$param = !empty($urlParts[2]) ? $urlParts[2] : null;
// Tambahkan parameter ke-4 untuk kasus seperti /review/add/psychologist/123
$subParam = !empty($urlParts[3]) ? $urlParts[3] : null; 

// Tentukan path file yang akan dimuat berdasarkan URL
$filePath = '';
$moduleDir = __DIR__ . '/modules/';

switch ($controller) {
    case 'home':
        $filePath = __DIR__ . '/templates/home.php';
        break;
    
    case 'login':
    case 'register':
    case 'logout':
        $filePath = $moduleDir . 'auth/' . $controller . '.php';
        break;

    case 'profile':
        $action = ($method === 'index' || empty($method)) ? 'view' : $method;
        $filePath = $moduleDir . 'profile/' . $action . '.php';
        break;

    // --- LOGIKA ROUTER BARU YANG LEBIH PINTAR ---
    case 'psychologist':
    case 'nebeng':
    case 'print':
    case 'laptop':
    case 'jastip':
    case 'review':
        $potentialPathAsFile = $moduleDir . $controller . '/' . $method . '.php';
        $potentialPathAsDir = $moduleDir . $controller . '/' . $method;

        if (is_dir($potentialPathAsDir) && file_exists($potentialPathAsDir . '/index.php')) {
            // Menangani URL seperti /nebeng/chat/[id]
            // Ini akan memuat /modules/nebeng/chat/index.php
            $filePath = $potentialPathAsDir . '/index.php';
            // Parameter ID sekarang ada di $param
        } elseif (file_exists($potentialPathAsFile)) {
            // Menangani URL seperti /nebeng/detail/[id]
            // Ini akan memuat /modules/nebeng/detail.php
            $filePath = $potentialPathAsFile;
        } else {
            // Menangani URL default seperti /nebeng/
            $defaultAction = 'list'; // Default umum
            if ($controller === 'nebeng') $defaultAction = 'find_ride';
            if ($controller === 'print') $defaultAction = 'upload';
            if ($controller === 'jastip') $defaultAction = 'list_orders';

            $filePath = $moduleDir . $controller . '/' . $defaultAction . '.php';
        }
        break;
    // -------------------------------------------

    default:
        http_response_code(404);
        $filePath = __DIR__ . '/templates/errors/404.php';
        break;
}

// Muat file yang sesuai
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    http_response_code(404);
    if (!file_exists(__DIR__ . '/templates/errors/404.php')) {
        echo "<h1>404 Not Found</h1><p>Router tidak dapat menemukan file untuk controller '{$controller}' di path: {$filePath}</p>";
    } else {
        require_once __DIR__ . '/templates/errors/404.php';
    }
}
?>
