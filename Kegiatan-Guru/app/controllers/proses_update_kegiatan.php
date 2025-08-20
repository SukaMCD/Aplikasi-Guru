<?php
session_start();
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include '../../config/koneksi.php';

// Function to check time conflicts for updates
function checkTimeConflict($conn, $id_kegiatan, $tanggal = null, $jam_mulai = null, $jam_selesai = null, $id_kelas = null) {
    // Get current kegiatan data if not provided
    if (!$tanggal || !$jam_mulai || !$jam_selesai || !$id_kelas) {
        $current_query = "SELECT tanggal, jam_mulai, jam_selesai, id_kelas, id_guru FROM kegiatan WHERE id_kegiatan = $1";
        $current_result = pg_query_params($conn, $current_query, [$id_kegiatan]);
        if (!$current_result || pg_num_rows($current_result) === 0) {
            return ['has_conflict' => false, 'conflict_info' => ''];
        }
        $current_data = pg_fetch_assoc($current_result);
        
        $tanggal = $tanggal ?: $current_data['tanggal'];
        $jam_mulai = $jam_mulai ?: $current_data['jam_mulai'];
        $jam_selesai = $jam_selesai ?: $current_data['jam_selesai'];
        $id_kelas = $id_kelas ?: $current_data['id_kelas'];
        $id_guru = $current_data['id_guru'];
    } else {
        // Get guru ID for the kegiatan
        $guru_query = "SELECT id_guru FROM kegiatan WHERE id_kegiatan = $1";
        $guru_result = pg_query_params($conn, $guru_query, [$id_kegiatan]);
        $guru_data = pg_fetch_assoc($guru_result);
        $id_guru = $guru_data['id_guru'];
    }
    
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
        AND k.id_kegiatan != $3
        AND (
            (k.jam_mulai <= $4::time AND k.jam_selesai > $4::time) OR
            (k.jam_mulai < $5::time AND k.jam_selesai >= $5::time) OR
            (k.jam_mulai >= $4::time AND k.jam_selesai <= $5::time)
        )
    ";
    
    $class_conflict_result = pg_query_params($conn, $class_conflict_query, [
        $id_kelas, $tanggal, $id_kegiatan, $jam_mulai, $jam_selesai
    ]);
    
    if ($class_conflict_result && pg_num_rows($class_conflict_result) > 0) {
        $conflict_data = pg_fetch_assoc($class_conflict_result);
        $kelas_nama = $conflict_data['tingkat'] . ' ' . $conflict_data['jurusan'];
        $guru_nama = $conflict_data['nama_guru'];
        $jenis_kegiatan = $conflict_data['nama_kegiatan'];
        $jam_mulai_existing = date('H:i', strtotime($conflict_data['jam_mulai']));
        $jam_selesai_existing = date('H:i', strtotime($conflict_data['jam_selesai']));
        
        return [
            'has_conflict' => true,
            'conflict_info' => "Sudah ada kegiatan {$jenis_kegiatan} oleh {$guru_nama} di {$kelas_nama} pada jam {$jam_mulai_existing} - {$jam_selesai_existing}."
        ];
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
        AND k.id_kegiatan != $3
        AND k.id_kelas != $4
        AND (
            (k.jam_mulai <= $5::time AND k.jam_selesai > $5::time) OR
            (k.jam_mulai < $6::time AND k.jam_selesai >= $6::time) OR
            (k.jam_mulai >= $5::time AND k.jam_selesai <= $6::time)
        )
    ";
    
    $teacher_conflict_result = pg_query_params($conn, $teacher_conflict_query, [
        $id_guru, $tanggal, $id_kegiatan, $id_kelas, $jam_mulai, $jam_selesai
    ]);
    
    if ($teacher_conflict_result && pg_num_rows($teacher_conflict_result) > 0) {
        $conflict_data = pg_fetch_assoc($teacher_conflict_result);
        $kelas_nama = $conflict_data['tingkat'] . ' ' . $conflict_data['jurusan'];
        $guru_nama = $conflict_data['nama_guru'];
        $jenis_kegiatan = $conflict_data['nama_kegiatan'];
        $jam_mulai_existing = date('H:i', strtotime($conflict_data['jam_mulai']));
        $jam_selesai_existing = date('H:i', strtotime($conflict_data['jam_selesai']));
        
        return [
            'has_conflict' => true,
            'conflict_info' => "Guru {$guru_nama} sudah mengajar {$jenis_kegiatan} di {$kelas_nama} pada jam {$jam_mulai_existing} - {$jam_selesai_existing}."
        ];
    }
    
    return ['has_conflict' => false, 'conflict_info' => ''];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kegiatan = $_POST['id_kegiatan'] ?? '';
    $field_type = $_POST['field_type'] ?? '';
    $value = $_POST['value'] ?? '';

    if (empty($id_kegiatan) || empty($field_type)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }

    try {
        switch ($field_type) {
            case 'tanggal':
                // Check for time conflict before updating date
                $conflict_result = checkTimeConflict($conn, $id_kegiatan, $value, null, null, null);
                if ($conflict_result['has_conflict']) {
                    echo json_encode(['success' => false, 'message' => 'Konflik waktu: ' . $conflict_result['conflict_info']]);
                    exit();
                }
                
                $query = "UPDATE kegiatan SET tanggal = $1 WHERE id_kegiatan = $2";
                $result = pg_query_params($conn, $query, [$value, $id_kegiatan]);
                break;

            case 'waktu':
                $timeData = json_decode($value, true);
                
                // Check for time conflict before updating time
                $conflict_result = checkTimeConflict($conn, $id_kegiatan, null, $timeData['jam_mulai'], $timeData['jam_selesai'], null);
                if ($conflict_result['has_conflict']) {
                    echo json_encode(['success' => false, 'message' => 'Konflik waktu: ' . $conflict_result['conflict_info']]);
                    exit();
                }
                
                $query = "UPDATE kegiatan SET jam_mulai = $1, jam_selesai = $2 WHERE id_kegiatan = $3";
                $result = pg_query_params($conn, $query, [
                    $timeData['jam_mulai'], 
                    $timeData['jam_selesai'], 
                    $id_kegiatan
                ]);
                break;

            case 'guru':
                // Check for time conflict before updating guru
                $conflict_result = checkTimeConflict($conn, $id_kegiatan, null, null, null, null);
                if ($conflict_result['has_conflict']) {
                    echo json_encode(['success' => false, 'message' => 'Konflik waktu: ' . $conflict_result['conflict_info']]);
                    exit();
                }
                
                $query = "UPDATE kegiatan SET id_guru = $1 WHERE id_kegiatan = $2";
                $result = pg_query_params($conn, $query, [$value, $id_kegiatan]);
                break;

            case 'jenis':
                $query = "UPDATE kegiatan SET id_jenis_kegiatan = $1 WHERE id_kegiatan = $2";
                $result = pg_query_params($conn, $query, [$value, $id_kegiatan]);
                break;

            case 'kelas':
                // Check for time conflict before updating kelas
                $conflict_result = checkTimeConflict($conn, $id_kegiatan, null, null, null, $value);
                if ($conflict_result['has_conflict']) {
                    echo json_encode(['success' => false, 'message' => 'Konflik waktu: ' . $conflict_result['conflict_info']]);
                    exit();
                }
                
                $query = "UPDATE kegiatan SET id_kelas = $1 WHERE id_kegiatan = $2";
                $result = pg_query_params($conn, $query, [$value, $id_kegiatan]);
                break;

            case 'laporan':
                $query = "UPDATE kegiatan SET laporan = $1 WHERE id_kegiatan = $2";
                $result = pg_query_params($conn, $query, [$value, $id_kegiatan]);
                break;

            case 'status':
                $query = "UPDATE kegiatan SET id_status = $1 WHERE id_kegiatan = $2";
                $result = pg_query_params($conn, $query, [$value, $id_kegiatan]);
                
                // Get status name for response
                $status_query = "SELECT status FROM status_kegiatan WHERE id_status = $1";
                $status_result = pg_query_params($conn, $status_query, [$value]);
                $status_row = pg_fetch_assoc($status_result);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid field type']);
                exit();
        }

        if ($result) {
            $response = ['success' => true, 'message' => 'Update successful'];
            
            // Add status name to response if updating status
            if ($field_type === 'status' && isset($status_row)) {
                $response['status_name'] = $status_row['status'];
            }
            
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database update failed']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
