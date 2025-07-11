<?php
require_once __DIR__ . '/../../functions/service_functions.php';
require_once __DIR__ . '/../../functions/helper_functions.php';
require_once __DIR__ . '/../../core/Auth.php';

$auth = new Auth();
$service = new Service();

if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . "/login?redirect=nebeng/post_ride");
}

function get_lat_lng($address) {
    $address = urlencode($address);
    $url = "https://nominatim.openstreetmap.org/search?q={$address}&format=json&limit=1";
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: nebeng-app/1.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $json = @file_get_contents($url, false, $context);
    $data = json_decode($json, true);
    return !empty($data) ? ['lat' => $data[0]['lat'], 'lng' => $data[0]['lon']] : false;
}

$pageTitle = "Tawarkan Tumpangan";
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origin = trim($_POST['origin'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $departure_time = trim($_POST['departure_time'] ?? '');
    $available_seats = filter_input(INPUT_POST, 'available_seats', FILTER_VALIDATE_INT);
    $notes = trim($_POST['notes'] ?? '');

    $origin_lat = filter_input(INPUT_POST, 'origin_lat', FILTER_VALIDATE_FLOAT);
    $origin_lng = filter_input(INPUT_POST, 'origin_lng', FILTER_VALIDATE_FLOAT);
    $destination_lat = filter_input(INPUT_POST, 'destination_lat', FILTER_VALIDATE_FLOAT);
    $destination_lng = filter_input(INPUT_POST, 'destination_lng', FILTER_VALIDATE_FLOAT);

    if (!$origin_lat || !$origin_lng) {
        $origin_coords = get_lat_lng($origin);
        if ($origin_coords) {
            $origin_lat = $origin_coords['lat'];
            $origin_lng = $origin_coords['lng'];
        } else {
            $errors[] = "Gagal mendapatkan koordinat lokasi jemput.";
        }
    }

    if (!$destination_lat || !$destination_lng) {
        $destination_coords = get_lat_lng($destination);
        if ($destination_coords) {
            $destination_lat = $destination_coords['lat'];
            $destination_lng = $destination_coords['lng'];
        } else {
            $errors[] = "Gagal mendapatkan koordinat tujuan.";
        }
    }

    if (empty($origin)) $errors[] = "Lokasi jemput wajib diisi.";
    if (empty($destination)) $errors[] = "Tujuan akhir wajib diisi.";
    if (empty($departure_time)) $errors[] = "Waktu berangkat wajib diisi.";
    if ($available_seats === false || $available_seats < 1) $errors[] = "Jumlah kursi minimal 1.";

    if (empty($errors)) {
        $driverId = $auth->getUserId();
        $ride_lat = ($origin_lat + $destination_lat) / 2;
        $ride_lng = ($origin_lng + $destination_lng) / 2;

        $result = $service->createNebengRide(
            $driverId, $origin, $destination, $departure_time, $available_seats, $notes,
            $origin_lat, $origin_lng, $destination_lat, $destination_lng, $ride_lat, $ride_lng
        );

        if ($result['success']) {
            $success_message = "Tumpangan berhasil dipublikasikan! Anda akan diarahkan...";
            header("refresh:3;url=" . BASE_URL . "/nebeng/detail/" . $result['id']);
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}

include_once __DIR__ . '/../../templates/header.php';
?>

<!-- Map & Autocomplete -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.photon-autocomplete@1.0.1/dist/photon-autocomplete.min.css" />
<script src="https://unpkg.com/leaflet.photon-autocomplete@1.0.1/dist/photon-autocomplete.min.js"></script>

<section class="bg-[#F9FAFB] px-4 py-10 font-sans">
  <div class="max-w-3xl mx-auto">
    <div class="bg-white p-8 rounded-xl shadow-md">
      <h1 class="text-2xl font-semibold text-gray-900 mb-4">Tawarkan Tumpangan</h1>

      <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 p-4 mb-6 rounded-lg">
          <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-800 p-4 mb-6 rounded-lg">
          <p><?= e($success_message) ?></p>
        </div>
      <?php else: ?>
        <form action="<?= BASE_URL ?>/nebeng/post_ride" method="POST" id="ride-form" class="space-y-4 text-sm text-gray-800">
          <div>
            <label for="origin" class="block font-medium mb-1">Lokasi Jemput</label>
            <input type="text" id="origin" name="origin" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Cari lokasi jemput...">
          </div>

          <div>
            <label for="destination" class="block font-medium mb-1">Tujuan Akhir</label>
            <input type="text" id="destination" name="destination" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Cari tujuan akhir...">
          </div>

          <div>
            <label for="departure_time" class="block font-medium mb-1">Waktu Berangkat</label>
            <input type="datetime-local" id="departure_time" name="departure_time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
          </div>

          <div>
            <label for="available_seats" class="block font-medium mb-1">Jumlah Kursi</label>
            <input type="number" id="available_seats" name="available_seats" min="1" required value="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
          </div>

          <div>
            <label for="notes" class="block font-medium mb-1">Catatan Tambahan</label>
            <textarea id="notes" name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-lg" rows="3" placeholder="Opsional"></textarea>
          </div>

          <!-- Koordinat -->
          <input type="hidden" id="origin_lat" name="origin_lat">
          <input type="hidden" id="origin_lng" name="origin_lng">
          <input type="hidden" id="destination_lat" name="destination_lat">
          <input type="hidden" id="destination_lng" name="destination_lng">

          <div>
            <label class="block font-medium mb-1">Pilih Titik di Peta</label>
            <div id="ride-map" class="h-64 w-full border border-gray-300 rounded-lg"></div>
          </div>

          <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-lg hover:bg-indigo-700 transition">Publikasikan Tumpangan</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</section>

<script>
let map, originMarker, destMarker;

function reverseGeocode(lat, lng, callback) {
  fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
    .then(res => res.json())
    .then(data => {
      if (data && data.display_name) callback(data.display_name);
    })
    .catch(err => console.warn("Reverse geocode error:", err));
}

function initMap() {
  map = L.map('ride-map').setView([-7.797, 110.37], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  originMarker = L.marker([-7.797, 110.37], { draggable: true }).addTo(map).bindPopup("Jemput");
  destMarker = L.marker([-7.8, 110.38], { draggable: true }).addTo(map).bindPopup("Tujuan");

  originMarker.on('dragend', function (e) {
    const { lat, lng } = e.target.getLatLng();
    document.getElementById('origin_lat').value = lat;
    document.getElementById('origin_lng').value = lng;
    reverseGeocode(lat, lng, addr => document.getElementById('origin').value = addr);
  });

  destMarker.on('dragend', function (e) {
    const { lat, lng } = e.target.getLatLng();
    document.getElementById('destination_lat').value = lat;
    document.getElementById('destination_lng').value = lng;
    reverseGeocode(lat, lng, addr => document.getElementById('destination').value = addr);
  });
}

function enableAutocomplete() {
  new PhotonAutocomplete(document.getElementById('origin'), {
    placeholder: 'Cari lokasi jemput...',
    onSelected: feature => {
      const lat = feature.geometry.coordinates[1];
      const lng = feature.geometry.coordinates[0];
      originMarker.setLatLng([lat, lng]);
      map.setView([lat, lng], 15);
      document.getElementById('origin_lat').value = lat;
      document.getElementById('origin_lng').value = lng;
    }
  });

  new PhotonAutocomplete(document.getElementById('destination'), {
    placeholder: 'Cari tujuan akhir...',
    onSelected: feature => {
      const lat = feature.geometry.coordinates[1];
      const lng = feature.geometry.coordinates[0];
      destMarker.setLatLng([lat, lng]);
      map.setView([lat, lng], 15);
      document.getElementById('destination_lat').value = lat;
      document.getElementById('destination_lng').value = lng;
    }
  });
}

window.addEventListener('DOMContentLoaded', () => {
  initMap();
  enableAutocomplete();
});
</script>

<?php include_once __DIR__ . '/../../templates/footer.php'; ?>