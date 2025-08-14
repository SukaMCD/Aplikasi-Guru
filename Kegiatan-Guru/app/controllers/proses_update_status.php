<?php
session_start();
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include '../../config/koneksi.php';

// Check if required data is provided
if (!isset($_POST['id_kegiatan']) || !isset($_POST['id_status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

$id_kegiatan = $_POST['id_kegiatan'];
$id_status = $_POST['id_status'];

// Validate status ID
if (!in_array($id_status, ['1', '2', '3', '4'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status ID']);
    exit();
}

try {
    // Update the status
    $query = "UPDATE kegiatan SET id_status = $1 WHERE id_kegiatan = $2";
    $result = pg_query_params($conn, $query, array($id_status, $id_kegiatan));
    
    if ($result) {
        // Get the status name
        $status_query = "SELECT status FROM status_kegiatan WHERE id_status = $1";
        $status_result = pg_query_params($conn, $status_query, array($id_status));
        $status_row = pg_fetch_assoc($status_result);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Status updated successfully',
            'status_name' => $status_row['status']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

pg_close($conn);
?>
