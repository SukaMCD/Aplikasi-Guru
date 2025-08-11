<?php
if (basename($_SERVER['PHP_SELF']) == 'koneksi.php') {
    header("Location: index.php");
    exit();
}

// Deteksi apakah sedang di localhost
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // Konfigurasi untuk localhost
    $host = "localhost";
    $user = "root"; // biasanya default di localhost
    $password = ""; // kosong jika tidak ada password
    $database = "Leafly"; // sesuaikan dengan nama database lokal kamu
} else {
    // Konfigurasi untuk hosting
    $host = "localhost";
    $user = "u993542331_adxuser";
    $password = "BMXRider123";
    $database = "u993542331_digital";
}

$koneksi = mysqli_connect($host, $user, $password, $database);
if (mysqli_connect_errno()) {
    echo "Koneksi database gagal: " . mysqli_connect_error();
    exit();
}
?>
