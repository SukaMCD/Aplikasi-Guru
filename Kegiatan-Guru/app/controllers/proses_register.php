<?php
header("Content-Type: application/json");
include '../../config/koneksi.php';

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);
$confirm = trim($_POST['confirm_password']);
$role = trim($_POST['role']);

// Validasi field kosong
if (empty($username) || empty($email) || empty($password) || empty($confirm) || empty($role)) {
    echo json_encode([
        "status" => "error",
        "title" => "Gagal!",
        "message" => "Semua field harus diisi.",
        "icon" => "error"
    ]);
    exit;
}

if (!in_array($role, ['guru', 'murid'])) {
    echo json_encode([
        "status" => "error",
        "title" => "Gagal!",
        "message" => "Role tidak valid.",
        "icon" => "error"
    ]);
    exit;
}

// Validasi panjang username maksimal 12 karakter
if (strlen($username) > 12) {
    echo json_encode([
        "status" => "error",
        "title" => "Username Terlalu Panjang!",
        "message" => "Username maksimal 12 karakter.",
        "icon" => "warning"
    ]);
    exit;
}

// Validasi panjang password minimal 6 karakter
if (strlen($password) < 6) {
    echo json_encode([
        "status" => "error",
        "title" => "Password Terlalu Pendek!",
        "message" => "Password minimal 6 karakter.",
        "icon" => "warning"
    ]);
    exit;
}

// Validasi kesamaan password
if ($password !== $confirm) {
    echo json_encode([
        "status" => "error",
        "title" => "Gagal!",
        "message" => "Password dan konfirmasi tidak cocok.",
        "icon" => "warning"
    ]);
    exit;
}

// Cek apakah username atau email sudah digunakan
$check_query = "SELECT * FROM users WHERE username = $1 OR email = $2";
$check_result = pg_query_params($conn, $check_query, [$username, $email]);

if (pg_num_rows($check_result) > 0) {
    $existing_user = pg_fetch_assoc($check_result);
    if ($existing_user['username'] === $username) {
        $message = "Username sudah digunakan. Gunakan username lain.";
    } else {
        $message = "Email sudah digunakan. Gunakan email lain.";
    }
    echo json_encode([
        "status" => "error",
        "title" => "Registrasi Gagal!",
        "message" => $message,
        "icon" => "warning"
    ]);
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$insert_query = "INSERT INTO users (username, email, password, level, status, created_at) VALUES ($1, $2, $3, $4, 'pending', NOW())";
$insert_result = pg_query_params($conn, $insert_query, [$username, $email, $hashed_password, $role]);

if ($insert_result) {
    echo json_encode([
        "status" => "success",
        "title" => "Berhasil!",
        "message" => "Registrasi berhasil! Akun Anda menunggu persetujuan admin. Anda akan dihubungi setelah akun disetujui.",
        "icon" => "success",
        "redirect" => "/KODINGAN/PWPB/Aplikasi-Guru/"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "title" => "Gagal!",
        "message" => "Terjadi kesalahan saat registrasi: " . pg_last_error($conn),
        "icon" => "error"
    ]);
}

pg_close($conn);
?>
