<?php
// File: functions/helper_functions.php
// Kumpulan fungsi bantuan yang sering digunakan

/**
 * Fungsi untuk membersihkan input dari potensi XSS dan spasi berlebih.
 * @param string $data Input string.
 * @return string String yang sudah dibersihkan.
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = e($data); // gunakan e() yang aman
    return $data;
}

/**
 * Fungsi untuk menampilkan teks dengan aman ke HTML.
 * Aman terhadap null dan XSS.
 * @param mixed $value Nilai yang akan di-escape.
 * @return string
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Versi echo langsung dari fungsi e().
 * @param mixed $value
 */
function ee($value) {
    echo e($value);
}

/**
 * Fungsi untuk redirect ke halaman lain.
 * @param string $url URL tujuan.
 */
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

/**
 * Memformat tanggal ke format Indonesia (contoh: 06 Juli 2025, 20:00).
 * @param string $datetimeString String tanggal dari database.
 * @return string Tanggal yang sudah diformat atau pesan error jika tidak valid.
 */
function format_indonesian_date(string $datetimeString): string
{
    if (empty($datetimeString)) {
        return 'Waktu tidak valid';
    }
    $timestamp = strtotime($datetimeString);
    if ($timestamp === false) {
        return 'Format waktu tidak valid';
    }

    $hari = date('d', $timestamp);
    $bulan_array = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $bulan = $bulan_array[date('n', $timestamp)];
    $tahun = date('Y', $timestamp);
    $waktu = date('H:i', $timestamp);

    return "$hari $bulan $tahun, $waktu";
}

/**
 * Fungsi untuk memotong teks dan menambahkan elipsis (...) jika terlalu panjang.
 * @param string $text Teks asli.
 * @param int $length Panjang maksimal sebelum dipotong.
 * @return string Teks yang sudah dipotong.
 */
function truncate_text($text, $length = 100) {
    $text = (string) $text;
    if (mb_strlen($text) > $length) {
        $text = mb_substr($text, 0, $length) . '...';
    }
    return $text;
}
?>
