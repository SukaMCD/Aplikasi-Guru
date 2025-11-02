<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'murid') {
    header('Location: ../admin/index.php');
    exit();
}

include '../../config/koneksi.php';

$session_user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Karena user hanya bisa mengakses halaman ini jika sudah login sebagai murid,
    // kita langsung gunakan session user_id tanpa perlu validasi tambahan
    // (sudah divalidasi di bagian atas dengan session check)

    $username = trim($_POST['username'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validasi username tidak kosong
    if (empty($username)) {
        header('Location: edit_profile.php?msg=error&detail=' . urlencode("Username tidak boleh kosong."));
        exit();
    }

    // Validasi panjang username maksimal 12 karakter
    if (strlen($username) > 12) {
        header('Location: edit_profile.php?msg=error&detail=' . urlencode("Username maksimal 12 karakter."));
        exit();
    }

    // Validasi password jika diisi
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            header('Location: edit_profile.php?msg=error&detail=' . urlencode("Password minimal 6 karakter."));
            exit();
        }
        
        if ($new_password !== $confirm_password) {
            header('Location: edit_profile.php?msg=error&detail=' . urlencode("Konfirmasi password tidak cocok."));
            exit();
        }
    }

    // Ambil data user saat ini untuk memastikan status tetap approved
    $check_query = "SELECT status FROM users WHERE id_user = $1";
    $check_result = pg_query_params($conn, $check_query, [$session_user_id]);
    
    if (!$check_result || pg_num_rows($check_result) == 0) {
        header('Location: edit_profile.php?msg=error&detail=' . urlencode("User tidak ditemukan."));
        exit();
    }
    
    $current_user = pg_fetch_assoc($check_result);
    $current_status = trim($current_user['status']);
    
    // Bangun query UPDATE - hanya update field yang diubah, JANGAN sentuh status jika sudah approved
    $params = [];
    $update_fields = [];
    
    // Update username - SELALU update
    $update_fields[] = "username = $" . (count($params) + 1);
    $params[] = $username;

    // Update password (jika diisi)
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_fields[] = "password = $" . (count($params) + 1);
        $params[] = $hashed_password;
    }

    // PENTING: Hanya update status jika status saat ini BUKAN 'approved'
    // Jika sudah approved, JANGAN sentuh kolom status sama sekali
    // Ini mencegah status kembali ke default 'pending'
    if ($current_status !== 'approved') {
        $update_fields[] = "status = $" . (count($params) + 1);
        $params[] = $current_status;
    }

    // Update updated_at
    $update_fields[] = "updated_at = NOW()";

    // Query update - tidak menyentuh status jika sudah approved
    $query = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id_user = $" . (count($params) + 1);
    $params[] = $session_user_id;

    $result = pg_query_params($conn, $query, $params);

    if ($result) {
        // Jika status sudah approved sebelumnya, verifikasi masih approved setelah update
        if ($current_status === 'approved') {
            $verify_query = "SELECT status FROM users WHERE id_user = $1";
            $verify_result = pg_query_params($conn, $verify_query, [$session_user_id]);
            
            if ($verify_result && pg_num_rows($verify_result) > 0) {
                $verify_user = pg_fetch_assoc($verify_result);
                $new_status = trim($verify_user['status']);
                
                // Jika status berubah dari approved ke pending/rejected, kembalikan ke approved
                if ($new_status !== 'approved') {
                    error_log("WARNING: Status changed from approved to " . $new_status . " for user " . $session_user_id . ". Reverting to approved.");
                    $fix_query = "UPDATE users SET status = $1 WHERE id_user = $2";
                    $fix_result = pg_query_params($conn, $fix_query, ['approved', $session_user_id]);
                    if (!$fix_result) {
                        error_log("ERROR: Failed to revert status to approved for user " . $session_user_id);
                    }
                }
            }
        }
        
        // Update session username jika username berubah
        $_SESSION['username'] = $username;
        
        header('Location: profile.php?msg=success');
        exit();
    } else {
        // Tampilkan error detail dari PostgreSQL
        $error_detail = pg_last_error($conn);
        error_log("PostgreSQL Error: " . $error_detail);
        header('Location: edit_profile.php?msg=error&detail=' . urlencode($error_detail));
        exit();
    }
} else {
    header('Location: edit_profile.php?msg=error&detail=' . urlencode("Metode tidak valid."));
    exit();
}
