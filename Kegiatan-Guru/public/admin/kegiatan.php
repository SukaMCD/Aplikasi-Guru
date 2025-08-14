<?php
session_start(); // Pastikan session dimulai
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
  header('Location: ../murid/index.php'); // Redirect ke halaman login jika bukan admin
  exit();
}
include '../../config/koneksi.php'; // Pastikan koneksi database sudah benar

function updateStatusBasedOnDate($conn) {
    $today = date('Y-m-d');
    
    // Update status to 'Berlangsung' for activities scheduled today that are still 'Direncanakan'
    $query1 = "UPDATE kegiatan SET id_status = 2 
               WHERE DATE(tanggal) = '$today' 
               AND id_status = 1";
    pg_query($conn, $query1);
    
    $query2 = "UPDATE kegiatan SET id_status = 3 
               WHERE DATE(tanggal) < '$today' 
               AND id_status IN (1, 2)"; // Only update if status is 'Direncanakan' or 'Berlangsung'
    pg_query($conn, $query2);
}

// Call the auto-update function
updateStatusBasedOnDate($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Activity - Kegiatan Guru
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  <aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/argon-dashboard/pages/dashboard.html " target="_blank">
        <img src="../assets/img/favicon.png" width="26px" height="26px" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold">Activity Guru</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="tables.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-circle-08 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Users</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="kegiatan.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Activity</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link " href="virtual-reality.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-app text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Virtual Reality</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link " href="rtl.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-world-2 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">RTL</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account pages</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link " href="profile.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link " href="../../app/controllers/proses_logout.php">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Logout</span>
          </a>
        </li>
      </ul>
    </div>
    <div class="sidenav-footer mx-3 ">
      <div class="card card-plain shadow-none" id="sidenavCard">
        <img class="w-50 mx-auto" src="../assets/img/illustrations/icon-documentation.svg" alt="sidebar_illustration">
        <div class="card-body text-center p-3 w-100 pt-0">
          <div class="docs-info">
            <h6 class="mb-0">Need help?</h6>
            <p class="text-xs font-weight-bold mb-0">Please check our docs</p>
          </div>
        </div>
      </div>
      <a href="https://www.creative-tim.com/learning-lab/bootstrap/license/argon-dashboard" target="_blank" class="btn btn-dark btn-sm w-100 mb-3">Documentation</a>
      <a class="btn btn-primary btn-sm mb-0 w-100" href="https://www.creative-tim.com/product/argon-dashboard-pro?ref=sidebarfree" type="button">Upgrade to pro</a>
    </div>
  </aside>
  <main class="main-content position-relative border-radius-lg ">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Activity</li>
          </ol>
          <h6 class="font-weight-bolder text-white mb-0">Activity</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
              <input type="text" class="form-control" placeholder="Type here...">
            </div>
          </div>
          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <a href="../../app/controllers/proses_logout.php" class="nav-link text-white font-weight-bold px-0">
                <i class="fa fa-sign-out me-sm-1"></i>
                <span class="d-sm-inline d-none">Logout</span>
              </a>
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line bg-white"></i>
                  <i class="sidenav-toggler-line bg-white"></i>
                  <i class="sidenav-toggler-line bg-white"></i>
                </div>
              </a>
            </li>
            <li class="nav-item px-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0">
                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
              </a>
            </li>
            <li class="nav-item dropdown pe-2 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-white p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-bell cursor-pointer"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <?php
      // Query untuk statistik kegiatan dengan status baru
      $query_total = "SELECT COUNT(*) as total FROM kegiatan";
      $result_total = pg_query($conn, $query_total);
      $total_kegiatan = pg_fetch_assoc($result_total)['total'];

      $query_bulan_ini = "SELECT COUNT(*) as total FROM kegiatan WHERE EXTRACT(MONTH FROM tanggal) = EXTRACT(MONTH FROM CURRENT_DATE) AND EXTRACT(YEAR FROM tanggal) = EXTRACT(YEAR FROM CURRENT_DATE)";
      $result_bulan_ini = pg_query($conn, $query_bulan_ini);
      $bulan_ini = pg_fetch_assoc($result_bulan_ini)['total'];

      // Updated query to count completed activities based on status
      $query_selesai = "SELECT COUNT(*) as total FROM kegiatan WHERE id_status = 3"; // 3 = Selesai
      $result_selesai = pg_query($conn, $query_selesai);
      $selesai = pg_fetch_assoc($result_selesai)['total'];

      $progress = $total_kegiatan > 0 ? round(($selesai / $total_kegiatan) * 100) : 0;
      ?>
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Kegiatan</p>
                    <h5 class="font-weight-bolder"><?php echo $total_kegiatan; ?></h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                    <i class="ni ni-books text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Bulan Ini</p>
                    <h5 class="font-weight-bolder"><?php echo $bulan_ini; ?></h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                    <i class="ni ni-calendar-grid-58 text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Selesai</p>
                    <h5 class="font-weight-bolder"><?php echo $selesai; ?></h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Progress</p>
                    <h5 class="font-weight-bolder"><?php echo $progress; ?>%</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                    <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modified table to include database-connected status -->
      <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between">
                <h6>Daftar Kegiatan</h6>
                <a href="tambah_kegiatan.php" class="btn btn-sm btn-outline-primary">Tambah Kegiatan</a>
              </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0 border">
                  <thead>
                    <tr class="border-bottom">
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">ID</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Guru</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Jenis Kegiatan</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Kelas</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Laporan</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Tanggal</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Detail</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // Modified query to include status from database
                    $query_kegiatan = "
                      SELECT 
                        k.id_kegiatan,
                        g.nama_guru,
                        jk.nama_kegiatan as jenis_kegiatan,
                        CONCAT(kl.tingkat, ' ', kl.jurusan) as kelas,
                        k.tanggal,
                        k.laporan,
                        k.id_status,
                        sk.status as status_name
                      FROM kegiatan k
                      LEFT JOIN guru g ON k.id_guru = g.id_guru
                      LEFT JOIN jenis_kegiatan jk ON k.id_jenis_kegiatan = jk.id_jenis_kegiatan
                      LEFT JOIN kelas kl ON k.id_kelas = kl.id_kelas
                      LEFT JOIN status_kegiatan sk ON k.id_status = sk.id_status
                      ORDER BY k.tanggal DESC
                    ";
                    $result_kegiatan = pg_query($conn, $query_kegiatan);

                    if ($result_kegiatan && pg_num_rows($result_kegiatan) > 0) {
                      $no = 1;
                      while ($row = pg_fetch_assoc($result_kegiatan)) {
                        // Set status badge color based on status
                        $status_class = '';
                        switch($row['id_status']) {
                          case '1': // Direncanakan
                            $status_class = 'bg-gradient-secondary';
                            break;
                          case '2': // Berlangsung
                            $status_class = 'bg-gradient-warning';
                            break;
                          case '3': // Selesai
                            $status_class = 'bg-gradient-success';
                            break;
                          case '4': // Dibatalkan
                            $status_class = 'bg-gradient-danger';
                            break;
                          default:
                            $status_class = 'bg-gradient-secondary';
                        }

                        $detail_class = 'bg-gradient-info';

                        $laporan_short = !empty($row['laporan']) ?
                          (strlen($row['laporan']) > 30 ? substr($row['laporan'], 0, 30) . '...' : $row['laporan']) :
                          'N/A';
                    ?>
                        <tr class="border-bottom">
                          <td class="border-end text-center">
                            <div class="d-flex justify-content-center px-3 py-2">
                              <h6 class="mb-0 text-sm font-weight-bold"><?php echo $no; ?></h6>
                            </div>
                          </td>
                          <td class="border-end">
                            <div class="d-flex px-3 py-2">
                              <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm font-weight-bold"><?php echo $row['nama_guru'] ?? 'N/A'; ?></h6>
                              </div>
                            </div>
                          </td>
                          <td class="border-end">
                            <div class="d-flex px-3 py-2">
                              <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm"><?php echo $row['jenis_kegiatan'] ?? 'N/A'; ?></h6>
                              </div>
                            </div>
                          </td>
                          <td class="border-end">
                            <div class="d-flex px-3 py-2">
                              <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm"><?php echo $row['kelas'] ?? 'N/A'; ?></h6>
                              </div>
                            </div>
                          </td>
                          <td class="border-end">
                            <div class="d-flex px-3 py-2">
                              <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm"><?php echo $laporan_short; ?></h6>
                              </div>
                            </div>
                          </td>
                          <td class="border-end">
                            <div class="d-flex px-3 py-2">
                              <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></h6>
                              </div>
                            </div>
                          </td>
                          <td class="border-end align-middle text-center text-sm">
                            <span class="badge badge-sm <?php echo $status_class; ?>"><?php echo $row['status_name'] ?? 'N/A'; ?></span>
                          </td>
                          <td class="align-middle text-center">
                            <!-- Modified Detail button to use badge style like status -->
                            <span class="badge badge-sm <?php echo $detail_class; ?> cursor-pointer"
                              onclick="showDetailModal('<?php echo $row['id_kegiatan']; ?>',
                                                           '<?php echo addslashes($row['nama_guru'] ?? 'N/A'); ?>', 
                                                           '<?php echo addslashes($row['jenis_kegiatan'] ?? 'N/A'); ?>', 
                                                           '<?php echo addslashes($row['kelas'] ?? 'N/A'); ?>', 
                                                           '<?php echo addslashes($row['laporan'] ?? 'Belum ada laporan'); ?>', 
                                                           '<?php echo date('d/m/Y', strtotime($row['tanggal'])); ?>',
                                                           '<?php echo $row['id_status']; ?>',
                                                           '<?php echo addslashes($row['status_name'] ?? 'N/A'); ?>')">
                              <i class="fas fa-eye me-1"></i>Detail
                            </span>
                          </td>
                        </tr>
                      <?php
                        $no++;
                      }
                    } else {
                      ?>
                      <tr>
                        <td colspan="8" class="text-center py-4">
                          <p class="text-xs text-secondary mb-0">Belum ada data kegiatan</p>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <footer class="footer pt-3  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                made with <i class="fa fa-heart"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative Tim</a>
                for a better web.
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>

  <!-- Modified Detail Modal with Status Editing -->
  <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailModalLabel">Detail Kegiatan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label font-weight-bold">Nama Guru:</label>
                <p id="modalGuru" class="text-sm mb-0"></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label font-weight-bold">Tanggal:</label>
                <p id="modalTanggal" class="text-sm mb-0"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label font-weight-bold">Jenis Kegiatan:</label>
                <p id="modalJenis" class="text-sm mb-0"></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label font-weight-bold">Kelas:</label>
                <p id="modalKelas" class="text-sm mb-0"></p>
              </div>
            </div>
          </div>
          <!-- Status editing section -->
          <div class="mb-3">
            <label class="form-label font-weight-bold">Status:</label>
            <div class="d-flex align-items-center">
              <span id="currentStatus" class="badge me-3"></span>
              <select id="statusSelect" class="form-select form-select-sm" style="width: auto;">
                <?php
                // Get all status options
                $status_query = "SELECT id_status, status FROM status_kegiatan ORDER BY id_status";
                $status_result = pg_query($conn, $status_query);
                while ($status_row = pg_fetch_assoc($status_result)) {
                  echo "<option value='{$status_row['id_status']}'>{$status_row['status']}</option>";
                }
                ?>
              </select>
              <button id="updateStatusBtn" class="btn btn-sm btn-primary ms-2">Update</button>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-bold">Laporan Lengkap:</label>
            <div class="card">
              <div class="card-body">
                <p id="modalLaporan" class="text-sm mb-0" style="white-space: pre-wrap;"></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>

  <!-- Modified JavaScript with status editing functionality -->
  <script>
    let currentKegiatanId = null;

    function showDetailModal(id, guru, jenis, kelas, laporan, tanggal, statusId, statusName) {
      currentKegiatanId = id;
      
      document.getElementById('modalGuru').textContent = guru;
      document.getElementById('modalJenis').textContent = jenis;
      document.getElementById('modalKelas').textContent = kelas;
      document.getElementById('modalLaporan').textContent = laporan;
      document.getElementById('modalTanggal').textContent = tanggal;
      
      // Set current status
      const currentStatusElement = document.getElementById('currentStatus');
      currentStatusElement.textContent = statusName;
      
      // Set status badge color
      currentStatusElement.className = 'badge me-3 ';
      switch(statusId) {
        case '1': // Direncanakan
          currentStatusElement.className += 'bg-gradient-secondary';
          break;
        case '2': // Berlangsung
          currentStatusElement.className += 'bg-gradient-warning';
          break;
        case '3': // Selesai
          currentStatusElement.className += 'bg-gradient-success';
          break;
        case '4': // Dibatalkan
          currentStatusElement.className += 'bg-gradient-danger';
          break;
      }
      
      // Set dropdown to current status
      document.getElementById('statusSelect').value = statusId;

      var modal = new bootstrap.Modal(document.getElementById('detailModal'));
      modal.show();
    }

    // Status update functionality
    document.getElementById('updateStatusBtn').addEventListener('click', function() {
      const newStatusId = document.getElementById('statusSelect').value;
      
      if (!currentKegiatanId || !newStatusId) {
        alert('Error: Missing data');
        return;
      }
      
      // Show loading state
      this.disabled = true;
      this.textContent = 'Updating...';
      
      // Send AJAX request
      const formData = new FormData();
      formData.append('id_kegiatan', currentKegiatanId);
      formData.append('id_status', newStatusId);
      
      fetch('../../app/controllers/proses_update_status.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update current status display
          const currentStatusElement = document.getElementById('currentStatus');
          currentStatusElement.textContent = data.status_name;
          
          // Update badge color
          currentStatusElement.className = 'badge me-3 ';
          switch(newStatusId) {
            case '1':
              currentStatusElement.className += 'bg-gradient-secondary';
              break;
            case '2':
              currentStatusElement.className += 'bg-gradient-warning';
              break;
            case '3':
              currentStatusElement.className += 'bg-gradient-success';
              break;
            case '4':
              currentStatusElement.className += 'bg-gradient-danger';
              break;
          }
          
          alert('Status berhasil diupdate!');
          
          // Refresh page to update table
          setTimeout(() => {
            location.reload();
          }, 1000);
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengupdate status');
      })
      .finally(() => {
        // Reset button state
        this.disabled = false;
        this.textContent = 'Update';
      });
    });

    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>
