<?php
session_start();
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

include '../../config/koneksi.php';

$action = $_POST['action'] ?? '';
$user_id = $_POST['user_id'] ?? '';

if (empty($action) || empty($user_id)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters'
    ]);
    exit;
}

// Validate user exists and is pending
$check_query = "SELECT username, email, status FROM users WHERE id_user = $1";
$check_result = pg_query_params($conn, $check_query, [$user_id]);

if (!$check_result || pg_num_rows($check_result) == 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found'
    ]);
    exit;
}

$user = pg_fetch_assoc($check_result);

if ($user['status'] !== 'pending') {
    echo json_encode([
        'status' => 'error',
        'message' => 'User is not pending approval'
    ]);
    exit;
}

// Process approval or rejection
$new_status = '';
$message = '';

switch ($action) {
    case 'approve':
        $new_status = 'approved';
        $message = 'User ' . $user['username'] . ' has been approved successfully';
        break;
    case 'reject':
        $new_status = 'rejected';
        $message = 'User ' . $user['username'] . ' has been rejected';
        break;
    default:
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action'
        ]);
        exit;
}

// Update user status
$update_query = "UPDATE users SET status = $1, updated_at = NOW() WHERE id_user = $2";
$update_result = pg_query_params($conn, $update_query, [$new_status, $user_id]);

if ($update_result) {
    // Log the approval/rejection action
    $log_query = "INSERT INTO approval_logs (admin_id, user_id, action, created_at) VALUES ($1, $2, $3, NOW())";
    pg_query_params($conn, $log_query, [$_SESSION['user_id'], $user_id, $action]);
    
    echo json_encode([
        'status' => 'success',
        'message' => $message
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update user status: ' . pg_last_error($conn)
    ]);
}

pg_close($conn);
?>
