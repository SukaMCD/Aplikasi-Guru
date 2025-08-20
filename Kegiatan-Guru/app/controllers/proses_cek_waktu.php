<?php
session_start();
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    $jam_mulai = $_POST['jam_mulai'] ?? '';
    $jam_selesai = $_POST['jam_selesai'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $check_conflict = $_POST['check_conflict'] ?? '';

    if (empty($tanggal) || empty($jam_mulai) || empty($jam_selesai) || empty($id_kelas)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }

    try {
        // Get guru ID for the selected kelas
        $guru_query = "SELECT g.id_guru, g.nama_guru FROM guru g 
                       JOIN kegiatan k ON g.id_guru = k.id_guru 
                       WHERE k.id_kelas = $1 LIMIT 1";
        $guru_result = pg_query_params($conn, $guru_query, [$id_kelas]);
        $guru_data = pg_fetch_assoc($guru_result);
        $id_guru = $guru_data['id_guru'] ?? null;
        
        // Check for class conflict (same class, same date, overlapping time)
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
            
            $conflict_info = "Sudah ada kegiatan <strong>{$jenis_kegiatan}</strong> oleh <strong>{$guru_nama}</strong> di {$kelas_nama} pada jam {$jam_mulai_existing} - {$jam_selesai_existing}.";
            
            echo json_encode([
                'success' => true,
                'has_conflict' => true,
                'conflict_info' => $conflict_info,
                'conflict_data' => $conflict_data
            ]);
            exit();
        }
        
        // Check for teacher conflict (same teacher, same date, overlapping time, different class)
        if ($id_guru) {
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
                
                $conflict_info = "Guru <strong>{$guru_nama}</strong> sudah mengajar <strong>{$jenis_kegiatan}</strong> di {$kelas_nama} pada jam {$jam_mulai_existing} - {$jam_selesai_existing}.";
                
                echo json_encode([
                    'success' => true,
                    'has_conflict' => true,
                    'conflict_info' => $conflict_info,
                    'conflict_data' => $conflict_data
                ]);
                exit();
            }
        }
        
        // No conflicts found
        echo json_encode([
            'success' => true,
            'has_conflict' => false,
            'conflict_info' => null
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage(),
            'has_conflict' => false
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
