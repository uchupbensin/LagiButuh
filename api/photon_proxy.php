<?php
header('Content-Type: application/json');

if (!isset($_GET['q'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing query']);
    exit;
}

$q = urlencode($_GET['q']);
$limit = intval($_GET['limit'] ?? 5);

$url = "https://photon.komoot.io/api/?q=$q&limit=$limit";

$response = file_get_contents($url);
if ($response === FALSE) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to contact Photon API']);
    exit;
}

echo $response;
