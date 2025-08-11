<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../../public/murid/index.php');
    exit();
}

include '../../config/koneksi.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Pastikan tidak menghapus admin utama (ID 1)
    if ($user_id == 1) {
        echo "<script>
            alert('Tidak dapat menghapus admin utama!');
            window.location.href = '../../public/admin/tables.php';
        </script>";
        exit();
    }
    
    // Query untuk menghapus user
    $query = "DELETE FROM users WHERE id_user = $1";
    $result = pg_query_params($conn, $query, [$user_id]);
    
    if ($result) {
        echo "<script>
            alert('User berhasil dihapus!');
            window.location.href = '../../public/admin/tables.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menghapus user!');
            window.location.href = '../../public/admin/tables.php';
        </script>";
    }
} else {
    header('Location: ../../public/admin/tables.php');
}

// Menutup koneksi database
pg_close($conn);
?>