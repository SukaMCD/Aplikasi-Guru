<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

include '../../config/koneksi.php';

$message = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'success') {
        $message = '<div class="alert alert-success">User berhasil diupdate!</div>';
    } elseif ($_GET['msg'] === 'error_insert') {
        $message = '<div class="alert alert-danger">Gagal mengupdate user!</div>';
    }
}

$user = null;
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Ambil data user
    $query = "SELECT id_user, username, email, level FROM users WHERE id_user = $1";
    $result = pg_query_params($conn, $query, array($user_id));
    $user = pg_fetch_assoc($result);

    // Bebaskan memori dari hasil query
    pg_free_result($result);

    if (!$user) {
        header('Location: tables.php');
        exit();
    }
} else {
    // Jika tidak ada ID, redirect ke halaman tabel
    header('Location: tables.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Edit User - Kegiatan Guru</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-primary-100">
    <div class="min-height-300 bg-primary position-absolute w-100"></div>

    <main class="main-content position-relative border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Edit User</h6>
                            <a href="tables.php" class="btn btn-sm btn-outline-primary">Kembali ke Tabel</a>
                        </div>
                        <div class="card-body">
                            <?php echo $message; ?>

                            <?php if ($user): ?>
                                <form action="../../app/controllers/proses_edit_user.php" method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id_user']; ?>">

                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="new_password">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                    </div>

                                    <div class="form-group">
                                        <label for="level">Level</label>
                                        <select class="form-control" id="level" name="level" required>
                                            <option value="murid" <?php echo ($user['level'] == 'murid') ? 'selected' : ''; ?>>Murid</option>
                                            <option value="guru" <?php echo ($user['level'] == 'guru') ? 'selected' : ''; ?>>Guru</option>
                                            <option value="admin" <?php echo ($user['level'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update User</button>
                                </form>
                            <?php else: ?>
                                <p>User tidak ditemukan.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>
</html>