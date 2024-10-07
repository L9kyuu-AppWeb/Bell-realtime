<?php
// Set header untuk SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

require_once('config/config.php');

// Fungsi untuk mendapatkan timestamp terakhir dari tabel
function getLastTimestamp($conn) {
    $sql = "SELECT MAX(timestamp) as last_timestamp FROM button_presses";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['last_timestamp'];
}

$lastTimestamp = getLastTimestamp($conn);

while (true) {
    clearstatcache();
    $newTimestamp = getLastTimestamp($conn);

    if ($newTimestamp != $lastTimestamp) {
        // Jika ada data baru, kirim event ke klien
        echo "data: new_data\n\n";
        ob_flush();
        flush();

        // Update lastTimestamp dengan timestamp terbaru
        $lastTimestamp = $newTimestamp;
    }

    // Cek perubahan setiap 3 detik (tanpa polling dari klien)
    sleep(3);
}
?>
