<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../murid/index.php');
    exit();
}

include '../../config/koneksi.php';

$message = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'success') {
        $message = '<div class="alert alert-success">User berhasil ditambahkan!</div>';
    } elseif ($_GET['msg'] === 'error_insert') {
        $message = '<div class="alert alert-danger">Gagal menambahkan user karena masalah database!</div>';
    } elseif ($_GET['msg'] === 'username_exists') {
        $message = '<div class="alert alert-danger">Username sudah digunakan!</div>';
    } elseif ($_GET['msg'] === 'error_empty') {
        $message = '<div class="alert alert-danger">Semua field harus diisi!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Tambah User - Kegiatan Guru</title>
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
                            <div class="d-flex justify-content-between">
                                <h6>Tambah User Baru</h6>
                                <a href="tables.php" class="btn btn-sm btn-outline-primary">Kembali ke Tabel</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php echo $message; ?>
                            
                            <form action="../../app/controllers/proses_tambah_user.php" method="POST">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="level">Level</label>
                                    <select class="form-control" id="level" name="level" required>
                                        <option value="murid" <?php echo (isset($_POST['level']) && $_POST['level'] == 'murid') ? 'selected' : ''; ?>>Murid</option>
                                        <option value="guru" <?php echo (isset($_POST['level']) && $_POST['level'] == 'guru') ? 'selected' : ''; ?>>Guru</option>
                                        <option value="admin" <?php echo (isset($_POST['level']) && $_POST['level'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Tambah User</button>
                            </form>
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