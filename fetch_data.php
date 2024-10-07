<?php
require_once('config/config.php');

// Query untuk mendapatkan data dari tabel
$sql = "SELECT u.username, bp.timestamp
        FROM button_presses bp
        JOIN users u ON bp.user_id = u.id
        ORDER BY bp.timestamp ASC";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Mengirim data dalam format JSON
echo json_encode($data);
?>
