<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6 mt-8">
    <h2 class="text-2xl font-bold text-center mb-6">Tawarkan Tumpangan</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <form action="<?php echo BASE_URL; ?>/nebeng/post" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="departure" class="block text-gray-700 mb-2">Lokasi Keberangkatan</label>
                <input type="text" id="departure" name="departure" class="input-field" required>
            </div>
            
            <div class="mb-4">
                <label for="destination" class="block text-gray-700 mb-2">Tujuan</label>
                <input type="text" id="destination" name="destination" class="input-field" required>
            </div>
            
            <div class="mb-4">
                <label for="departure_time" class="block text-gray-700 mb-2">Waktu Keberangkatan</label>
                <input type="datetime-local" id="departure_time" name="departure_time" class="input-field" required>
            </div>
            
            <div class="mb-4">
                <label for="available_seats" class="block text-gray-700 mb-2">Jumlah Kursi Tersedia</label>
                <input type="number" id="available_seats" name="available_seats" min="1" value="1" class="input-field" required>
            </div>
            
            <div class="mb-4">
                <label for="price" class="block text-gray-700 mb-2">Harga per Orang (Rp)</label>
                <input type="number" id="price" name="price" min="0" value="0" class="input-field">
            </div>
        </div>
        
        <div class="mb-4">
            <label for="notes" class="block text-gray-700 mb-2">Catatan Tambahan</label>
            <textarea id="notes" name="notes" rows="3" class="input-field"></textarea>
        </div>
        
        <button type="submit" class="btn-primary w-full">Posting Tawaran Nebeng</button>
    </form>
</div>