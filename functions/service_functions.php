<?php
// File: functions/service_functions.php
// Kelas untuk mengelola semua logika terkait layanan.

// Memastikan kelas-kelas lain yang dibutuhkan sudah tersedia
if (!class_exists('Database')) {
    require_once __DIR__ . '/../config/database.php';
}
if (!class_exists('PusherHelper')) {
    if (file_exists(__DIR__ . '/PusherHelper.php')) {
        require_once __DIR__ . '/PusherHelper.php';
    }
}
if (!class_exists('Auth')) {
    require_once __DIR__ . '/../core/Auth.php';
}

class Service {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // --- FUNGSI-FUNGSI PSIKOLOG ---
    public function getAllPsychologists() {
        $stmt = $this->conn->prepare("
            SELECT
                u.id, u.full_name, u.profile_picture, pd.specialization,
                pd.experience_years, pd.hourly_rate,
                (SELECT AVG(rating) FROM ratings_reviews WHERE provider_id = u.id AND service_type = 'psychologist') as avg_rating
            FROM users u
            JOIN psychologists_details pd ON u.id = pd.user_id
            WHERE u.role = 'psychologist'
            ORDER BY avg_rating DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPsychologistDetailsById($id) {
        $stmt = $this->conn->prepare("
            SELECT
                u.id, u.full_name, u.email, u.profile_picture,
                pd.specialization, pd.experience_years, pd.description, pd.hourly_rate,
                (SELECT AVG(rating) FROM ratings_reviews WHERE provider_id = u.id AND service_type = 'psychologist') as avg_rating,
                (SELECT COUNT(*) FROM ratings_reviews WHERE provider_id = u.id AND service_type = 'psychologist') as total_reviews
            FROM users u
            JOIN psychologists_details pd ON u.id = pd.user_id
            WHERE u.id = ? AND u.role = 'psychologist'
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createConsultationBooking($userId, $psychologistId, $scheduleTime) {
        $stmt = $this->conn->prepare(
            "INSERT INTO consultation_bookings (user_id, psychologist_id, schedule_time, status, payment_status) VALUES (?, ?, ?, 'pending', 'unpaid')"
        );
        try {
            $stmt->execute([$userId, $psychologistId, $scheduleTime]);
            $bookingId = $this->conn->lastInsertId();

            if (class_exists('PusherHelper')) {
                $user = (new Auth())->getUserById($userId);
                PusherHelper::notify(
                    'private-user-' . $psychologistId,
                    'new-notification',
                    [
                        'title' => 'Booking Konsultasi Baru',
                        'message' => 'Anda menerima permintaan booking baru dari ' . ($user['full_name'] ?? 'seorang pengguna') . '.',
                        'link' => BASE_URL . '/profile'
                    ]
                );
            }
            return $bookingId;
        } catch (PDOException $e) {
            error_log("Booking Error: " . $e->getMessage());
            return "Gagal membuat booking. Silakan coba lagi.";
        }
    }

    public function getBookingsByUserId($userId) {
        $stmt = $this->conn->prepare("
            SELECT
                cb.id, cb.schedule_time, cb.status, cb.payment_status,
                u.full_name as psychologist_name, pd.specialization
            FROM consultation_bookings cb
            JOIN users u ON cb.psychologist_id = u.id
            JOIN psychologists_details pd ON u.id = pd.user_id
            WHERE cb.user_id = ?
            ORDER BY cb.schedule_time DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // --- FUNGSI-FUNGSI NEBENG ---
    public function createNebengRide($driverId, $origin, $destination, $departureTime, $availableSeats, $notes, $origin_lat, $origin_lng, $dest_lat, $dest_lng) {
        $sql = "INSERT INTO nebeng_rides (driver_id, origin, destination, departure_time, available_seats, notes, origin_lat, origin_lng, destination_lat, destination_lng)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$driverId, $origin, $destination, $departureTime, $availableSeats, $notes, $origin_lat, $origin_lng, $dest_lat, $dest_lng]);
            return ['success' => true, 'id' => $this->conn->lastInsertId()];
        } catch (PDOException $e) {
            error_log("Create Ride Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal memposting tumpangan.'];
        }
    }

    public function getActiveNebengRides(): array
    {
        try {
            $sql = "SELECT
                        nr.id, nr.origin, nr.destination, nr.departure_time,
                        nr.available_seats, u.full_name AS driver_name,
                        u.profile_picture AS driver_picture, nr.origin_lat,
                        nr.origin_lng, nr.destination_lat, nr.destination_lng
                    FROM nebeng_rides nr
                    JOIN users u ON nr.driver_id = u.id
                    WHERE nr.status = 'active' AND nr.departure_time > NOW()
                    ORDER BY nr.departure_time ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error fetching active rides: " . $e->getMessage());
            return [];
        }
    }

    public function getNebengRideDetailsById($rideId) {
        $stmt = $this->conn->prepare("
            SELECT
                nr.*, u.full_name as driver_name, u.profile_picture as driver_picture, u.email as driver_email
            FROM nebeng_rides nr
            JOIN users u ON nr.driver_id = u.id
            WHERE nr.id = ?
        ");
        $stmt->execute([$rideId]);
        return $stmt->fetch();
    }

    public function createNebengBooking($rideId, $passengerId) {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare("SELECT * FROM nebeng_rides WHERE id = ? FOR UPDATE");
            $stmt->execute([$rideId]);
            $ride = $stmt->fetch();

            if (!$ride) { throw new Exception("Tumpangan tidak ditemukan."); }
            if ($ride['available_seats'] <= 0) { throw new Exception("Maaf, kursi untuk tumpangan ini sudah habis."); }
            if ($ride['driver_id'] == $passengerId) { throw new Exception("Anda tidak bisa memesan kursi di tumpangan Anda sendiri."); }

            $checkStmt = $this->conn->prepare("SELECT id FROM nebeng_bookings WHERE ride_id = ? AND passenger_id = ?");
            $checkStmt->execute([$rideId, $passengerId]);
            if ($checkStmt->fetch()) { throw new Exception("Anda sudah mengambil kursi untuk tumpangan ini."); }

            $newSeats = $ride['available_seats'] - 1;
            $newStatus = ($newSeats == 0) ? 'full' : 'active';
            $updateStmt = $this->conn->prepare("UPDATE nebeng_rides SET available_seats = ?, status = ? WHERE id = ?");
            $updateStmt->execute([$newSeats, $newStatus, $rideId]);

            $insertStmt = $this->conn->prepare("INSERT INTO nebeng_bookings (ride_id, passenger_id) VALUES (?, ?)");
            $insertStmt->execute([$rideId, $passengerId]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return $e->getMessage();
        }
    }
    
    public function hasNebengBooking($rideId, $passengerId) {
        $stmt = $this->conn->prepare("SELECT id FROM nebeng_bookings WHERE ride_id = ? AND passenger_id = ? AND status = 'confirmed'");
        $stmt->execute([$rideId, $passengerId]);
        return $stmt->fetch() !== false;
    }

    public function getNebengRidesByPassenger($passengerId) {
        $stmt = $this->conn->prepare("
            SELECT
                nr.id, nr.origin, nr.destination, nr.departure_time, u.full_name as driver_name
            FROM nebeng_bookings nb
            JOIN nebeng_rides nr ON nb.ride_id = nr.id
            JOIN users u ON nr.driver_id = u.id
            WHERE nb.passenger_id = ? AND nb.status = 'confirmed'
            ORDER BY nr.departure_time DESC
        ");
        $stmt->execute([$passengerId]);
        return $stmt->fetchAll();
    }

    // --- FUNGSI-FUNGSI PEMINJAMAN LAPTOP ---
    public function createLaptopListing($ownerId, $brand, $model, $specifications, $imagePath, $rentalRate) {
        $stmt = $this->conn->prepare(
            "INSERT INTO laptops (owner_id, brand, model, specifications, image_path, rental_rate_per_day) VALUES (?, ?, ?, ?, ?, ?)"
        );
        try {
            $stmt->execute([$ownerId, $brand, $model, $specifications, $imagePath, $rentalRate]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Create Laptop Error: " . $e->getMessage());
            return "Gagal menambahkan laptop. Silakan coba lagi.";
        }
    }

    public function getAllAvailableLaptops() {
        $stmt = $this->conn->prepare("
            SELECT l.id, l.brand, l.model, l.image_path, l.rental_rate_per_day, u.full_name as owner_name
            FROM laptops l
            JOIN users u ON l.owner_id = u.id
            WHERE l.availability_status = 'available'
            ORDER BY l.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getLaptopDetailsById($laptopId) {
        $stmt = $this->conn->prepare("
            SELECT l.*, u.full_name as owner_name, u.profile_picture as owner_picture
            FROM laptops l
            JOIN users u ON l.owner_id = u.id
            WHERE l.id = ?
        ");
        $stmt->execute([$laptopId]);
        return $stmt->fetch();
    }
    
    public function getLaptopBookingsByUserId($userId) {
        $stmt = $this->conn->prepare("
            SELECT
                lb.id, lb.start_date, lb.end_date, lb.status, lb.payment_status,
                l.brand, l.model, l.image_path, u.full_name as owner_name
            FROM laptop_bookings lb
            JOIN laptops l ON lb.laptop_id = l.id
            JOIN users u ON l.owner_id = u.id
            WHERE lb.user_id = ?
            ORDER BY lb.start_date DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function createLaptopBooking($userId, $laptopId, $startDate, $endDate) {
        $laptop = $this->getLaptopDetailsById($laptopId);
        if (!$laptop || $laptop['availability_status'] !== 'available') {
            return "Maaf, laptop ini sedang tidak tersedia atau sudah dipesan.";
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO laptop_bookings (user_id, laptop_id, start_date, end_date, status, payment_status) VALUES (?, ?, ?, ?, 'pending', 'unpaid')"
        );

        $updateStmt = $this->conn->prepare("UPDATE laptops SET availability_status = 'rented' WHERE id = ?");

        try {
            $this->conn->beginTransaction();
            $stmt->execute([$userId, $laptopId, $startDate, $endDate]);
            $bookingId = $this->conn->lastInsertId();
            $updateStmt->execute([$laptopId]);
            $this->conn->commit();
            return $bookingId;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Laptop Booking Error: " . $e->getMessage());
            return "Gagal membuat booking laptop. Silakan coba lagi.";
        }
    }

    // --- FUNGSI-FUNGSI TITIP PRINT (MODEL PAPAN LOWONGAN) ---
public function createPrintJob($userId, $tmpFilePath, $originalFileName, $copies, $notes, $deliveryMethod, $deliveryAddress, $pickupNotes) {
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/lagibutuh-website/uploads/print_documents/';
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            return "Error: Gagal membuat direktori penyimpanan.";
        }
    }
    $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    $newFileName = 'doc_' . $userId . '_' . time() . '.' . $fileExtension;
    $targetFile = $targetDir . $newFileName;

    if (move_uploaded_file($tmpFilePath, $targetFile)) {
        try {
            // Menambahkan kolom baru ke query INSERT
            $stmt = $this->conn->prepare(
                "INSERT INTO print_jobs (user_id, file_path, file_name_encrypted, copies, notes, status, delivery_method, delivery_address, pickup_notes) 
                VALUES (?, ?, ?, ?, ?, 'open', ?, ?, ?)"
            );
            // Menambahkan variabel baru ke execute
            $stmt->execute([$userId, $newFileName, $originalFileName, $copies, $notes, $deliveryMethod, $deliveryAddress, $pickupNotes]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            unlink($targetFile);
            return "Error Database Sebenarnya: " . $e->getMessage();
        }
    } else {
        return "Error: Gagal memindahkan file yang diunggah.";
    }
}

public function getOpenPrintJobs() {
    $stmt = $this->conn->prepare("
        SELECT 
            pj.id, pj.copies, pj.notes, pj.created_at, 
            pj.delivery_method, pj.delivery_address, pj.pickup_notes, /* <-- KOLOM BARU DIAMBIL */
            u.full_name as requester_name, u.profile_picture as requester_picture
        FROM print_jobs pj
        JOIN users u ON pj.user_id = u.id
        WHERE pj.status = 'open'
        ORDER BY pj.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

public function acceptPrintJob($jobId, $providerId) {
    // Ambil ID pembuat pekerjaan (requester) untuk ditampilkan di notifikasi
    $jobStmt = $this->conn->prepare("SELECT user_id FROM print_jobs WHERE id = ?");
    $jobStmt->execute([$jobId]);
    $job = $jobStmt->fetch();

    // Jika pekerjaan tidak ditemukan atau pengguna mencoba mengambil pekerjaannya sendiri
    if (!$job || $job['user_id'] == $providerId) {
        return false;
    }

    $requesterId = $job['user_id']; // Simpan ID pembuat pekerjaan

    // Query UPDATE "siapa cepat dia dapat"
    $sql = "UPDATE print_jobs 
            SET printer_provider_id = ?, status = 'accepted' 
            WHERE id = ? AND status = 'open'";
    
    $stmt = $this->conn->prepare($sql);
    
    try {
        $stmt->execute([$providerId, $jobId]);
        
        // Cek apakah update berhasil
        if ($stmt->rowCount() > 0) {
            
            // --- BAGIAN NOTIFIKASI UNTUK PENGAMBIL PEKERJAAN ---
            if (class_exists('PusherHelper')) {
                // Ambil NAMA pembuat pekerjaan (requester) untuk ditampilkan di notifikasi
                $requester = (new Auth())->getUserById($requesterId);
                $requesterName = $requester['full_name'] ?? 'seseorang';

                // Kirim notifikasi ke channel pribadi PENGAMBIL pekerjaan (provider)
                PusherHelper::notify(
                    'private-user-' . $providerId, // <-- TARGET NOTIFIKASI DIUBAH
                    'new-notification',
                    [
                        'title' => 'Anda Mengambil Pekerjaan Baru!',
                        'message' => 'Anda berhasil mengambil pekerjaan cetak dari ' . $requesterName . '.',
                        'link' => BASE_URL . '/print/my_accepted_jobs' // Arahkan ke halaman pekerjaan yang diambil
                    ]
                );
            }
            // --- BAGIAN NOTIFIKASI SELESAI ---
            
            return true; // Berhasil
        } else {
            return false; // Gagal (sudah diambil orang lain)
        }

    } catch (PDOException $e) {
        error_log("Error accepting print job: " . $e->getMessage());
        return false;
    }
}

    public function getPrintJobsByUserId($userId) {
        $stmt = $this->conn->prepare("
            SELECT pj.id, pj.file_path, pj.copies, pj.status, pj.created_at, u.full_name as provider_name
            FROM print_jobs pj
            LEFT JOIN users u ON pj.printer_provider_id = u.id
            WHERE pj.user_id = ?
            ORDER BY pj.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // --- FUNGSI-FUNGSI JASA TITIP ---
    public function createJastipOrder($userId, $itemDescription, $purchaseLocation, $deliveryLocation, $estimatedPrice) {
        $stmt = $this->conn->prepare(
            "INSERT INTO jastip_orders (user_id, item_description, purchase_location, delivery_location, estimated_price, status) VALUES (?, ?, ?, ?, ?, 'open')"
        );
        try {
            $stmt->execute([$userId, $itemDescription, $purchaseLocation, $deliveryLocation, $estimatedPrice]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Create Jastip Error: " . $e->getMessage());
            return "Gagal membuat pesanan jastip.";
        }
    }

    public function getOpenJastipOrders() {
        $stmt = $this->conn->prepare("
            SELECT jo.id, jo.item_description, jo.purchase_location, jo.delivery_location, jo.created_at, u.full_name as user_name, u.profile_picture as user_picture
            FROM jastip_orders jo
            JOIN users u ON jo.user_id = u.id
            WHERE jo.status = 'open'
            ORDER BY jo.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getJastipOrderById($orderId) {
        $stmt = $this->conn->prepare("
            SELECT 
                jo.*, 
                u_order.full_name as orderer_name, 
                u_runner.full_name as runner_name
            FROM jastip_orders jo
            JOIN users u_order ON jo.user_id = u_order.id
            LEFT JOIN users u_runner ON jo.runner_id = u_runner.id
            WHERE jo.id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }
    
    public function getJastipOrdersByUserId($userId) {
        $stmt = $this->conn->prepare("
            SELECT 
                jo.id,
                jo.item_description,
                jo.purchase_location,
                jo.delivery_location,
                jo.estimated_price,
                jo.status,
                jo.created_at,
                u.full_name as runner_name
            FROM jastip_orders jo
            LEFT JOIN users u ON jo.runner_id = u.id
            WHERE jo.user_id = ?
            ORDER BY jo.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getJastipOrdersTakenByUser($runnerId) {
        $stmt = $this->conn->prepare("
            SELECT 
                jo.id,
                jo.item_description,
                jo.purchase_location,
                jo.delivery_location,
                jo.estimated_price,
                jo.status,
                jo.created_at,
                u.full_name as orderer_name
            FROM jastip_orders jo
            JOIN users u ON jo.user_id = u.id
            WHERE jo.runner_id = ?
            ORDER BY jo.created_at DESC
        ");
        $stmt->execute([$runnerId]);
        return $stmt->fetchAll();
    }

    public function acceptJastipOrder($orderId, $runnerId) {
        $order = $this->getJastipOrderById($orderId);

        if (!$order || $order['status'] !== 'open') {
            return "Pesanan ini sudah tidak tersedia atau sudah diambil.";
        }
        
        if ($order['user_id'] == $runnerId) {
            return "Anda tidak bisa mengambil pesanan milik sendiri.";
        }

        $stmt = $this->conn->prepare("UPDATE jastip_orders SET runner_id = ?, status = 'accepted' WHERE id = ?");
        try {
            $stmt->execute([$runnerId, $orderId]);
            return true;
        } catch (PDOException $e) {
            error_log("Accept Jastip Error: " . $e->getMessage());
            return "Gagal menerima pesanan.";
        }
    }
    
    public function cancelJastipOrder($orderId, $userId) {
        $order = $this->getJastipOrderById($orderId);

        if (!$order) { return "Pesanan tidak ditemukan."; }

        $isOwner = $order['user_id'] == $userId;
        $isRunner = $order['runner_id'] == $userId;

        if (!$isOwner && !$isRunner) {
            return "Anda tidak punya izin membatalkan pesanan ini.";
        }
        
        if (!in_array($order['status'], ['open', 'accepted'])) {
            return "Pesanan tidak bisa dibatalkan.";
        }

        try {
            $stmt = $this->conn->prepare("UPDATE jastip_orders SET status = 'cancelled' WHERE id = ?");
            $stmt->execute([$orderId]);
            return true;
        } catch (PDOException $e) {
            error_log("Cancel Jastip Error: " . $e->getMessage());
            return "Gagal membatalkan pesanan.";
        }
    }

    // --- FUNGSI-FUNGSI RATING & REVIEW ---
    public function addReview($serviceType, $serviceId, $providerId, $reviewerId, $rating, $reviewText) {
        $checkStmt = $this->conn->prepare("SELECT id FROM ratings_reviews WHERE service_type = ? AND service_id = ? AND reviewer_id = ?");
        $checkStmt->execute([$serviceType, $serviceId, $reviewerId]);
        if ($checkStmt->fetch()) {
            return "Anda sudah pernah memberikan ulasan untuk layanan ini.";
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO ratings_reviews (service_type, service_id, provider_id, reviewer_id, rating, review_text) VALUES (?, ?, ?, ?, ?, ?)"
        );
        try {
            $stmt->execute([$serviceType, $serviceId, $providerId, $reviewerId, $rating, $reviewText]);
            return true;
        } catch (PDOException $e) {
            error_log("Add Review Error: " . $e->getMessage());
            return "Gagal menyimpan ulasan.";
        }
    }

    public function getReviewsForProvider($providerId, $serviceType) {
        $stmt = $this->conn->prepare("
            SELECT rr.rating, rr.review_text, rr.created_at, u.full_name as reviewer_name, u.profile_picture as reviewer_picture
            FROM ratings_reviews rr
            JOIN users u ON rr.reviewer_id = u.id
            WHERE rr.provider_id = ? AND rr.service_type = ?
            ORDER BY rr.created_at DESC
        ");
        $stmt->execute([$providerId, $serviceType]);
        return $stmt->fetchAll();
    }
    
    public function isConsultationReviewable($bookingId, $userId) {
        $bookingStmt = $this->conn->prepare("SELECT id FROM consultation_bookings WHERE id = ? AND user_id = ? AND status = 'completed'");
        $bookingStmt->execute([$bookingId, $userId]);
        if (!$bookingStmt->fetch()) {
            return false;
        }

        $reviewStmt = $this->conn->prepare("SELECT id FROM ratings_reviews WHERE service_type = 'psychologist' AND service_id = ? AND reviewer_id = ?");
        $reviewStmt->execute([$bookingId, $userId]);
        if ($reviewStmt->fetch()) {
            return false;
        }
        return true;
    }
}
?>