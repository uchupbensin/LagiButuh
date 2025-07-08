<?php
// modules/nebeng/find_ride.php

$pageTitle = "Cari Tumpangan";

require_once __DIR__ . '/../../functions/service_functions.php';
require_once __DIR__ . '/../../functions/helper_functions.php';

$service = new Service(); 
$rides = $service->getActiveNebengRides();

$map_data = [
    'rides' => $rides,
    'center' => ['lat' => -6.365, 'lng' => 106.831],
    'baseUrl' => BASE_URL
];

include_once __DIR__ . '/../../templates/header.php';
?>

<section class="bg-[#F9FAFB] px-4 py-8 font-sans">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">Cari Tumpangan</h1>
                <p class="mt-1 md:mt-2 text-base md:text-lg text-gray-600">Temukan tumpangan yang searah dengan tujuanmu. Hemat biaya, tambah teman!</p>
            </div>
            <a href="<?= BASE_URL ?>/nebeng/post_ride" class="inline-block w-full md:w-auto bg-[#5C3AC7] text-white font-semibold px-6 py-3 rounded-xl shadow hover:bg-[#47289f] transition duration-200 ease-in-out text-center">
                + Tawarkan Tumpangan
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <?php if (empty($rides)): ?>
                    <div class="text-center py-12 px-6 bg-white rounded-xl shadow-md">
                        <p class="text-gray-500">Belum ada tumpangan yang tersedia saat ini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($rides as $ride): ?>
                        <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg">
                            <div class="flex flex-col sm:flex-row items-start gap-5">
                                <img src="<?= BASE_URL ?>/uploads/profiles/<?= $ride['driver_picture'] ?? 'default.png' ?>" 
                                     alt="Pengemudi <?= e($ride['driver_name']) ?>" 
                                     class="w-16 h-16 rounded-full object-cover border-2 border-[#D6C8FF]">
                                <div class="flex-grow">
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        <span class="font-medium text-[#5C3AC7] mr-2"><?= e($ride['driver_name']) ?></span>
                                        <span class="hidden sm:inline mx-2">&bull;</span>
                                        <span><?= format_indonesian_date($ride['departure_time']) ?></span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center text-lg gap-2 sm:gap-0">
                                        <span class="font-bold text-gray-800"><?= e($ride['origin']) ?></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-3 text-[#5C3AC7] hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                        <span class="font-bold text-gray-800"><?= e($ride['destination']) ?></span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 w-full sm:w-auto text-center sm:text-right">
                                    <div class="bg-[#F3F0FF] p-3 rounded-lg mb-3">
                                        <p class="font-bold text-2xl text-[#5C3AC7] leading-none"><?= $ride['available_seats'] ?></p>
                                        <p class="text-sm text-[#5C3AC7] font-medium">Kursi</p>
                                    </div>
                                    <a href="<?= BASE_URL ?>/nebeng/detail/<?= $ride['id'] ?>" class="inline-block w-full bg-indigo-600 text-white font-semibold px-5 py-3 rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Peta -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="h-80 lg:h-[600px] w-full bg-gray-200 rounded-xl shadow-md relative">
                        <!-- Loading State -->
                        <div id="map-loading" class="absolute inset-0 flex items-center justify-center bg-gray-100 z-10">
                            <div class="text-center p-4">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#5C3AC7] mx-auto"></div>
                                <p class="mt-2 text-gray-600">Memuat peta...</p>
                            </div>
                        </div>
                        
                        <!-- Fallback UI -->
                        <div id="map-fallback" class="absolute inset-0 flex items-center justify-center bg-gray-100 hidden z-20">
                            <div class="text-center p-4">
                                <p class="text-red-500 mb-2">Peta tidak dapat dimuat</p>
                                <button onclick="window.location.reload()" class="px-4 py-2 bg-[#5C3AC7] text-white rounded-lg hover:bg-[#47289f] transition">
                                    Coba Lagi
                                </button>
                            </div>
                        </div>
                        
                        <!-- Container Peta -->
                        <div id="map" class="absolute inset-0 rounded-xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Data untuk peta
    const mapData = <?= json_encode($map_data) ?>;
    
    // Fungsi untuk menampilkan error
    function showMapError() {
        document.getElementById('map-loading').style.display = 'none';
        document.getElementById('map-fallback').style.display = 'flex';
    }
</script>

<!-- Load Google Maps API -->
<script>
    function initMap() {
        try {
            // 1. Sembunyikan loading
            document.getElementById('map-loading').style.display = 'none';
            
            // 2. Cek elemen dan data
            const mapElement = document.getElementById('map');
            if (!mapElement) throw new Error('Elemen peta tidak ditemukan');
            
            if (!mapData?.rides) throw new Error('Data rides tidak valid');

            // 3. Buat peta
            const map = new google.maps.Map(mapElement, {
                center: mapData.center || { lat: -6.365, lng: 106.831 },
                zoom: 12,
                mapTypeControl: true,
                streetViewControl: false
            });

            // 4. Tambahkan marker
            const markers = [];
            mapData.rides.forEach(ride => {
                if (ride.lat && ride.lng) {
                    const marker = new google.maps.Marker({
                        position: { 
                            lat: parseFloat(ride.lat), 
                            lng: parseFloat(ride.lng) 
                        },
                        map: map,
                        title: ride.driver_name || 'Tumpangan',
                        icon: {
                            url: `${mapData.baseUrl}/assets/img/marker.png`,
                            scaledSize: new google.maps.Size(40, 40)
                        }
                    });

                    // 5. Info window
                    if (ride.driver_name) {
                        marker.addListener('click', () => {
                            new google.maps.InfoWindow({
                                content: `
                                    <div class="p-2 max-w-xs">
                                        <div class="flex items-center gap-2 mb-2">
                                            <img src="${mapData.baseUrl}/uploads/profiles/${ride.driver_picture || 'default.png'}" 
                                                 class="w-8 h-8 rounded-full object-cover">
                                            <h3 class="font-bold">${ride.driver_name}</h3>
                                        </div>
                                        <p class="text-sm"><span class="font-medium">Rute:</span> ${ride.origin} → ${ride.destination}</p>
                                        <a href="${mapData.baseUrl}/nebeng/detail/${ride.id}" 
                                           class="inline-block mt-2 px-3 py-1 bg-[#5C3AC7] text-white text-sm rounded-lg hover:bg-[#47289f] transition">
                                            Lihat Detail
                                        </a>
                                    </div>
                                `
                            }).open(map, marker);
                        });
                    }

                    markers.push(marker);
                }
            });

            // 6. Auto-zoom ke semua marker
            if (markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds, { padding: 50 });
            }

        } catch (error) {
            console.error('Error initMap:', error);
            showMapError();
        }
    }

    // Load API dengan fallback
    function loadMapsAPI() {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/gh/somanchiu/Keyless-Google-Maps-API@v7.0/mapsJavaScriptAPI.js';
        script.onerror = () => {
            console.error('Gagal memuat Keyless API');
            showMapError();
        };
        document.head.appendChild(script);
    }

    // Jalankan saat DOM siap
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('map')) {
            loadMapsAPI();
            
            // Fallback timeout
            setTimeout(() => {
                if (typeof google === 'undefined') {
                    showMapError();
                }
            }, 5000);
        }
    });

    // Ekspos ke global
    window.initMap = initMap;
</script>

<?php 
include_once __DIR__ . '/../../templates/footer.php'; 
?>