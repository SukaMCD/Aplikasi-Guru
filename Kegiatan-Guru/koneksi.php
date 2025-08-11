<?php
$host     = "127.0.0.1";
$port     = "5432";
$dbname   = "kegiatanguru_db";
$user     = "postgres";
$password = "admin";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Koneksi gagal: " . pg_last_error());
} else {
    echo "Koneksi berhasil";
}
?>
