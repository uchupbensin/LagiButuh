<?php
// File: templates/home.php
$pageTitle = "Solusi Cepat untuk Semua Kebutuhanmu";

// Sertakan header yang sudah kita buat
include_once __DIR__ . '/header.php';
?>

<!-- Bagian Hero Section -->
<section class="text-center py-20">
    <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight">
        Butuh Bantuan Cepat?
        <br class="hidden md:inline">
        <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 to-purple-500">
            Temukan Solusinya di Sini.
        </span>
    </h1>
    <p class="mt-6 text-lg text-gray-600 max-w-2xl mx-auto">
        LagiButuh adalah ekosistem gotong royong digital yang menghubungkanmu dengan penyedia bantuan untuk berbagai kebutuhan darurat dan jasa di sekitarmu.
    </p>
    <div class="mt-8">
        <a href="#layanan" class="btn-primary">
            Lihat Semua Layanan
        </a>
    </div>
</section>

<!-- Bagian Daftar Layanan -->
<section id="layanan" class="py-20">
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Ekosistem Gotong Royong Digital</h2>
        <p class="mt-4 text-gray-600 max-w-xl mx-auto">Satu platform untuk semua kebutuhan mendadakmu.</p>
    </div>

    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <?php
        // Data layanan untuk ditampilkan di kartu
        $services = [
            [
                'title' => 'Konsultasi Psikolog',
                'description' => 'Bicara dengan psikolog profesional secara privat dan aman untuk kesehatan mentalmu.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5c-1.5 0-2.84.6-3.8 1.61A5.002 5.002 0 004.5 10.5c0 1.5.6 2.84 1.61 3.8A5.002 5.002 0 0010.5 18c1.5 0 2.84-.6 3.8-1.61A5.002 5.002 0 0018 12.5c0-1.5-.6-2.84-1.61-3.8A5.002 5.002 0 0012.5 5h-1.42c.44-.9.72-1.92.72-3V4.5zM10.5 18a2.5 2.5 0 100-5 2.5 2.5 0 000 5zM12 1.5a.75.75 0 00-1.5 0v1.5a.75.75 0 001.5 0V1.5zM4.502 6.002a.75.75 0 00-1.06 1.06l.75.75a.75.75 0 001.06-1.06l-.75-.75zM1.5 12a.75.75 0 000 1.5h1.5a.75.75 0 000-1.5H1.5zM6.002 19.498a.75.75 0 001.06-1.06l-.75-.75a.75.75 0 10-1.06 1.06l.75.75z"></path>',
                'color' => 'bg-blue-100 text-blue-600',
                'link' => BASE_URL . '/psychologist/list'
            ],
            [
                'title' => 'Jasa Nebeng & Curhat',
                'description' => 'Cari tumpangan searah atau teman ngobrol saat di perjalanan. Bayar seikhlasnya.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5v-1.875a3.375 3.375 0 013.375-3.375h9.75a3.375 3.375 0 013.375 3.375v1.875m-17.25 4.5a3.375 3.375 0 003.375 3.375h9.75a3.375 3.375 0 003.375-3.375m-17.25 4.5L5.625 18.75m12.75 0l-1.125-1.875M12 3.75l3 3.75-3 3.75-3-3.75 3-3.75z"></path>',
                'color' => 'bg-green-100 text-green-600',
                'link' => BASE_URL . '/nebeng/find_ride'
            ],
            [
                'title' => 'Titip Cetak/Print',
                'description' => 'Harus cetak tugas mendadak? Titipkan file Anda dengan aman kepada komunitas.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18M3.75 12.118c0 4.142 3.358 7.5 7.5 7.5s7.5-3.358 7.5-7.5V4.5A2.25 2.25 0 0016.5 2.25h-9A2.25 2.25 0 005.25 4.5v7.618m3.75-.868a2.25 2.25 0 01-4.5 0V9.75a2.25 2.25 0 014.5 0v1.5z"></path>',
                'color' => 'bg-red-100 text-red-600',
                'link' => BASE_URL . '/print/upload'
            ],
            [
                'title' => 'Peminjaman Laptop',
                'description' => 'Perangkat rusak saat deadline? Pinjam laptop untuk sementara dari anggota komunitas.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-1.621-.871A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25A2.25 2.25 0 015.25 3h9.75a2.25 2.25 0 012.25 2.25z"></path>',
                'color' => 'bg-purple-100 text-purple-600',
                'link' => BASE_URL . '/laptop/list'
            ],
            [
                'title' => 'Jasa Titip Makanan',
                'description' => 'Lapar tapi mager? Titip beli makanan dari luar dan biarkan runner mengantarkannya.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M9 9.563C9 9.252 9.252 9 9.563 9h4.874c.311 0 .563.252.563.563v4.874c0 .311-.252.563-.563.563H9.563A.562.562 0 019 14.437V9.564z"></path>',
                'color' => 'bg-orange-100 text-orange-600',
                'link' => BASE_URL . '/jastip/list_orders'
            ],
            [
                'title' => 'Dan Banyak Lagi!',
                'description' => 'Ekosistem kami terus berkembang dengan berbagai layanan bantuan lainnya dari komunitas.',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6v11.25m10.5-11.25h-3.375V6.375h3.375v3.375z"></path>',
                'color' => 'bg-gray-100 text-gray-600',
                'link' => '#'
            ]
        ];

        foreach ($services as $service) {
            echo '
            <div class="service-card">
                <div class="p-8">
                    <div class="service-card-icon ' . $service['color'] . '">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            ' . $service['icon_svg'] . '
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mt-5">' . e($service['title']) . '</h3>
                    <p class="mt-2 text-gray-600 text-sm">' . e($service['description']) . '</p>
                    <a href="' . $service['link'] . '" class="inline-block mt-6 font-semibold text-indigo-600 hover:text-indigo-800 group">
                        Jelajahi Layanan <span class="transition-transform group-hover:translate-x-1 inline-block">&rarr;</span>
                    </a>
                </div>
            </div>';
        }
        ?>
    </div>
</section>

<?php
// Sertakan footer
include_once __DIR__ . '/footer.php';
?>
