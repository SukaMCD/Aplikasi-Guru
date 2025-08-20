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
    $jam_mulai = trim($_POST['jam_mulai'] ?? '');
    $jam_selesai = trim($_POST['jam_selesai'] ?? '');
    $laporan = trim($_POST['laporan'] ?? '');

    // Validate required fields
    if (empty($id_guru) || empty($id_jenis_kegiatan) || empty($id_kelas) || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai) || empty($laporan)) {
        header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_empty');
        exit();
    }

    // Validate date format
    if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
        header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_date');
        exit();
    }

    if (!DateTime::createFromFormat('H:i', $jam_mulai) || !DateTime::createFromFormat('H:i', $jam_selesai)) {
        header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_time');
        exit();
    }

    $start_time = DateTime::createFromFormat('H:i', $jam_mulai);
    $end_time = DateTime::createFromFormat('H:i', $jam_selesai);
    if ($end_time <= $start_time) {
        header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_time_order');
        exit();
    }

    try {
        // Check if guru exists
        $guru_check = pg_query_params($conn, "SELECT id_guru FROM guru WHERE id_guru = $1", array($id_guru));
        if (!$guru_check || pg_num_rows($guru_check) === 0) {
            error_log("Guru not found: " . $id_guru);
            header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_invalid');
            exit();
        }

        // Check if jenis_kegiatan exists
        $jenis_check = pg_query_params($conn, "SELECT id_jenis_kegiatan FROM jenis_kegiatan WHERE id_jenis_kegiatan = $1", array($id_jenis_kegiatan));
        if (!$jenis_check || pg_num_rows($jenis_check) === 0) {
            error_log("Jenis kegiatan not found: " . $id_jenis_kegiatan);
            header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_invalid');
            exit();
        }

        // Check if kelas exists
        $kelas_check = pg_query_params($conn, "SELECT id_kelas FROM kelas WHERE id_kelas = $1", array($id_kelas));
        if (!$kelas_check || pg_num_rows($kelas_check) === 0) {
            error_log("Kelas not found: " . $id_kelas);
            header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_invalid');
            exit();
        }

        // Check for time conflict in the same class and date
        $class_conflict_query = "
            SELECT k.id_kegiatan, k.jam_mulai, k.jam_selesai, 
                   g.nama_guru, jk.nama_kegiatan, kl.tingkat, kl.jurusan
            FROM kegiatan k
            JOIN guru g ON k.id_guru = g.id_guru
            JOIN jenis_kegiatan jk ON k.id_jenis_kegiatan = jk.id_jenis_kegiatan
            JOIN kelas kl ON k.id_kelas = kl.id_kelas
            WHERE k.id_kelas = $1 
            AND k.tanggal = $2
            AND (
                (k.jam_mulai <= $3::time AND k.jam_selesai > $3::time) OR
                (k.jam_mulai < $4::time AND k.jam_selesai >= $4::time) OR
                (k.jam_mulai >= $3::time AND k.jam_selesai <= $4::time)
            )
        ";
        
        $class_conflict_result = pg_query_params($conn, $class_conflict_query, array(
            $id_kelas, 
            $tanggal, 
            $jam_mulai, 
            $jam_selesai
        ));
        
        if ($class_conflict_result && pg_num_rows($class_conflict_result) > 0) {
            $conflict_data = pg_fetch_assoc($class_conflict_result);
            $kelas_nama = $conflict_data['tingkat'] . ' ' . $conflict_data['jurusan'];
            $guru_nama = $conflict_data['nama_guru'];
            $jenis_kegiatan = $conflict_data['nama_kegiatan'];
            $jam_mulai_existing = date('H:i', strtotime($conflict_data['jam_mulai']));
            $jam_selesai_existing = date('H:i', strtotime($conflict_data['jam_selesai']));
            
            header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_time_conflict&kelas=' . urlencode($kelas_nama) . '&tanggal=' . urlencode($tanggal) . '&jam=' . urlencode($jam_mulai . ' - ' . $jam_selesai) . '&guru=' . urlencode($guru_nama) . '&jenis=' . urlencode($jenis_kegiatan));
            exit();
        }
        
        // Check for teacher conflict (same teacher, same date, overlapping time, different class)
        $teacher_conflict_query = "
            SELECT k.id_kegiatan, k.jam_mulai, k.jam_selesai, 
                   g.nama_guru, jk.nama_kegiatan, kl.tingkat, kl.jurusan
            FROM kegiatan k
            JOIN guru g ON k.id_guru = g.id_guru
            JOIN jenis_kegiatan jk ON k.id_jenis_kegiatan = jk.id_jenis_kegiatan
            JOIN kelas kl ON k.id_kelas = kl.id_kelas
            WHERE k.id_guru = $1 
            AND k.tanggal = $2
            AND k.id_kelas != $3
            AND (
                (k.jam_mulai <= $4::time AND k.jam_selesai > $4::time) OR
                (k.jam_mulai < $5::time AND k.jam_selesai >= $5::time) OR
                (k.jam_mulai >= $4::time AND k.jam_selesai <= $5::time)
            )
        ";
        
        $teacher_conflict_result = pg_query_params($conn, $teacher_conflict_query, array(
            $id_guru, 
            $tanggal, 
            $id_kelas,
            $jam_mulai, 
            $jam_selesai
        ));
        
        if ($teacher_conflict_result && pg_num_rows($teacher_conflict_result) > 0) {
            $conflict_data = pg_fetch_assoc($teacher_conflict_result);
            $kelas_nama = $conflict_data['tingkat'] . ' ' . $conflict_data['jurusan'];
            $guru_nama = $conflict_data['nama_guru'];
            $jenis_kegiatan = $conflict_data['nama_kegiatan'];
            $jam_mulai_existing = date('H:i', strtotime($conflict_data['jam_mulai']));
            $jam_selesai_existing = date('H:i', strtotime($conflict_data['jam_selesai']));
            
            header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_teacher_conflict&kelas=' . urlencode($kelas_nama) . '&tanggal=' . urlencode($tanggal) . '&jam=' . urlencode($jam_mulai . ' - ' . $jam_selesai) . '&guru=' . urlencode($guru_nama) . '&jenis=' . urlencode($jenis_kegiatan));
            exit();
        }

        $status_check = pg_query($conn, "SELECT id_status FROM status_kegiatan WHERE id_status = 1");
        if (!$status_check || pg_num_rows($status_check) === 0) {
            // Create default status if it doesn't exist
            $create_status = pg_query($conn, "INSERT INTO status_kegiatan (id_status, status) VALUES (1, 'Direncanakan') ON CONFLICT (id_status) DO NOTHING");
            if (!$create_status) {
                error_log("Failed to create default status: " . pg_last_error($conn));
            }
        }
    } catch (Exception $e) {
        error_log("Database validation error: " . $e->getMessage());
        header('Location: ../../public/admin/tambah_kegiatan.php?msg=error_invalid');
        exit();
    }

    try {
        // Begin transaction
        pg_query($conn, "BEGIN");

        $insert_query = "INSERT INTO kegiatan (id_guru, id_jenis_kegiatan, id_kelas, tanggal, jam_mulai, jam_selesai, laporan, created_at, id_status) 
                         VALUES ($1, $2, $3, $4, $5::time, $6::time, $7, NOW(), 1)";

        $result = pg_query_params($conn, $insert_query, array(
            $id_guru,
            $id_jenis_kegiatan,
            $id_kelas,
            $tanggal,
            $jam_mulai,
            $jam_selesai,
            $laporan
        ));

        if ($result) {
            // Commit transaction
            pg_query($conn, "COMMIT");
            // Success - redirect with success message
            header('Location: ../../public/admin/kegiatan.php?msg=success');
            exit();
        } else {
            // Rollback transaction
            pg_query($conn, "ROLLBACK");
            throw new Exception(pg_last_error($conn));
        }
    } catch (Exception $e) {
        // Rollback transaction
        pg_query($conn, "ROLLBACK");
        error_log("Database insert error in proses_tambah_kegiatan.php: " . $e->getMessage());
        // Tampilkan error database secara langsung untuk debugging
        echo "Gagal: " . pg_last_error($conn);
        exit();
    }
} else {
    // If not POST request, redirect back to form
    header('Location: ../../public/admin/tambah_kegiatan.php');
    exit();
}
