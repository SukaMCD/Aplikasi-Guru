<?php
session_start(); // Pastikan session dimulai
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
  header('Location: ../murid/index.php'); // Redirect ke halaman login jika bukan admin
  exit();
}
include '../../config/koneksi.php'; // Pastikan koneksi database sudah benar

function updateStatusBasedOnDate($conn)
{
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
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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


      // Updated query to count planed activities based on status
      $query_direncanakan = "SELECT COUNT(*) as total FROM kegiatan WHERE id_status = 1"; // 1 = Direncanakan
      $result_direncanakan = pg_query($conn, $query_direncanakan);
      $direncanakan = pg_fetch_assoc($result_direncanakan)['total'];

      // Updated query to count ongoing activities based on status
      $query_berlangsung = "SELECT COUNT(*) as total FROM kegiatan WHERE id_status = 2"; // 2 = berlangsung
      $result_berlangsung = pg_query($conn, $query_berlangsung);
      $berlangsung = pg_fetch_assoc($result_berlangsung)['total'];

      // Updated query to count completed activities based on status
      $query_selesai = "SELECT COUNT(*) as total FROM kegiatan WHERE id_status = 3"; // 3 = Selesai
      $result_selesai = pg_query($conn, $query_selesai);
      $selesai = pg_fetch_assoc($result_selesai)['total'];
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
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Direncanakan</p>
                    <h5 class="font-weight-bolder"><?php echo $direncanakan; ?></h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-secondary shadow-secondary text-center rounded-circle">
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
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Berlangsung</p>
                    <h5 class="font-weight-bolder"><?php echo $berlangsung; ?></h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                    <i class="ni ni-building text-lg opacity-10" aria-hidden="true"></i>
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
                  <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
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
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Waktu</th>
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
                        k.id_guru,
                        k.id_jenis_kegiatan,
                        k.id_kelas,
                        g.nama_guru,
                        jk.nama_kegiatan as jenis_kegiatan,
                        CONCAT(kl.tingkat, ' ', kl.jurusan) as kelas,
                        k.tanggal,
                        k.laporan,
                        k.jam_mulai,
                        k.jam_selesai,
                        k.id_status,
                        sk.status as status_name
                      FROM kegiatan k
                      LEFT JOIN guru g ON k.id_guru = g.id_guru
                      LEFT JOIN jenis_kegiatan jk ON k.id_jenis_kegiatan = jk.id_jenis_kegiatan
                      LEFT JOIN kelas kl ON k.id_kelas = kl.id_kelas
                      LEFT JOIN status_kegiatan sk ON k.id_status = sk.id_status
                      ORDER BY k.tanggal ASC
                    ";
                      $result_kegiatan = pg_query($conn, $query_kegiatan);

                      if ($result_kegiatan && pg_num_rows($result_kegiatan) > 0) {
                        $no = 1;
                        while ($row = pg_fetch_assoc($result_kegiatan)) {
                          // Set status badge color based on status
                          $status_class = '';
                          switch ($row['id_status']) {
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
                            (strlen($row['laporan']) > 25 ? substr($row['laporan'], 0, 25) . '...' : $row['laporan']) :
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
                                  <h6 class="mb-0 text-sm">
                                    <?php
                                    if (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) {
                                      echo date('H:i', strtotime($row['jam_mulai'])) . ' - ' . date('H:i', strtotime($row['jam_selesai']));
                                    } else {
                                      echo 'N/A';
                                    }
                                    ?>
                                  </h6>
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
                                                              '<?php echo (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) ? date('H:i', strtotime($row['jam_mulai'])) . ' - ' . date('H:i', strtotime($row['jam_selesai'])) : 'N/A'; ?>',
                                                              '<?php echo $row['id_status']; ?>',
                                                              '<?php echo addslashes($row['status_name'] ?? 'N/A'); ?>',
                                                              '<?php echo $row['id_guru']; ?>',
                                                              '<?php echo $row['id_jenis_kegiatan']; ?>',
                                                              '<?php echo $row['id_kelas']; ?>')">
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

  <!-- Enhanced Detail Modal with Fixed Layout and Consistent Buttons -->
  <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content shadow-lg">
        <div class="modal-header bg-gradient-primary">
          <h5 class="modal-title text-white" id="detailModalLabel">
            <i class="fas fa-info-circle me-2"></i>Detail Kegiatan
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <!-- Improved layout with consistent spacing and button alignment -->
          <div class="row g-3">
            <!-- Left Column - Basic Info -->
            <div class="col-md-6">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-light py-2">
                  <h6 class="mb-0 text-dark">
                    <i class="fas fa-user me-2"></i>Detail Pengajar
                  </h6>
                </div>
                <div class="card-body p-3">
                  <div class="mb-3">
                    <label class="form-label text-sm mb-1">
                      <i class="fas fa-chalkboard-teacher me-1"></i>Nama Guru
                    </label>
                    <div class="d-flex gap-2">
                      <select id="modalGuruSelect" class="form-select form-select-sm flex-grow-1">
                        <?php
                        $guru_modal_query = "SELECT id_guru, nama_guru FROM guru ORDER BY nama_guru";
                        $guru_modal_result = pg_query($conn, $guru_modal_query);
                        while ($guru_modal = pg_fetch_assoc($guru_modal_result)) {
                          echo "<option value='{$guru_modal['id_guru']}'>{$guru_modal['nama_guru']}</option>";
                        }
                        ?>
                      </select>
                      <button class="btn btn-primary btn-sm" type="button" id="updateGuruBtn">
                        <i class="fas fa-save"></i>Simpan
                      </button>
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label text-sm mb-1">
                      <i class="fas fa-tasks me-1"></i>Jenis Kegiatan
                    </label>
                    <div class="d-flex gap-2">
                      <select id="modalJenisSelect" class="form-select form-select-sm flex-grow-1">
                        <?php
                        $jenis_modal_query = "SELECT id_jenis_kegiatan, nama_kegiatan FROM jenis_kegiatan ORDER BY nama_kegiatan";
                        $jenis_modal_result = pg_query($conn, $jenis_modal_query);
                        while ($jenis_modal = pg_fetch_assoc($jenis_modal_result)) {
                          echo "<option value='{$jenis_modal['id_jenis_kegiatan']}'>{$jenis_modal['nama_kegiatan']}</option>";
                        }
                        ?>
                      </select>
                      <button class="btn btn-primary btn-sm" type="button" id="updateJenisBtn">
                        <i class="fas fa-save"></i>Simpan
                      </button>
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label text-sm mb-1">
                      <i class="fas fa-school me-1"></i>Kelas
                    </label>
                    <div class="row g-2 mb-2">
                      <div class="col-6">
                        <label class="form-label text-xs text-muted">Kelas:</label>
                        <select id="modalTingkatSelect" class="form-select form-select-sm" placeholder="Tingkat">
                          <?php
                          $tingkat_modal_query = "SELECT DISTINCT tingkat FROM kelas ORDER BY tingkat";
                          $tingkat_modal_result = pg_query($conn, $tingkat_modal_query);
                          while ($tingkat_modal = pg_fetch_assoc($tingkat_modal_result)) {
                            echo "<option value='{$tingkat_modal['tingkat']}'>{$tingkat_modal['tingkat']}</option>";
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-6">
                        <label class="form-label text-xs text-muted">Jurusan:</label>
                        <select id="modalJurusanSelect" class="form-select form-select-sm">
                          <option value="">Pilih Jurusan</option>
                        </select>
                      </div>
                    </div>
                    <button class="btn btn-primary btn-sm w-100" type="button" id="updateKelasBtn">
                      <i class="fas fa-save me-1"></i>Simpan
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column - Editable Fields -->
            <div class="col-md-6">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-light py-2">
                  <h6 class="mb-0 text-dark">
                    <i class="fas fa-calendar-alt me-2"></i>Jadwal Kegiatan
                  </h6>
                </div>
                <div class="card-body p-3">
                  <!-- Tanggal field -->
                  <div class="mb-3">
                    <label class="form-label text-sm mb-1">
                      <i class="fas fa-calendar me-1"></i>Tanggal
                    </label>
                    <div class="d-flex gap-2">
                      <input type="date" id="modalTanggalInput" class="form-control form-control-sm flex-grow-1">
                      <button class="btn btn-primary btn-sm px-3" type="button" id="updateDateBtn">
                        <i class="fas fa-save me-1"></i>Update
                      </button>
                    </div>
                  </div>

                  <!-- Status Section -->
                  <div class="mb-3">
                    <label class="form-label text-sm mb-1">
                      <i class="fas fa-flag me-1"></i>Status
                    </label>
                    <div class="d-flex gap-2">
                      <select id="statusSelect" class="form-select form-select-sm flex-grow-1">
                        <?php
                        $status_query = "SELECT id_status, status FROM status_kegiatan ORDER BY id_status";
                        $status_result = pg_query($conn, $status_query);
                        while ($status_row = pg_fetch_assoc($status_result)) {
                          echo "<option value='{$status_row['id_status']}'>{$status_row['status']}</option>";
                        }
                        ?>
                      </select>
                      <button id="updateStatusBtn" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-save me-1"></i>Update
                      </button>
                    </div>
                  </div>

                  <!-- Waktu fields -->
                  <div class="mb-3">
                    <label class="form-label text-sm mb-1">
                      <i class="fas fa-clock me-1"></i>Waktu
                    </label>
                    <div class="row g-2 mb-2">
                      <div class="col-6">
                        <label class="form-label text-xs text-muted">Mulai:</label>
                        <input type="time" id="modalWaktuMulai" class="form-control form-control-sm">
                      </div>
                      <div class="col-6">
                        <label class="form-label text-xs text-muted">Selesai:</label>
                        <input type="time" id="modalWaktuSelesai" class="form-control form-control-sm">
                      </div>
                    </div>
                    <button class="btn btn-primary btn-sm w-100" type="button" id="updateTimeBtn">
                      <i class="fas fa-save me-1"></i>Update
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Full Width Report Section with better spacing -->
          <div class="row mt-3">
            <div class="col-12">
              <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-2">
                  <h6 class="mb-0 text-dark">
                    <i class="fas fa-file-alt me-2"></i>Laporan Lengkap
                  </h6>
                </div>
                <div class="card-body p-3">
                  <div class="mb-2">
                    <textarea id="modalLaporanTextarea" class="form-control" rows="4" 
                              placeholder="Masukkan laporan kegiatan..." style="resize: vertical;"></textarea>
                  </div>
                  <button class="btn btn-primary btn-sm px-3 w-100" type="button" id="updateLaporanBtn">
                    <i class="fas fa-save me-1"></i>Update Laporan
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-success btn-sm" id="updateAllBtn">
            <i class="fas fa-save me-1"></i>Update Semua
          </button>
          <button type="button" class="btn btn-secondary btn-sm" id="closeModalBtn">
            <i class="fas fa-times me-1"></i>Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>

  <!-- Enhanced JavaScript with date and time editing functionality -->
  <script>
    let currentKegiatanId = null;
    let currentIdGuru = null;
    let currentIdJenis = null;
    let currentIdKelas = null;
    let hasUnsavedChanges = false;
    let isClosing = false;
    
    // Function to track changes
    function trackChanges() {
      hasUnsavedChanges = true;
    }
    
    // Add change tracking to all form elements
    document.addEventListener('DOMContentLoaded', function() {
      const formElements = ['modalGuruSelect', 'modalJenisSelect', 'modalTingkatSelect', 
                          'modalJurusanSelect', 'modalLaporanTextarea', 'modalTanggalInput',
                          'modalWaktuMulai', 'modalWaktuSelesai', 'statusSelect'];
      
      formElements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
          element.addEventListener('change', trackChanges);
          if (element.tagName === 'TEXTAREA') {
            element.addEventListener('input', trackChanges);
          }
        }
      });
    });

    // Function to reset change tracking
    function resetChangeTracking() {
      hasUnsavedChanges = false;
    }

    // Function to show warning dialog
    async function showUnsavedChangesWarning() {
      if (!hasUnsavedChanges) return true;
      
      const result = await Swal.fire({
        title: 'Perubahan Belum Disimpan!',
        text: 'Anda memiliki perubahan yang belum disimpan. Apakah Anda yakin ingin menutup?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#5e72e4',
        cancelButtonColor: '#f5365c',
        confirmButtonText: 'Ya, Tutup',
        cancelButtonText: 'Batal',
        customClass: {
          popup: 'rounded-4',
          confirmButton: 'btn btn-sm rounded-3 px-4',
          cancelButton: 'btn btn-sm rounded-3 px-4 me-2'
        },
        buttonsStyling: true,
        width: '360px',
        padding: '1em',
        reverseButtons: true
      });

      if (result.isConfirmed) {
        hasUnsavedChanges = false;
      }
      return result.isConfirmed;
    }

    // Store all kelas data for modal
    const kelasData = [
      <?php
      $kelas_modal_data_query = "SELECT id_kelas, tingkat, jurusan FROM kelas ORDER BY tingkat, jurusan";
      $kelas_modal_data_result = pg_query($conn, $kelas_modal_data_query);
      $kelas_modal_data_array = [];
      while ($kelas_modal_data = pg_fetch_assoc($kelas_modal_data_result)) {
        $kelas_modal_data_array[] = "{id: '" . $kelas_modal_data['id_kelas'] . "', tingkat: '" . $kelas_modal_data['tingkat'] . "', jurusan: '" . $kelas_modal_data['jurusan'] . "'}";
      }
      echo implode(',', $kelas_modal_data_array);
      ?>
    ];

    function showDetailModal(id, guru, jenis, kelas, laporan, tanggal, waktu, statusId, statusName, idGuru, idJenis, idKelas) {
      currentKegiatanId = id;
      currentIdGuru = idGuru;
      currentIdJenis = idJenis;
      currentIdKelas = idKelas;

      // Reset unsaved changes flag
      hasUnsavedChanges = false;

      // Set dropdown values
      document.getElementById('modalGuruSelect').value = idGuru;
      document.getElementById('modalJenisSelect').value = idJenis;
      
      // Extract tingkat and jurusan from kelas string (e.g., "XI BROADCAST")
      const kelasParts = kelas.split(' ');
      if (kelasParts.length >= 2) {
        const tingkat = kelasParts[0];
        const jurusan = kelasParts.slice(1).join(' ');
        
        document.getElementById('modalTingkatSelect').value = tingkat;
        populateModalJurusan(tingkat, jurusan);
      }
      
      document.getElementById('modalLaporanTextarea').value = laporan;

      // Parse and format the date
      const dateParts = tanggal.split('/');
      const formattedDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
      document.getElementById('modalTanggalInput').value = formattedDate;

      // Set time values
      if (waktu && waktu !== 'N/A') {
        const timeRange = waktu.split(' - ');
        if (timeRange.length === 2) {
          document.getElementById('modalWaktuMulai').value = timeRange[0];
          document.getElementById('modalWaktuSelesai').value = timeRange[1];
        }
      } else {
        document.getElementById('modalWaktuMulai').value = '';
        document.getElementById('modalWaktuSelesai').value = '';
      }

      // Set dropdown to current status
      document.getElementById('statusSelect').value = statusId;

      // Get the modal element
      const modalElement = document.getElementById('detailModal');

      // Create or get the modal instance
      let modalInstance = bootstrap.Modal.getInstance(modalElement);
      if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalElement, {
          backdrop: 'static',  // Prevent closing when clicking outside
          keyboard: false      // Prevent closing with Esc key
        });
      }
      
      // Show the modal
      modalInstance.show();
    }

    // Function to populate jurusan dropdown in modal
    function populateModalJurusan(tingkat, selectedJurusan = '') {
      const modalJurusanSelect = document.getElementById('modalJurusanSelect');
      
      // Clear existing options
      modalJurusanSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
      
      if (tingkat) {
        // Find unique jurusans for the selected tingkat
        const availableJurusans = [...new Set(
          kelasData
          .filter(kelas => kelas.tingkat === tingkat)
          .map(kelas => kelas.jurusan)
        )];
        
        // Populate the jurusan dropdown
        availableJurusans.forEach(jurusan => {
          const option = document.createElement('option');
          option.value = jurusan;
          option.textContent = jurusan;
          if (jurusan === selectedJurusan) {
            option.selected = true;
          }
          modalJurusanSelect.appendChild(option);
        });
      }
    }
    
    // Function to get kelas ID from tingkat and jurusan
    function getModalKelasId() {
      const tingkat = document.getElementById('modalTingkatSelect').value;
      const jurusan = document.getElementById('modalJurusanSelect').value;
      
      if (tingkat && jurusan) {
        const matchingKelas = kelasData
          .filter(kelas => kelas.tingkat === tingkat && kelas.jurusan === jurusan);
        
        if (matchingKelas.length > 0) {
          return matchingKelas[0].id;
        }
      }
      return null;
    }

    document.getElementById('updateDateBtn').addEventListener('click', function() {
      const newDate = document.getElementById('modalTanggalInput').value;

      if (!currentKegiatanId || !newDate) {
        alert('Error: Missing data');
        return;
      }

      updateField('tanggal', newDate, this, 'Tanggal');
    });

    document.getElementById('updateTimeBtn').addEventListener('click', function() {
      const waktuMulai = document.getElementById('modalWaktuMulai').value;
      const waktuSelesai = document.getElementById('modalWaktuSelesai').value;

      if (!currentKegiatanId || !waktuMulai || !waktuSelesai) {
        alert('Error: Mohon isi waktu mulai dan selesai');
        return;
      }

      const timeData = {
        jam_mulai: waktuMulai,
        jam_selesai: waktuSelesai
      };

      updateField('waktu', JSON.stringify(timeData), this, 'Waktu');
    });

    // Status update functionality (enhanced)
    // Event listeners for updating guru, jenis, and kelas
    document.getElementById('updateGuruBtn').addEventListener('click', function() {
      const newGuruId = document.getElementById('modalGuruSelect').value;

      if (!currentKegiatanId || !newGuruId) {
        alert('Error: Missing data');
        return;
      }

      updateField('guru', newGuruId, this, 'Guru');
    });

    document.getElementById('updateJenisBtn').addEventListener('click', function() {
      const newJenisId = document.getElementById('modalJenisSelect').value;

      if (!currentKegiatanId || !newJenisId) {
        alert('Error: Missing data');
        return;
      }

      updateField('jenis', newJenisId, this, 'Jenis Kegiatan');
    });

    document.getElementById('updateKelasBtn').addEventListener('click', function() {
      const newKelasId = getModalKelasId();

      if (!currentKegiatanId || !newKelasId) {
        alert('Error: Mohon pilih tingkat dan jurusan terlebih dahulu!');
        return;
      }

      updateField('kelas', newKelasId, this, 'Kelas');
    });

    document.getElementById('updateLaporanBtn').addEventListener('click', function() {
      const newLaporan = document.getElementById('modalLaporanTextarea').value;

      if (!currentKegiatanId || !newLaporan.trim()) {
        alert('Error: Mohon isi laporan terlebih dahulu!');
        return;
      }

      updateField('laporan', newLaporan, this, 'Laporan');
    });

    document.getElementById('updateStatusBtn').addEventListener('click', function() {
      const newStatusId = document.getElementById('statusSelect').value;

      if (!currentKegiatanId || !newStatusId) {
        alert('Error: Missing data');
        return;
      }

      updateField('status', newStatusId, this, 'Status');
    });

    // Add event listener for tingkat change in modal
    document.getElementById('modalTingkatSelect').addEventListener('change', function() {
      const selectedTingkat = this.value;
      populateModalJurusan(selectedTingkat);
    });

    // Function to update all fields
    async function updateAllFields() {
      const updates = [
        { field: 'guru', value: document.getElementById('modalGuruSelect').value },
        { field: 'jenis', value: document.getElementById('modalJenisSelect').value },
        { field: 'kelas', value: getModalKelasId() },
        { field: 'tanggal', value: document.getElementById('modalTanggalInput').value },
        { field: 'waktu', value: JSON.stringify({
          jam_mulai: document.getElementById('modalWaktuMulai').value,
          jam_selesai: document.getElementById('modalWaktuSelesai').value
        })},
        { field: 'status', value: document.getElementById('statusSelect').value },
        { field: 'laporan', value: document.getElementById('modalLaporanTextarea').value }
      ];

      let success = true;
      for (const update of updates) {
        try {
          const formData = new FormData();
          formData.append('id_kegiatan', currentKegiatanId);
          formData.append('field_type', update.field);
          formData.append('value', update.value);

          const response = await fetch('../../app/controllers/proses_update_kegiatan.php', {
            method: 'POST',
            body: formData
          });
          const data = await response.json();
          if (!data.success) {
            success = false;
            showNotification(data.message, 'error');
            break;
          }
        } catch (error) {
          success = false;
          showNotification('Terjadi kesalahan saat mengupdate data', 'error');
          break;
        }
      }

      if (success) {
        showNotification('Semua data berhasil diupdate', 'success');
        hasUnsavedChanges = false;
        setTimeout(() => {
          const modal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
          modal.hide();
          location.reload();
        }, 1000);
      }
    }

    // Add event listener for Update All button
    document.getElementById('updateAllBtn').addEventListener('click', updateAllFields);

    // Add event listener for close modal button
    document.getElementById('closeModalBtn').addEventListener('click', async function() {
      if (!hasUnsavedChanges || await showUnsavedChangesWarning()) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
        if (modal) {
          modal.hide();
          location.reload();
        }
      }
    });

    // Add event listener for modal hidden event (when clicking outside or pressing ESC)
    document.getElementById('detailModal').addEventListener('hide.bs.modal', async function(e) {
      if (hasUnsavedChanges) {
        e.preventDefault(); // Prevent modal from closing
        if (await showUnsavedChangesWarning()) {
          hasUnsavedChanges = false; // Reset the flag
          const modal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
          if (modal) {
            modal.hide();
            location.reload();
          }
        }
      }
    });

    function updateField(fieldType, value, buttonElement, fieldName) {
      // Show loading state
      const originalText = buttonElement.innerHTML;
      buttonElement.disabled = true;
      buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';

      // Send AJAX request
      const formData = new FormData();
      formData.append('id_kegiatan', currentKegiatanId);
      formData.append('field_type', fieldType);
      formData.append('value', value);

      fetch('../../app/controllers/proses_update_kegiatan.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            resetChangeTracking(); // Reset change tracking after successful update
            if (fieldType === 'status') {
              const currentStatusElement = document.getElementById('currentStatus');
              currentStatusElement.textContent = data.status_name;

              // Update badge color
              currentStatusElement.className = 'badge fs-6 ';
              switch (value) {
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
            }

            updateTableRow(currentKegiatanId, fieldType, value, data);

            // Show success message
            showNotification(`${fieldName} berhasil diupdate!`, 'success');

          } else {
            showNotification('Error: ' + data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification(`Terjadi kesalahan saat mengupdate ${fieldName.toLowerCase()}`, 'error');
        })
        .finally(() => {
          // Reset button state
          buttonElement.disabled = false;
          buttonElement.innerHTML = originalText;
        });
    }

    function updateTableRow(kegiatanId, fieldType, value, responseData) {
      // Find the table row for this kegiatan
      const tableRows = document.querySelectorAll('tbody tr');
      
      tableRows.forEach(row => {
        const detailButton = row.querySelector('[onclick*="' + kegiatanId + '"]');
        if (detailButton) {
          if (fieldType === 'tanggal') {
            // Update date column (index 6)
            const dateCell = row.children[6];
            if (dateCell) {
              const dateObj = new Date(value);
              const formattedDate = dateObj.toLocaleDateString('id-ID');
              dateCell.querySelector('h6').textContent = formattedDate;
            }
          } else if (fieldType === 'waktu') {
            // Update time column (index 5)
            const timeCell = row.children[5];
            if (timeCell && responseData.formatted_time) {
              timeCell.querySelector('h6').textContent = responseData.formatted_time;
            }
          } else if (fieldType === 'status') {
            // Update status column (index 7)
            const statusCell = row.children[7];
            if (statusCell) {
              const statusBadge = statusCell.querySelector('.badge');
              statusBadge.textContent = responseData.status_name;
              
              // Update badge color
              statusBadge.className = 'badge badge-sm ';
              switch (value) {
                case '1':
                  statusBadge.className += 'bg-gradient-secondary';
                  break;
                case '2':
                  statusBadge.className += 'bg-gradient-warning';
                  break;
                case '3':
                  statusBadge.className += 'bg-gradient-success';
                  break;
                case '4':
                  statusBadge.className += 'bg-gradient-danger';
                  break;
              }
            }
          }
          
          updateDetailButtonOnClick(detailButton, fieldType, value, responseData);
        }
      });
    }

    function updateDetailButtonOnClick(button, fieldType, value, responseData) {
      const currentOnClick = button.getAttribute('onclick');
      
      if (fieldType === 'tanggal') {
        const dateObj = new Date(value);
        const formattedDate = dateObj.toLocaleDateString('id-ID');
        const newOnClick = currentOnClick.replace(/'\d{2}\/\d{2}\/\d{4}'/, "'" + formattedDate + "'");
        button.setAttribute('onclick', newOnClick);
      } else if (fieldType === 'waktu' && responseData.formatted_time) {
        const newOnClick = currentOnClick.replace(/'[^']*\s-\s[^']*'/, "'" + responseData.formatted_time + "'");
        button.setAttribute('onclick', newOnClick);
      } else if (fieldType === 'status') {
        // Update status ID and name in onclick
        const newOnClick = currentOnClick.replace(/'(\d+)',\s*'([^']*)'(?=\))/g, "'" + value + "', '" + responseData.status_name + "'");
        button.setAttribute('onclick', newOnClick);
      }
    }

    function showNotification(message, type) {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: {
          popup: 'rounded-3 shadow-sm',
          title: 'text-sm'
        },
        iconColor: type === 'success' ? '#2dce89' : '#f5365c',
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      });

      Toast.fire({
        icon: type,
        title: message,
        padding: '0.5em 1em'
      });
    }
  </script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>
