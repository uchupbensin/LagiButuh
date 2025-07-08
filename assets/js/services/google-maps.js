// Fungsi utama untuk inisialisasi peta
function initMap() {
    console.log('Memulai inisialisasi peta...');
    
    // 1. Cek elemen dan data
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('Error: Elemen #map tidak ditemukan');
        return;
    }

    // 2. Cek data peta
    if (!mapData || !mapData.center) {
        console.warn('Peringatan: Menggunakan koordinat default');
        mapData.center = { lat: -6.365, lng: 106.831 };
    }

    // 3. Cek Google Maps API
    if (typeof google === 'undefined' || !google.maps) {
        console.error('Error: Google Maps API tidak terload');
        showMapError();
        return;
    }

    try {
        // 4. Buat peta
        const map = new google.maps.Map(mapElement, {
            center: mapData.center,
            zoom: 12,
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true
        });

        // 5. Tambahkan marker
        if (mapData.rides && mapData.rides.length > 0) {
            const markers = [];
            
            mapData.rides.forEach(ride => {
                if (ride.lat && ride.lng) {
                    const marker = new google.maps.Marker({
                        position: { 
                            lat: parseFloat(ride.lat), 
                            lng: parseFloat(ride.lng) 
                        },
                        map: map,
                        title: ride.driver_name || 'Tumpangan'
                    });
                    markers.push(marker);
                }
            });

            // Auto-zoom ke semua marker jika ada
            if (markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds, { padding: 50 });
            }
        }

        console.log('Peta berhasil dimuat!');
        document.getElementById('map-fallback').style.display = 'none';

    } catch (error) {
        console.error('Error saat memuat peta:', error);
        showMapError();
    }
}

// Fungsi untuk menampilkan error
function showMapError() {
    const fallback = document.getElementById('map-fallback');
    if (fallback) {
        fallback.style.display = 'block';
    }
}

// Jika API sudah dimuat sebelum script ini dijalankan
if (typeof google !== 'undefined') {
    initMap();
}

// Ekspos fungsi ke global scope untuk callback
window.initMap = initMap;