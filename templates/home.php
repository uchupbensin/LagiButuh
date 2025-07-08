<?php
// File: templates/home.php
include 'header.php';
?>

<!-- Hero Section - Student Focused -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<section class="relative py-32 bg-gradient-to-r from-[#5C3AC7] to-[#3A6EC6] text-white overflow-hidden w-screen left-1/2 -translate-x-1/2">
  <div class="max-w-7xl mx-auto px-6 text-center space-y-6">
    <h1 class="text-4xl md:text-6xl font-bold leading-tight animate-fadeIn">
      Mahasiswa Sibuk? <span class="text-yellow-300 animate-pulse">#LagiButuh</span> Bantuin!
    </h1>
    <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
      Platform gotong royong khusus mahasiswa - dari <span class="font-semibold">print deadline</span> sampai <span class="font-semibold">nebeng kampus</span>, semua ada solusinya!
    </p>
    <div class="flex flex-col sm:flex-row justify-center gap-4 pt-4">
      <a href="#layanan" class="px-8 py-4 bg-white text-[#5C3AC7] font-semibold rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
        <i class="fas fa-search mr-2"></i> Lihat Layanan
      </a>
      <a href="<?= BASE_URL ?>/register?utm_source=landing&utm_campaign=student" class="px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-[#5C3AC7] transition-all duration-300 hover:-translate-y-1">
        <i class="fas fa-graduation-cap mr-2"></i> Daftar Mahasiswa
      </a>
    </div>
    
    <!-- Student Trust Indicators -->
    <div class="pt-8 flex flex-wrap justify-center gap-6 text-sm">
      <div class="flex items-center">
        <i class="fas fa-check-circle text-green-300 mr-2"></i>
        <span>Digunakan 50+ kampus</span>
      </div>
      <div class="flex items-center">
        <i class="fas fa-shield-alt text-blue-200 mr-2"></i>
        <span>Verifikasi KTM</span>
      </div>
      <div class="flex items-center">
        <i class="fas fa-money-bill-wave text-yellow-300 mr-2"></i>
        <span>Harga mahasiswa</span>
      </div>
    </div>
  </div>
</section>

<!-- Student Pain Points Section -->
<section class="py-16 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid md:grid-cols-3 gap-8">
      <!-- Problem 1 -->
      <div class="text-center p-6 rounded-xl bg-indigo-50">
        <div class="w-14 h-14 mx-auto mb-4 bg-white text-indigo-600 rounded-full flex items-center justify-center shadow-md">
          <i class="fas fa-clock text-xl"></i>
        </div>
        <h3 class="font-bold text-lg mb-2">Deadline Mepet</h3>
        <p class="text-gray-600">Print tugas jam 2 pagi? Printer kosong? Tenang, teman kampus siap bantu!</p>
      </div>
      
      <!-- Problem 2 -->
      <div class="text-center p-6 rounded-xl bg-indigo-50">
        <div class="w-14 h-14 mx-auto mb-4 bg-white text-indigo-600 rounded-full flex items-center justify-center shadow-md">
          <i class="fas fa-wallet text-xl"></i>
        </div>
        <h3 class="font-bold text-lg mb-2">Dompet Tipis</h3>
        <p class="text-gray-600">Nebeng kampus lebih hemat 70% dibanding ojek online atau taksi</p>
      </div>
      
      <!-- Problem 3 -->
      <div class="text-center p-6 rounded-xl bg-indigo-50">
        <div class="w-14 h-14 mx-auto mb-4 bg-white text-indigo-600 rounded-full flex items-center justify-center shadow-md">
          <i class="fas fa-stress text-xl"></i>
        </div>
        <h3 class="font-bold text-lg mb-2">Stres Akademik</h3>
        <p class="text-gray-600">Konsultasi psikolog khusus mahasiswa dengan harga terjangkau</p>
      </div>
    </div>
  </div>
</section>

<!-- Services Section - Student Focused -->
<section id="layanan" class="py-24 bg-gray-50">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <div class="max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl md:text-4xl font-bold mb-4">Layanan <span class="text-[#5C3AC7]">#1</span> untuk Mahasiswa</h2>
      <p class="text-lg text-gray-600">Solusi praktis untuk masalah keseharian mahasiswa</p>
    </div>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php
      $services = [
        [
          "icon" => "fa-print", 
          "title" => "Titip Print Kilat",
          "desc" => "Dokumen dicetak & diantar ke kos/kampus dalam 2 jam",
          "stats" => "Rp 500,-/halaman",
          "badge" => "HOT",
          "link" => BASE_URL."/print/upload"
        ],
        [
          "icon" => "fa-car-side", 
          "title" => "Nebeng Kampus",
          "desc" => "Tumpangan aman sesama mahasiswa dengan sistem rating",
          "stats" => "Rp 10.000,- rata-rata",
          "badge" => "POPULER",
          "link" => BASE_URL."/nebeng/find_ride"
        ],
        [
          "icon" => "fa-laptop-code", 
          "title" => "Pinjam Laptop Darurat",
          "desc" => "Pinjam laptop untuk presentasi/sidang mendadak",
          "stats" => "Rp 25.000,-/hari",
          "badge" => "NEW",
          "link" => BASE_URL."/laptop/list"
        ],
        [
          "icon" => "fa-comments", 
          "title" => "Konsultasi Psikolog",
          "desc" => "Bantuan profesional untuk stres akademik & masalah lainnya",
          "stats" => "Mulai Rp 75.000,-",
          "link" => BASE_URL."/psychologist/list"
        ],
        [
          "icon" => "fa-utensils", 
          "title" => "Titip Makanan",
          "desc" => "Titip beli makanan favorit dari kantin/resto sekitar kampus",
          "stats" => "Tanpa biaya tambahan",
          "link" => BASE_URL."/jastip/list_orders"
        ],
        [
          "icon" => "fa-book", 
          "title" => "Pinjam Buku/Bahan",
          "desc" => "Pinjam buku kuliah atau bahan ajar dari senior",
          "stats" => "Gratis",
          "link" => "#"
        ]
      ];
      
      foreach ($services as $s):
      ?>
      <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-xl transition-all duration-300 group border border-gray-100 hover:border-[#5C3AC7] relative">
        <?php if(isset($s['badge'])): ?>
          <span class="absolute top-0 right-0 bg-[#5C3AC7] text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">
            <?= $s['badge'] ?>
          </span>
        <?php endif; ?>
        
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

<!-- How It Works - Student Version -->
<section class="py-24 bg-white">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <div class="max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl md:text-4xl font-bold mb-4">Cuma 3 Langkah Mudah!</h2>
      <p class="text-lg text-gray-600">Dapatkan bantuan dalam hitungan menit</p>
    </div>
    
    <div class="grid md:grid-cols-3 gap-8 text-left">
      <div class="p-8 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 hover:border-[#5C3AC7]">
        <div class="text-[#5C3AC7] font-bold text-4xl mb-4">1</div>
        <h3 class="font-bold text-xl mb-3 text-gray-800">Daftar dengan KTM</h3>
        <p class="text-gray-600">Verifikasi akun mahasiswa kamu untuk akses penuh</p>
      </div>
      <div class="p-8 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 hover:border-[#5C3AC7]">
        <div class="text-[#5C3AC7] font-bold text-4xl mb-4">2</div>
        <h3 class="font-bold text-xl mb-3 text-gray-800">Cari Bantuan</h3>
        <p class="text-gray-600">Temukan layanan dari mahasiswa di kampus/kos terdekat</p>
      </div>
      <div class="p-8 bg-gray-50 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 hover:border-[#5C3AC7]">
        <div class="text-[#5C3AC7] font-bold text-4xl mb-4">3</div>
        <h3 class="font-bold text-xl mb-3 text-gray-800">Terhubung & Selesai</h3>
        <p class="text-gray-600">Bayar via aplikasi dan masalah teratasi!</p>
      </div>
    </div>
  </div>
</section>

<!-- Student Testimonials -->
<section class="py-24 bg-gray-50">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <div class="max-w-3xl mx-auto mb-16">
      <h2 class="text-3xl md:text-4xl font-bold mb-4">Mahasiswa <span class="text-[#5C3AC7]">#LagiButuh</span></h2>
      <p class="text-lg text-gray-600">Cerita nyata dari teman-teman kampus</p>
    </div>
    
    <div class="grid md:grid-cols-3 gap-8 text-left">
      <!-- Testimonial 1 -->
      <div class="p-8 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 rounded-full bg-gray-200 mr-4 overflow-hidden">
            <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Dina" class="w-full h-full object-cover">
          </div>
          <div>
            <h4 class="font-bold">Dina - UI</h4>
            <div class="flex text-yellow-400 text-sm">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
          </div>
        </div>
        <p class="text-gray-600 italic mb-4">"Pas deadline skripsi, laptop rusak! Lewat LagiButuh bisa pinjam laptop senior dalam 1 jam. Harganya student-friendly banget!"</p>
        <div class="flex items-center text-sm text-gray-500">
          <i class="fas fa-book-open mr-2"></i>
          <span>Mahasiswa Teknik Informatika</span>
        </div>
      </div>
      
      <!-- Testimonial 2 -->
      <div class="p-8 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 rounded-full bg-gray-200 mr-4 overflow-hidden">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Rizky" class="w-full h-full object-cover">
          </div>
          <div>
            <h4 class="font-bold">Rizky - UGM</h4>
            <div class="flex text-yellow-400 text-sm">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
            </div>
          </div>
        </div>
        <p class="text-gray-600 italic mb-4">"Nebeng dari kos ke kampus cuma 10rb, ketemu teman sejurusan pula. Sekarang jadi teman nebeng tetap!"</p>
        <div class="flex items-center text-sm text-gray-500">
          <i class="fas fa-car-side mr-2"></i>
          <span>Pengguna Nebeng Setia</span>
        </div>
      </div>
      
      <!-- Testimonial 3 -->
      <div class="p-8 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 rounded-full bg-gray-200 mr-4 overflow-hidden">
            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Sari" class="w-full h-full object-cover">
          </div>
          <div>
            <h4 class="font-bold">Sari - ITB</h4>
            <div class="flex text-yellow-400 text-sm">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
          </div>
        </div>
        <p class="text-gray-600 italic mb-4">"Stres berat sama tugas akhir, konsultasi via LagiButuh sangat membantu. Psikolognya ramah dan harga khusus mahasiswa."</p>
        <div class="flex items-center text-sm text-gray-500">
          <i class="fas fa-brain mr-2"></i>
          <span>Pengguna Konsultasi</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Final CTA - Student Focused -->
<section class="relative py-32 bg-gradient-to-r from-[#5C3AC7] to-[#3A6EC6] text-white overflow-hidden w-screen left-1/2 -translate-x-1/2">
  <div class="absolute inset-0 bg-[url('<?= BASE_URL ?>/assets/images/campus-pattern.svg')] opacity-10 -z-10"></div>
  <div class="max-w-4xl mx-auto px-6 text-center relative">
    <h2 class="text-3xl md:text-4xl font-bold mb-6">Bergabung dengan 50.000+ Mahasiswa Lainnya</h2>
    <p class="text-xl opacity-90 mb-8 max-w-2xl mx-auto">Daftar sekarang dan nikmati kemudahan layanan khusus mahasiswa</p>
    <div class="flex flex-col sm:flex-row justify-center gap-4">
      <a href="<?= BASE_URL ?>/register?type=student" class="px-8 py-4 bg-white text-[#5C3AC7] font-bold rounded-full shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
        <i class="fas fa-graduation-cap mr-2"></i> Daftar Mahasiswa
      </a>
      <a href="#layanan" class="px-8 py-4 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-[#5C3AC7] transition-all hover:-translate-y-1">
        <i class="fas fa-info-circle mr-2"></i> Lihat Layanan
      </a>
    </div>
    <p class="mt-6 text-sm opacity-80">Verifikasi KTM diperlukan untuk akses penuh</p>
  </div>
</section>

<?php include 'footer.php'; ?>

<style>
  /* Animations */
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
  
  /* Custom Badges */
  [class^="fa-"], [class*=" fa-"] {
    font-family: 'Font Awesome 6 Free' !important;
    font-weight: 900 !important;
  }
</style>