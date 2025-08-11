<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../../murid/index.php');
    exit();
}

include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $level = $_POST['level'];
    $new_password = $_POST['new_password'];

    $result_update = false;

    // Update user
    if (!empty($new_password)) {
        // Jika ada password baru, hash password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET username = $1, email = $2, password = $3, level = $4 WHERE id_user = $5";
        $result_update = pg_query_params($conn, $query, [$username, $email, $hashed_password, $level, $user_id]);
    } else {
        // Jika tidak ada password baru, update tanpa password
        $query = "UPDATE users SET username = $1, email = $2, level = $3 WHERE id_user = $4";
        $result_update = pg_query_params($conn, $query, [$username, $email, $level, $user_id]);
    }

    if ($result_update) {
        // Berhasil, redirect ke halaman tables.php
        header('Location: ../../public/admin/tables.php?msg=success');
    } else {
        // Gagal, redirect ke halaman edit user dengan pesan error
        header('Location: ../../public/admin/edit_user.php?id=' . $user_id . '&msg=error_insert');
    }
    exit();
} else {
    // Jika diakses tanpa method POST, redirect ke halaman tabel
    header('Location: tables.php');
    exit();
}
?>