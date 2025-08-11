<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../../public/murid/index.php');
    exit();
}

include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $level = $_POST['level'] ?? '';

    if ($result_insert) {
        header('Location: ../../public/admin/tables.php?msg=success');
    } else {
        // Tambahkan ini untuk melihat error yang lebih detail
        $error_message = pg_last_error($conn);
        header('Location: ../../public/admin/tambah_user.php?msg=error_insert&detail=' . urlencode($error_message));
    }

    // Cek apakah username sudah ada
    $check_query = "SELECT id_user FROM users WHERE username = $1";
    $result_check = pg_query_params($conn, $check_query, [$username]);

    if (pg_num_rows($result_check) > 0) {
        header('Location: ../../public/admin/tambah_user.php?msg=username_exists');
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user baru
    $insert_query = "INSERT INTO users (username, email, password, level) VALUES ($1, $2, $3, $4)";
    $result_insert = pg_query_params($conn, $insert_query, [$username, $email, $hashed_password, $level]);

    if ($result_insert) {
        header('Location: ../../public/admin/tables.php?msg=success');
    } else {
        header('Location: ../../public/admin/tambah_user.php?msg=error_insert');
    }
    exit();
}
