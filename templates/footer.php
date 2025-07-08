</div> </main> 

<footer class="bg-white text-dark font-sans border-t border-gray-200">
  <div class="max-w-7xl mx-auto px-4 py-16">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

      <div>
        <h3 class="text-2xl font-bold mb-4 flex items-center text-primary">
          <img src="<?= BASE_URL ?>/assets/img/logobaru.png" alt="Logo LagiButuh" class="h-10 w-10 object-contain mr-2 rounded-full">
          LagiButuh
        </h3>
        <p class="text-sm text-text-muted leading-relaxed max-w-xs">
          Platform gotong royong digital untuk bantuan darurat mahasiswa dan masyarakat.
        </p>
        <div class="flex gap-4 mt-6 text-xl text-primary">
          <a href="#" class="hover:text-primary-dark transition"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="hover:text-primary-dark transition"><i class="fab fa-twitter"></i></a>
          <a href="#" class="hover:text-primary-dark transition"><i class="fab fa-instagram"></i></a>
        </div>
      </div>

      <div>
        <h4 class="text-lg font-semibold mb-5 pb-2 border-b border-gray-200 w-fit text-primary">Layanan</h4>
        <ul class="space-y-3 text-sm">
          <li><a href="<?= BASE_URL ?>/psychologist/list" class="flex items-center gap-2 text-text-muted hover:text-primary transition"><i class="fa-solid fa-comments"></i> Konsultasi Psikolog</a></li>
          <li><a href="<?= BASE_URL ?>/nebeng/find_ride" class="flex items-center gap-2 text-text-muted hover:text-primary transition"><i class="fa-solid fa-car"></i> Jasa Nebeng</a></li>
          <li><a href="<?= BASE_URL ?>/laptop/list" class="flex items-center gap-2 text-text-muted hover:text-primary transition"><i class="fa-solid fa-laptop"></i> Pinjam Laptop</a></li>
          <li><a href="<?= BASE_URL ?>/print/upload" class="flex items-center gap-2 text-text-muted hover:text-primary transition"><i class="fa-solid fa-print"></i> Titip Print</a></li>
        </ul>
      </div>

      <div>
        <h4 class="text-lg font-semibold mb-5 pb-2 border-b border-gray-200 w-fit text-primary">Perusahaan</h4>
        <ul class="space-y-3 text-sm">
          <li><a href="<?= BASE_URL ?>/about" class="flex items-center gap-2 text-text-muted hover:text-primary transition"><i class="fa-solid fa-circle-question"></i> Tentang Kami</a></li>
          <li><a href="<?= BASE_URL ?>/careers" class="flex items-center gap-2 text-text-muted hover:text-primary transition"><i class="fa-solid fa-briefcase"></i> Karir</a></li>
          <li><a href="<?= BASE_URL ?>/blog" class="flex items-center gap-2 text-text-muted hover:text-primary transition"><i class="fa-solid fa-blog"></i> Blog</a></li>
        </ul>
      </div>

      <div>
        <h4 class="text-lg font-semibold mb-5 pb-2 border-b border-gray-200 w-fit text-primary">Hubungi Kami</h4>
        <ul class="space-y-5 text-sm">
          <li>
            <p class="font-medium mb-1 text-dark">Email</p>
            <a href="mailto:halo@lagibutuh.com" class="text-text-muted hover:text-primary transition">halo@lagibutuh.com</a>
          </li>
          <li>
            <p class="font-medium mb-1 text-dark">Telepon</p>
            <a href="tel:+6281234567890" class="text-text-muted hover:text-primary transition">+62 812-3456-7890</a>
          </li>
          <li>
            <p class="font-medium mb-1 text-dark">Alamat</p>
            <p class="text-text-muted leading-relaxed">Jl. Digital No. 123, Kota Bandung, Jawa Barat</p>
          </li>
        </ul>
      </div>

    </div>

    <div class="border-t border-gray-200 pt-6 flex flex-col sm:flex-row justify-between items-center text-sm">
      <p class="text-text-muted mb-4 sm:mb-0 flex items-center gap-2">
        <i class="fa-regular fa-file-lines"></i> &copy; <?= date('Y') ?> <span class="font-semibold text-dark">LagiButuh</span>. All rights reserved.
      </p>
      <div class="flex gap-6">
        <a href="<?= BASE_URL ?>/privacy" class="text-text-muted hover:text-primary transition flex items-center gap-1">
          <i class="fa-solid fa-shield-halved"></i> Kebijakan Privasi
        </a>
        <a href="<?= BASE_URL ?>/terms" class="text-text-muted hover:text-primary transition flex items-center gap-1">
          <i class="fa-solid fa-scroll"></i> Syarat & Ketentuan
        </a>
      </div>
    </div>
  </div>
</footer>

</body>
</html>
