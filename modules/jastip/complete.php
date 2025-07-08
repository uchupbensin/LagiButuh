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

    $order = $service->getJastipOrderById($orderId);
    if ($order && $order['runner_id'] == $runnerId && $order['status'] === 'accepted') {
        $stmt = Database::getInstance()->getConnection()->prepare("UPDATE jastip_orders SET status = 'delivered' WHERE id = ?");
        $stmt->execute([$orderId]);

        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Pesanan berhasil ditandai sebagai selesai.'
        ];

        // Redirect ke profil supaya tetap di dashboard user setelah selesai
        redirect(BASE_URL . '/profile');
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Gagal menyelesaikan pesanan. Pastikan Anda adalah runner-nya.'
        ];
        redirect(BASE_URL . '/profile');
    }
} else {
    http_response_code(400);
    echo "Permintaan tidak valid.";
}
