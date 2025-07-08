<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <h3 class="text-xl font-semibold mb-4">Berikan Rating</h3>
    
    <form id="rating-form" method="POST" action="<?php echo BASE_URL; ?>/rating/submit">
        <input type="hidden" name="service_type" value="<?php echo $serviceType; ?>">
        <input type="hidden" name="service_id" value="<?php echo $serviceId; ?>">
        <input type="hidden" name="target_id" value="<?php echo $targetId; ?>">
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Rating</label>
            <div class="rating-stars flex" id="rating-stars">
                <?php renderRatingStars(0, true); ?>
            </div>
        </div>
        
        <div class="mb-4">
            <label for="review" class="block text-gray-700 mb-2">Ulasan (opsional)</label>
            <textarea id="review" name="review" rows="3" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Bagaimana pengalaman Anda?"></textarea>
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Kirim Rating</button>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#rating-form').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengirim rating');
            }
        });
    });
});
</script>