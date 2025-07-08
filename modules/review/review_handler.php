<?php
require_once __DIR__.'/../../config/database.php';
require_once __DIR__.'/../../core/Auth.php';

$db = new Database();
$auth = new Auth($db->getConnection());

if (!$auth->isLoggedIn()) {
    header('Location: /login');
    exit;
}

$user = $auth->getUser();

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceType = $db->escape($_POST['service_type']);
    $serviceId = (int)$_POST['service_id'];
    $rating = (int)$_POST['rating'];
    $comment = $db->escape($_POST['comment'] ?? '');
    $reviewedId = (int)$_POST['reviewed_id'];
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        header('Location: '.$_SERVER['HTTP_REFERER'].'?error=invalid_rating');
        exit;
    }
    
    // Check if user already reviewed this service
    $checkQuery = "SELECT id FROM reviews 
                  WHERE reviewer_id = {$user['id']} 
                  AND service_type = '$serviceType' 
                  AND service_id = $serviceId";
    
    if ($db->query($checkQuery)->num_rows > 0) {
        header('Location: '.$_SERVER['HTTP_REFERER'].'?error=already_reviewed');
        exit;
    }
    
    // Insert review
    $query = "INSERT INTO reviews 
             (reviewer_id, reviewed_id, service_type, service_id, rating, comment, created_at)
             VALUES
             ({$user['id']}, $reviewedId, '$serviceType', $serviceId, $rating, '$comment', NOW())";
    
    if ($db->query($query)) {
        // Update service rating
        $this->updateServiceRating($serviceType, $serviceId, $reviewedId);
        
        header('Location: '.$_SERVER['HTTP_REFERER'].'?success=review_submitted');
    } else {
        header('Location: '.$_SERVER['HTTP_REFERER'].'?error=review_failed');
    }
    exit;
}

function updateServiceRating($serviceType, $serviceId, $userId) {
    global $db;
    
    // Calculate new average rating
    $query = "SELECT AVG(rating) as avg_rating FROM reviews 
             WHERE service_type = '$serviceType' 
             AND reviewed_id = $userId";
    
    $result = $db->query($query);
    $avgRating = $result->fetch_assoc()['avg_rating'];
    
    // Update rating in the appropriate table
    switch ($serviceType) {
        case 'nebeng':
            $db->query("UPDATE users SET nebeng_rating = $avgRating WHERE id = $userId");
            break;
        case 'psychologist':
            $db->query("UPDATE psychologists SET rating = $avgRating WHERE user_id = $userId");
            break;
        case 'print':
            $db->query("UPDATE print_services SET rating = $avgRating WHERE user_id = $userId");
            break;
    }
    
    return true;
}

// Get reviews for a service
if (isset($_GET['service_type'], $_GET['service_id'])) {
    $serviceType = $db->escape($_GET['service_type']);
    $serviceId = (int)$_GET['service_id'];
    
    $query = "SELECT r.*, u.name as reviewer_name, u.avatar as reviewer_avatar 
             FROM reviews r
             JOIN users u ON r.reviewer_id = u.id
             WHERE r.service_type = '$serviceType' AND r.service_id = $serviceId
             ORDER BY r.created_at DESC";
    
    $result = $db->query($query);
    $reviews = [];
    
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($reviews);
    exit;
}

header('Location: /');
?>