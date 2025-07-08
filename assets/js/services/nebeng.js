// Lokasi: assets/js/services/nebeng.js

document.addEventListener('DOMContentLoaded', function () {
    // Periksa apakah elemen peta ada di halaman ini
    const mapElement = document.getElementById('map');
    
    // mapData diambil dari variabel global yang kita buat di file PHP
    if (mapElement && typeof mapData !== 'undefined') {
        
        // Inisialisasi Mapbox
        mapboxgl.accessToken = mapData.accessToken;
        const map = new mapboxgl.Map({
            container: 'map', // ID elemen div
            style: 'mapbox://styles/mapbox/streets-v12', // Gaya peta
            center: mapData.center, // Titik tengah peta (diambil dari data PHP)
            zoom: 11 // Tingkat zoom awal
        });

        // Tambahkan kontrol navigasi (zoom in/out)
        map.addControl(new mapboxgl.NavigationControl());

        // Tambahkan pin (marker) untuk setiap tumpangan
        mapData.rides.forEach(ride => {
            if (ride.from_lat && ride.from_lng) {
                // Buat elemen popup HTML
                const popup = new mapboxgl.Popup({ offset: 25 })
                    .setHTML(`
                        <h3 style="font-weight: bold; margin-bottom: 4px;">${ride.driver}</h3>
                        <p><strong>Dari:</strong> ${ride.from}</p>
                        <p><strong>Ke:</strong> ${ride.to}</p>
                        <a href="${mapData.baseUrl}/nebeng/detail?id=${ride.id}" style="color: #4F46E5; text-decoration: underline;">Lihat Detail</a>
                    `);

                // Buat marker dan tambahkan ke peta
                new mapboxgl.Marker()
                    .setLngLat([ride.from_lng, ride.from_lat])
                    .setPopup(popup) // Tambahkan popup ke marker
                    .addTo(map);
            }
        });
    }
});