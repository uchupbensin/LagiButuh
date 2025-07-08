<?php
require_once __DIR__ . '/../../functions/service_functions.php';

$auth = new Auth();
$service = new Service();

if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? null;
    $userId = $auth->getUserId();

    $result = $service->cancelJastipOrder($orderId, $userId);

    if ($result === true) {
        redirect(BASE_URL . "/jastip/detail/$orderId?success=Pesanan berhasil dibatalkan.");
    } else {
        redirect(BASE_URL . "/jastip/detail/$orderId?error=" . urlencode($result));
    }
} else {
    http_response_code(400);
    echo "Permintaan tidak valid.";
}
