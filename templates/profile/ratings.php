<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold mb-6">Rating dan Ulasan</h2>
    
    <div class="flex items-center mb-8 p-4 bg-gray-50 rounded-lg">
        <div class="text-5xl font-bold mr-6"><?php echo number_format($summary['average_rating'], 1); ?></div>
        <div>
            <?php renderRatingStars($summary['average_rating']); ?>
            <div class="text-gray-600 mt-2">Berdasarkan <?php echo $summary['total_ratings']; ?> ulasan</div>
        </div>
    </div>
    
    <div class="space-y-6">
        <?php if (empty($ratings)): ?>
            <div class="text-center py-8 text-gray-500">
                Belum ada rating untuk Anda
            </div>
        <?php else: ?>
            <?php foreach ($ratings as $rating): ?>
                <div class="border-b pb-6">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center">
                            <img src="<?php echo !empty($rating['reviewer_photo']) ? BASE_URL.'/uploads/profiles/'.$rating['reviewer_photo'] : BASE_URL.'/assets/images/default-profile.png'; ?>" alt="<?php echo $rating['reviewer_name']; ?>" class="w-10 h-10 rounded-full mr-3">
                            <div>
                                <div class="font-semibold"><?php echo $rating['reviewer_name']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo $rating['service_name']; ?></div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500"><?php echo date('d M Y', strtotime($rating['created_at'])); ?></div>
                    </div>
                    
                    <div class="mt-3">
                        <?php renderRatingStars($rating['rating']); ?>
                    </div>
                    
                    <?php if (!empty($rating['review'])): ?>
                        <div class="mt-3 text-gray-700"><?php echo $rating['review']; ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>