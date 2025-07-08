<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config/env.php';

use Midtrans\Config;
use Midtrans\Snap;

class PaymentService {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        
        // Setup Midtrans configuration (Sandbox mode for localhost)
        Config::$serverKey = 'SB-Mid-server-Your-Sandbox-Server-Key';
        Config::$clientKey = 'SB-Mid-client-Your-Sandbox-Client-Key';
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
    
    public function createNebengPayment($rideId, $userId, $amount) {
        // Get ride details
        $ride = $this->db->query("SELECT * FROM nebeng_rides WHERE id = $rideId")->fetch_assoc();
        $user = $this->db->query("SELECT * FROM users WHERE id = $userId")->fetch_assoc();
        
        // Prepare transaction details
        $transaction = [
            'transaction_details' => [
                'order_id' => 'NEBENG-'.time(),
                'gross_amount' => $amount,
            ],
            'item_details' => [
                [
                    'id' => 'nebeng-'.$rideId,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Nebeng from '.$ride['departure'].' to '.$ride['destination'],
                    'brand' => 'LagiButuh',
                    'category' => 'Transportation'
                ]
            ],
            'customer_details' => [
                'first_name' => explode(' ', $user['name'])[0],
                'last_name' => explode(' ', $user['name'])[1] ?? '',
                'email' => $user['email'],
                'phone' => $user['phone'],
            ],
            'callbacks' => [
                'finish' => BASE_URL.'/nebeng/payment_callback',
                'error' => BASE_URL.'/nebeng/payment_callback',
                'pending' => BASE_URL.'/nebeng/payment_callback'
            ]
        ];
        
        try {
            // Get Snap Token
            $snapToken = Snap::getSnapToken($transaction);
            
            // Save to database
            $orderId = $transaction['transaction_details']['order_id'];
            $this->db->query("
                INSERT INTO payments 
                (order_id, user_id, service_type, service_id, amount, status, snap_token, created_at)
                VALUES
                ('$orderId', $userId, 'nebeng', $rideId, $amount, 'pending', '$snapToken', NOW())
            ");
            
            return $snapToken;
        } catch (Exception $e) {
            error_log('Midtrans Error: '.$e->getMessage());
            return false;
        }
    }
    
    public function handlePaymentCallback($orderId, $status) {
        // Update payment status
        $this->db->query("UPDATE payments SET status = '$status' WHERE order_id = '$orderId'");
        
        // Get payment details
        $payment = $this->db->query("SELECT * FROM payments WHERE order_id = '$orderId'")->fetch_assoc();
        
        // Update related service based on service_type
        switch ($payment['service_type']) {
            case 'nebeng':
                $this->db->query("UPDATE nebeng_bookings SET payment_status = '$status' WHERE id = {$payment['service_id']}");
                break;
            case 'psychologist':
                $this->db->query("UPDATE psychologist_bookings SET payment_status = '$status' WHERE id = {$payment['service_id']}");
                break;
            case 'print':
                $this->db->query("UPDATE print_orders SET payment_status = '$status' WHERE id = {$payment['service_id']}");
                break;
        }
        
        return true;
    }
}
?>