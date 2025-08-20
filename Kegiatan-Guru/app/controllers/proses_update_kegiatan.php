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
                $query = "UPDATE kegiatan SET tanggal = $1 WHERE id_kegiatan = $2";
                $result = pg_query_params($conn, $query, [$value, $id_kegiatan]);
                break;

            case 'waktu':
                $timeData = json_decode($value, true);
                $query = "UPDATE kegiatan SET jam_mulai = $1, jam_selesai = $2 WHERE id_kegiatan = $3";
                $result = pg_query_params($conn, $query, [
                    $timeData['jam_mulai'], 
                    $timeData['jam_selesai'], 
                    $id_kegiatan
                ]);
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
