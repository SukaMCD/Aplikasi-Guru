<?php

// Tentukan masa berlaku cookie session
// Ini harus dipanggil sebelum session_start()
if (isset($_POST['rememberMe']) && $_POST['rememberMe'] === 'on') {
    // Jika 'Remember Me' dicentang, atur masa aktif 30 hari (30 * 24 * 60 * 60 detik)
    $lifetime = 30 * 24 * 60 * 60;
    session_set_cookie_params($lifetime);
} else {
    // Jika tidak, gunakan masa aktif default (biasanya sampai browser ditutup)
    session_set_cookie_params(0);
}

session_start(); // Harus di awal

include '../../config/koneksi.php';

header('Content-Type: application/json');

$username_or_email = trim($_POST['username']);
$password = trim($_POST['password']);

if (empty($username_or_email) || empty($password)) {
    echo json_encode([
        'status' => 'error',
        'title' => 'Login Gagal!',
        'message' => 'Username/Email dan password tidak boleh kosong.',
        'icon' => 'warning'
    ]);
    exit;
}

$query = "SELECT * FROM users WHERE username = $1 OR email = $1";
$result = pg_query_params($conn, $query, array($username_or_email));

if (!$result) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan pada server.'
    ]);
    exit;
}

$user = pg_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    if ($user['status'] !== 'approved') {
        $status_message = '';
        switch ($user['status']) {
            case 'pending':
                $status_message = 'Akun Anda masih menunggu persetujuan admin. Silakan hubungi administrator.';
                break;
            case 'rejected':
                $status_message = 'Akun Anda telah ditolak. Silakan hubungi administrator untuk informasi lebih lanjut.';
                break;
            default:
                $status_message = 'Status akun Anda tidak valid. Silakan hubungi administrator.';
                break;
        }
        
        echo json_encode([
            'status' => 'error',
            'title' => 'Akses Ditolak!',
            'message' => $status_message,
            'icon' => 'warning'
        ]);
        exit;
    }

    // Regenerasi ID session untuk keamanan
    session_regenerate_id(true);

    // Simpan user_id ke session
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['user'] = $user;
    $_SESSION['username'] = $user['username'];
    $_SESSION['level'] = $user['level'];

    $redirect = '';
    switch ($user['level']) {
        case 'admin':
            $redirect = 'Kegiatan-Guru/public/admin/index.php';
            break;
        case 'guru':
            $redirect = 'Kegiatan-Guru/public/guru/index.php';
            break;
        case 'murid':
            $redirect = 'Kegiatan-Guru/public/murid/index.php';
            break;
        default:
            $redirect = 'kegiatan-Guru/index.php'; // Path default jika level tidak dikenali
            break;
    }

    echo json_encode([
        'status' => 'success',
        'title' => 'Login Berhasil!',
        'message' => 'Selamat datang kembali, ' . $user['username'] . '!',
        'icon' => 'success',
        'redirect' => $redirect
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'title' => 'Login Gagal!',
        'message' => 'Username/Email atau password salah.',
        'icon' => 'error'
    ]);
}

pg_close($conn);

?>
