<div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg hover:-translate-y-1">
    <div class="relative">
        <img src="<?= $service['image_url'] ?? '/assets/images/default-service.jpg' ?>" 
             alt="<?= e($service['name']) ?>" 
             class="w-full h-48 object-cover">
        
        <?php if (isset($service['rating'])): ?>
            <div class="absolute top-2 right-2 bg-white bg-opacity-90 px-2 py-1 rounded-full flex items-center">
                <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="text-sm font-medium"><?= number_format($service['rating'], 1) ?></span>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="p-4">
        <h3 class="font-bold text-lg mb-1"><?= e($service['name']) ?></h3>
        
        <?php if (isset($service['location'])): ?>
            <div class="flex items-center text-gray-600 mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm"><?= e($service['location']) ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (isset($service['description'])): ?>
            <p class="text-gray-700 text-sm mb-3 line-clamp-2"><?= e($service['description']) ?></p>
        <?php endif; ?>
        
        <div class="flex justify-between items-center">
            <?php if (isset($service['price'])): ?>
                <span class="font-bold text-blue-600">Rp<?= number_format($service['price'], 0, ',', '.') ?></span>
            <?php else: ?>
                <span class="font-bold text-blue-600">Mulai dari Rp0</span>
            <?php endif; ?>
            
            <a href="<?= $service['url'] ?>" 
               class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">
                Lihat Detail
            </a>
        </div>
    </div>
</div>