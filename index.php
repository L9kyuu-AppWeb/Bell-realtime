<?php
require_once('config/config.php');
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect jika pengguna tidak login
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy(); // Hentikan sesi
    header('Location: login.php'); // Redirect ke login setelah logout
    exit();
}

if (isset($_POST['reset'])) {
    // Perintah SQL untuk reset data
    $reset_sql = "DELETE FROM button_presses";
    if ($conn->query($reset_sql) === TRUE) {
        echo '<script>alert("Data tombol telah di-reset.");</script>';
        echo '<script>window.location.href = "index.php";</script>'; // Reload halaman
    } else {
        echo '<script>alert("Error: ' . $reset_sql . '\n' . $conn->error . '");</script>';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0 40px;
        padding: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .index-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        text-align: left;
        max-width: 800px;
        width: 100%;
    }

    .logout-button, .reset-button {
        float: right;
        background-color: #007BFF;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;
        padding: 5px 10px;
        transition: background-color 0.3s;
    }

    .logout-button {
        background-color: #dc3545;
    }

    .reset-button:hover,
    .logout-button:hover {
        background-color: #0056b3;
    }

    .table-container {
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ccc;
    }

    table th,
    table td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ccc;
    }

    table th {
        background-color: #f5f5f5;
    }

    .icon-logout {
        font-size: 16px;
    }
    </style>
</head>

<body>
    <div class="index-container">
        <form method="post">
            <button class="logout-button" type="submit" name="logout">
                <i class="fas fa-sign-out-alt icon-logout"></i>
            </button>
            <?php if ($_SESSION['role'] === 'admin') { ?>
                <button class="reset-button" type="submit" name="reset">
                    <i class="fas fa-sync-alt"></i>
                </button>
            <?php } ?>
        </form>

        <div class="table-container">
            <h2>Monitoring Board</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan dimasukkan di sini melalui AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Audio elemen untuk nada panggil -->
        <audio id="notification-sound" src="notification.wav" preload="auto"></audio>
    </div>

    <script type="text/javascript">
    function fetchData() {
        $.ajax({
            url: 'fetch_data.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                var tableContent = '';
                $.each(data, function(index, value) {
                    tableContent += '<tr>';
                    tableContent += '<td>' + value.username + '</td>';
                    tableContent += '<td>' + value.timestamp + '</td>';
                    tableContent += '</tr>';
                });
                $('table tbody').html(tableContent);
            }
        });
    }

    if (typeof(EventSource) !== "undefined") {
        var source = new EventSource("events.php");
        source.onmessage = function(event) {
            if (event.data === "new_data") {
                // Panggil AJAX untuk mengambil data terbaru
                fetchData();

                // Putar nada panggil saat ada data baru
                var audio = document.getElementById('notification-sound');
                audio.play();
            }
        };
    } else {
        console.log("Browser tidak mendukung Server-Sent Events.");
    }

    $(document).ready(function() {
        // Ambil data pertama kali saat halaman dimuat
        fetchData();
    });
    </script>
</body>

</html>
