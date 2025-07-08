<?php
// ===================================================================
// == File: core/Router.php (DIPERBAIKI)                            ==
// == Lokasi: lagibutuh-website/core/Router.php                     ==
// == Fungsi: Mengarahkan URL ke file controller/view yang benar    ==
// ===================================================================

class Router {
    protected $routes = [];
    protected $db;

    public function __construct(PDO $db_connection) {
        $this->db = $db_connection; // Simpan koneksi DB untuk bisa diakses di file yang di-include
    }

    /**
     * Menambahkan rute ke tabel routing.
     *
     * @param string $method (GET, POST)
     * @param string $uri    (Contoh: 'login', 'users/show')
     * @param string $file   (Path ke file yang akan dieksekusi)
     */
    public function add($method, $uri, $file) {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'file' => $file
        ];
    }

    /**
     * Mencocokkan URL saat ini dengan rute yang terdaftar dan memuat file yang sesuai.
     *
     * @param string $url URL yang diminta oleh pengguna
     */
    public function dispatch($url) {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            // Cocokkan metode request (GET/POST) dan URI
            if ($route['method'] === $requestMethod && $route['uri'] === $url) {
                $filePath = __DIR__ . '/../' . $route['file'];

                if (file_exists($filePath)) {
                    // Membuat koneksi $db tersedia di dalam file yang di-include
                    $db = $this->db; 
                    require_once $filePath;
                    return; // Hentikan eksekusi setelah rute ditemukan
                }
            }
        }

        // Jika tidak ada rute yang cocok, tampilkan halaman 404
        $this->abort(404);
    }

    /**
     * Menampilkan halaman error.
     *
     * @param int $code Kode status HTTP (misal: 404)
     */
    protected function abort($code = 404) {
        http_response_code($code);
        
        // Memuat file view untuk halaman error
        $errorView = __DIR__ . '/../templates/errors/' . $code . '.php';
        if (file_exists($errorView)) {
            require_once $errorView;
        } else {
            // Fallback jika file error tidak ada
            echo "<h1>Error {$code}</h1>";
            echo "<p>Halaman tidak ditemukan.</p>";
        }

        die();
    }
}
?>