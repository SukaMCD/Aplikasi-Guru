<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../murid/index.php');
    exit();
}

include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id_guru = trim($_POST['id_guru'] ?? '');
    $id_jenis_kegiatan = trim($_POST['id_jenis_kegiatan'] ?? '');
    $id_kelas = trim($_POST['id_kelas'] ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');
    $laporan = trim($_POST['laporan'] ?? '');
    
    // Validate required fields
    if (empty($id_guru) || empty($id_jenis_kegiatan) || empty($id_kelas) || empty($tanggal) || empty($laporan)) {
        header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_empty');
        exit();
    }
    
    // Validate date format
    if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
        header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_date');
        exit();
    }
    
    // Validate foreign key references exist
    $guru_check = pg_query_params($conn, "SELECT id_guru FROM guru WHERE id_guru = $1", array($id_guru));
    
    $jenis_check = pg_query_params($conn, "SELECT id_jenis_kegiatan FROM jenis_kegiatan WHERE id_jenis_kegiatan = $1", array($id_jenis_kegiatan));
    
    $kelas_check = pg_query_params($conn, "SELECT id_kelas FROM kelas WHERE id_kelas = $1", array($id_kelas));
    
    if (pg_num_rows($guru_check) === 0 || pg_num_rows($jenis_check) === 0 || pg_num_rows($kelas_check) === 0) {
        header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_invalid');
        exit();
    }
    
    // Insert data into kegiatan table
    $insert_query = "INSERT INTO kegiatan (id_guru, id_jenis_kegiatan, id_kelas, tanggal, laporan, created_at) 
                     VALUES ($1, $2, $3, $4, $5, NOW())";
    
    $result = pg_query_params($conn, $insert_query, array(
        $id_guru,
        $id_jenis_kegiatan, 
        $id_kelas,
        $tanggal,
        $laporan
    ));
    
    if ($result) {
        // Success - redirect with success message
        header('Location: ../../public/admin/kegiatan.php?msg=success');
        exit();
    } else {
        // Database error - redirect with error message
        error_log("Database error in proses_tambah_kegiatan.php: " . pg_last_error($conn));
        header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_insert');
        // echo "Gagal: " . pg_last_error($conn);
        exit();
    }
    
} else {
    // If not POST request, redirect back to form
    header('Location: ../../public/admin/tambah_kegiatan.php');
    exit();
}
?>
