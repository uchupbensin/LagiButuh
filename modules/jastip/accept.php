<?php
require_once __DIR__ . '/../../functions/service_functions.php';
require_once __DIR__ . '/../../core/Auth.php';

$auth = new Auth();
$service = new Service();

if (!$auth->isLoggedIn()) {
    redirect(BASE_URL . '/login');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    $runnerId = $auth->getUserId();

    $result = $service->acceptJastipOrder($orderId, $runnerId);

    if ($result === true) {
        redirect(BASE_URL . "/jastip/detail/$orderId?success=1");
    } else {
        redirect(BASE_URL . "/jastip/detail/$orderId?error=" . urlencode($result));
    }
} else {
    http_response_code(400);
    echo "Permintaan tidak valid.";
}
