<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'guru') {
  header('Location: ../admin/index.php');
  exit();
}
include '../../config/koneksi.php';

$current_user_id = $_SESSION['user_id'];

// Ambil data user saat ini
$query_user = "SELECT id_user, username, email FROM users WHERE id_user = $1";
$result_user = pg_query_params($conn, $query_user, array($current_user_id));
$user = $result_user ? pg_fetch_assoc($result_user) : null;

// Jika user tidak ditemukan, kembali ke halaman profil dengan pesan error
if (!$user) {
  header('Location: profile.php?msg=error&detail=' . urlencode('Profil tidak ditemukan.'));
  exit();
}

// Notifikasi dari proses update
$message = '';
if (isset($_GET['msg'])) {
  if ($_GET['msg'] === 'success') {
    $message = '<div class="alert alert-success mb-4">Profil berhasil diperbarui.</div>';
  } elseif ($_GET['msg'] === 'error') {
    $detail = isset($_GET['detail']) ? htmlspecialchars($_GET['detail']) : 'Terjadi kesalahan.';
    $message = '<div class="alert alert-danger mb-4">' . $detail . '</div>';
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../admin/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Dashboard Guru - Kegiatan Guru</title>

  <!-- Fonts and icons -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Bootstrap Icons -->
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/bootstrap-icons.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>

  <!-- Sidebar -->
  <aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="#">
        <img src="../admin/assets/img/favicon.png" width="26px" height="26px" class="navbar-brand-img h-100" alt="main_logo">
        <!-- Changed brand name to Portal Guru -->
        <span class="ms-1 font-weight-bold">Portal Guru</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link " href="index.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="lihat_kegiatan.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Lihat Kegiatan</span>
          </a>
        </li>
        <!-- Added Tambah Kegiatan menu for guru -->
        <li class="nav-item">
          <a class="nav-link" href="tambah_kegiatan.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-fat-add text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Tambah Kegiatan</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="profile.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../app/controllers/proses_logout.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main-content position-relative border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="false">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Profile</li>
          </ol>
          <!-- Changed title to Dashboard Guru -->
          <h6 class="font-weight-bolder text-white mb-0">Profile Guru</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
              <input type="text" class="form-control" placeholder="Cari kegiatan...">
            </div>
          </div>
          <ul class="navbar-nav justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <a href="../app/controllers/proses_logout.php" class="nav-link text-white font-weight-bold px-0">
                <i class="fa fa-sign-out me-sm-1"></i>
                <span class="d-sm-inline d-none">Logout</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
              <h6>Formulir Edit Profil</h6>
              <a href="profile.php" class="btn btn-sm btn-outline-primary">Kembali ke Profil</a>
            </div>
            <div class="card-body">
              <?php echo $message; ?>
              <form action="../../app/controllers/proses_update_profile.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id_user']); ?>">

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <label for="username" class="form-control-label">Username</label>
                      <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" maxlength="12" required>
                      <small class="text-muted">Maksimal 12 karakter.</small>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <label for="email" class="form-control-label">Email</label>
                      <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                      <small class="text-muted">Email tidak dapat diubah.</small>
                    </div>
                  </div>
                </div>

                <hr class="horizontal dark my-4">
                <h6 class="mb-3">Ubah Password</h6>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <label for="new_password" class="form-control-label">Password Baru</label>
                      <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <label for="confirm_password" class="form-control-label">Konfirmasi Password Baru</label>
                      <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru">
                    </div>
                  </div>
                </div>
                <div class="text-end">
                  <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <footer class="footer pt-3">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                Portal Admin - Kegiatan Guru
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>

  <script src="../admin/assets/js/core/popper.min.js"></script>
  <script src="../admin/assets/js/core/bootstrap.min.js"></script>
  <script src="../admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../admin/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../admin/assets/js/argon-dashboard.min.js?v=2.1.0"></script>
  <script>
    // Validasi form edit profil
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('form');
      const newPassword = document.getElementById('new_password');
      const confirmPassword = document.getElementById('confirm_password');

      form.addEventListener('submit', function(e) {
        const existingError = document.querySelector('.password-error');
        if (existingError) {
          existingError.remove();
        }

        if (newPassword.value || confirmPassword.value) {
          if (newPassword.value !== confirmPassword.value) {
            e.preventDefault();
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger password-error mt-2';
            errorDiv.textContent = 'Konfirmasi password tidak cocok.';
            confirmPassword.parentNode.appendChild(errorDiv);
            confirmPassword.focus();
            return false;
          }

          if (newPassword.value.length < 6) {
            e.preventDefault();
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger password-error mt-2';
            errorDiv.textContent = 'Password minimal 6 karakter.';
            newPassword.parentNode.appendChild(errorDiv);
            newPassword.focus();
            return false;
          }
        }
      });

      [newPassword, confirmPassword].forEach(input => {
        input.addEventListener('input', function() {
          const error = document.querySelector('.password-error');
          if (error) {
            error.remove();
          }
        });
      });
    });
  </script>

</body>

</html>