<?php
// File: templates/home.php
include 'header.php';
?>

<!-- Hero Section - Lebih Menarik -->
 <!-- Di header.php sebelum </head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<section class="relative py-32 bg-gradient-to-r from-[#5C3AC7] to-[#3A6EC6] text-white overflow-hidden w-screen left-1/2 -translate-x-1/2">
  <div class="max-w-7xl mx-auto px-6 text-center space-y-6">
    <h1 class="text-4xl md:text-6xl font-bold leading-tight animate-fadeIn">
      Darurat? <span class="text-yellow-300 animate-pulse">#LagiButuh</span> Solusinya!
    </h1>
    <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
      Platform gotong royong digital pertama di Indonesia yang telah membantu <span class="font-semibold">50.000+ anggota</span> dalam situasi darurat.
    </p>
    <div class="flex flex-col sm:flex-row justify-center gap-4 pt-4">
      <a href="#layanan" class="px-8 py-4 bg-white text-[#5C3AC7] font-semibold rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <i class="fas fa-search mr-2"></i> Jelajahi Layanan
      </a>
      <a href="<?= BASE_URL ?>/register" class="px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-[#5C3AC7] transition-all duration-300 hover:-translate-y-1">
        <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang
      </a>
    </div>
  </div>
</section>

<!-- Services Section - Lebih Interaktif -->
<section id="layanan" class="py-24 bg-gray-50">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <div class="max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl md:text-4xl font-bold mb-4">Layanan <span class="text-[#5C3AC7]">#LagiButuh</span></h2>
      <p class="text-lg text-gray-600">Solusi cepat untuk masalah darurat mahasiswa dan profesional muda</p>
    </div>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php
      $services = [
        [
          "icon" => "fa-comments", 
          "title" => "Konsultasi Psikolog 24 Jam",
          "desc" => "Bantuan profesional via chat/video call kapan saja",
          "stats" => "1.200+ konsultasi/minggu",
          "link" => BASE_URL."/psychologist/list"
        ],
        [
          "icon" => "fa-car-side", 
          "title" => "Nebeng Aman",
          "desc" => "Tumpangan terverifikasi dengan sistem rating",
          "stats" => "3.500+ perjalanan/bulan",
          "link" => BASE_URL."/nebeng/find_ride"
        ],
        [
          "icon" => "fa-print", 
          "title" => "Titip Print Kilat",
          "desc" => "Dokumen dicetak & diantar dalam 2 jam",
          "stats" => "98% kepuasan pengguna",
          "link" => BASE_URL."/print/upload"
        ],
        [
          "icon" => "fa-laptop-code", 
          "title" => "Pinjam Laptop",
          "desc" => "Pinjaman darurat dengan jaminan keamanan",
          "stats" => "500+ laptop tersedia",
          "link" => BASE_URL."/laptop/list"
        ],
        [
          "icon" => "fa-utensils", 
          "title" => "Titip Makanan",
          "desc" => "Antar makanan favorit ke lokasimu",
          "stats" => "1.000+ pesanan/hari",
          "link" => BASE_URL."/jastip/list_orders"
        ],
        [
          "icon" => "fa-plus-circle", 
          "title" => "Layanan Lainnya",
          "desc" => "Temukan solusi darurat lainnya",
          "stats" => "Terus berkembang",
          "link" => "#"
        ]
      ];
      
      foreach ($services as $s):
      ?>
      <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-xl transition-all duration-300 group border border-gray-100 hover:border-[#5C3AC7]">
        <div class="w-16 h-16 mx-auto mb-6 bg-indigo-50 text-[#5C3AC7] rounded-xl flex items-center justify-center
                    group-hover:bg-[#5C3AC7] group-hover:text-white transition-colors duration-300">
          <i class="fas <?= $s['icon'] ?> text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold mb-3 text-gray-800 group-hover:text-[#5C3AC7]"><?= $s['title'] ?></h3>
        <p class="text-gray-600 mb-4 min-h-[60px]"><?= $s['desc'] ?></p>
        <p class="text-sm text-[#5C3AC7] font-medium mb-4"><?= $s['stats'] ?></p>
        <a href="<?= $s['link'] ?>" class="text-[#5C3AC7] font-semibold hover:underline inline-flex items-center group/link">
          Lihat Detail <i class="fas fa-arrow-right ml-2 group-hover/link:translate-x-1 transition-transform"></i>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Testimonial Section - Lebih Banyak -->
<section class="py-24 bg-white">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <div class="max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl md:text-4xl font-bold mb-4">Kata Mereka Tentang <span class="text-[#5C3AC7]">#LagiButuh</span></h2>
      <p class="text-lg text-gray-600">Cerita nyata dari komunitas kami</p>
    </div>
    
    <div class="grid md:grid-cols-3 gap-8 text-left">
      <!-- Testimonial 1 -->
      <div class="p-8 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 hover:border-[#5C3AC7]">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 rounded-full bg-gray-200 mr-4 overflow-hidden">
            <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Dina" class="w-full h-full object-cover">
          </div>
          <div>
            <h4 class="font-bold">Dina, Mahasiswa</h4>
            <div class="flex text-yellow-400 text-sm">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              <span class="text-gray-500 ml-2">5/5</span>
            </div>
          </div>
        </div>
        <p class="text-gray-600 italic mb-4">"Laptop rusak tepat sebelum sidang skripsi. Lewat LagiButuh dapat pinjaman laptop dalam 1 jam! Penyewanya mahasiswa kampus sebelah yang sangat membantu."</p>
        <p class="text-sm text-gray-500">12 Maret 2025</p>
      </div>

      <!-- Testimonial 2 -->
      <div class="p-8 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 hover:border-[#5C3AC7]">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 rounded-full bg-gray-200 mr-4 overflow-hidden">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Rizky" class="w-full h-full object-cover">
          </div>
          <div>
            <h4 class="font-bold">Rizky, Karyawan</h4>
            <div class="flex text-yellow-400 text-sm">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
              <span class="text-gray-500 ml-2">4.5/5</span>
            </div>
          </div>
        </div>
        <p class="text-gray-600 italic mb-4">"Nebeng kerja tiap hari via LagiButuh menghemat 70% biaya transportasi. Sekarang punya teman nebeng tetap yang sama lokasi kantornya."</p>
        <p class="text-sm text-gray-500">28 Februari 2025</p>
      </div>

      <!-- Testimonial 3 -->
      <div class="p-8 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 hover:border-[#5C3AC7]">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 rounded-full bg-gray-200 mr-4 overflow-hidden">
            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Sari" class="w-full h-full object-cover">
          </div>
          <div>
            <h4 class="font-bold">Sari, Freelancer</h4>
            <div class="flex text-yellow-400 text-sm">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              <span class="text-gray-500 ml-2">5/5</span>
            </div>
          </div>
        </div>
        <p class="text-gray-600 italic mb-4">"Pas isolasi COVID, titip beli obat dan makanan via LagiButuh. Pengirimnya sangat membantu sampai beliin buah dan vitamin tambahan."</p>
        <p class="text-sm text-gray-500">15 Januari 2025</p>
      </div>
    </div>
  </div>
</section>


<!-- Final CTA Section -->
<section class="relative py-32 bg-gradient-to-r from-[#5C3AC7] to-[#3A6EC6] text-white overflow-hidden w-screen left-1/2 -translate-x-1/2">
  <div class="absolute inset-0 bg-[url('<?= BASE_URL ?>/assets/images/pattern.svg')] opacity-10 -z-10"></div>
  <div class="max-w-4xl mx-auto px-6 text-center relative">
    <h2 class="text-3xl md:text-4xl font-bold mb-6">Siap Bergabung dengan Komunitas Kami?</h2>
    <p class="text-xl opacity-90 mb-8 max-w-2xl mx-auto">Daftar sekarang dan dapatkan akses ke semua layanan darurat</p>
    <div class="flex flex-col sm:flex-row justify-center gap-4">
      <a href="<?= BASE_URL ?>/register" class="px-8 py-4 bg-white text-[#5C3AC7] font-bold rounded-full shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
        <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang - Gratis!
      </a>
      <a href="#layanan" class="px-8 py-4 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-[#5C3AC7] transition-all hover:-translate-y-1">
        <i class="fas fa-info-circle mr-2"></i> Pelajari Lebih Lanjut
      </a>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>

<style>
  /* Animasi untuk ikon */
  .animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
  }
  .animate-fadeIn {
    animation: fadeIn 1s ease-out forwards;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>