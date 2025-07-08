<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../core/Auth.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$lng = $data['lng'] ?? 0;
$lat = $data['lat'] ?? 0;
$radius = $data['radius'] ?? 10; // km

// Calculate distance using Haversine formula
$query = "
    SELECT 
        r.*,
        u.name as user_name,
        u.avatar as user_avatar,
        (6371 * acos(
            cos(radians(?)) * 
            cos(radians(departure_lat)) * 
            cos(radians(departure_lng) - radians(?)) + 
            sin(radians(?)) * 
            sin(radians(departure_lat))
        ) AS distance
    FROM 
        nebeng_rides r
    JOIN 
        users u ON r.user_id = u.id
    WHERE 
        r.status = 'active' AND
        r.departure_time > NOW()
    HAVING 
        distance < ?
    ORDER BY 
        distance ASC, 
        r.departure_time ASC
    LIMIT 20
";

$stmt = $conn->prepare($query);
$stmt->bind_param('dddd', $lat, $lng, $lat, $radius);
$stmt->execute();
$result = $stmt->get_result();

$rides = [];
while ($row = $result->fetch_assoc()) {
    $rides[] = $row;
}

echo json_encode($rides);
?>