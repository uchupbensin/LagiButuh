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
    $userId = $auth->getUserId();
    $order = $service->getJastipOrderById($orderId);

    if ($order && $order['user_id'] == $userId && $order['status'] === 'delivered') {
        $stmt = $conn->prepare("UPDATE jastip_orders SET status = 'completed' WHERE id = ?");
        $stmt->execute([$orderId]);
        redirect(BASE_URL . '/jastip/detail/' . $orderId . '?confirmed=1');
    } else {
        redirect(BASE_URL . '/jastip/detail/' . $orderId . '?error=Unauthorized');
    }
}

} else {
    http_response_code(400);
    echo "Permintaan tidak valid.";
}
