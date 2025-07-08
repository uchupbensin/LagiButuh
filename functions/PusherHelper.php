<?php
// File: functions/PusherHelper.php
// Kelas bantuan untuk berinteraksi dengan Pusher.

require_once __DIR__ . '/../vendor/autoload.php';

class PusherHelper {
    private static $pusher = null;

    /**
     * Menginisialisasi instance Pusher (Singleton).
     */
    private static function init() {
        if (self::$pusher === null) {
            $options = [
                'cluster' => PUSHER_APP_CLUSTER,
                'useTLS' => true
            ];
            self::$pusher = new Pusher\Pusher(
                PUSHER_APP_KEY,
                PUSHER_APP_SECRET,
                PUSHER_APP_ID,
                $options
            );
        }
    }

    /**
     * Mengirim notifikasi ke channel tertentu.
     * @param string $channel Nama channel (e.g., 'private-user-123').
     * @param string $event Nama event (e.g., 'new-notification').
     * @param array $data Data yang akan dikirim.
     */
    public static function notify($channel, $event, $data) {
        self::init();
        try {
            self::$pusher->trigger($channel, $event, $data);
        } catch (Exception $e) {
            // Log error jika pengiriman gagal
            error_log('Pusher Error: ' . $e->getMessage());
        }
    }
}
?>
