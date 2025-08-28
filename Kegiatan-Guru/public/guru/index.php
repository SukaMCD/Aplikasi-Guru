<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'guru') {
    header('Location: ../admin/index.php');
    exit();
}
include '../../config/koneksi.php';

$current_user_id = $_SESSION['user_id'];
$query_guru_id = "SELECT id_guru FROM guru WHERE id_user = $1";
$result_guru_id = pg_query_params($conn, $query_guru_id, array($current_user_id));
$current_guru_id = null;
if ($result_guru_id && pg_num_rows($result_guru_id) > 0) {
    $current_guru_id = pg_fetch_assoc($result_guru_id)['id_guru'];
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
                    <a class="nav-link active" href="index.php">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kegiatan.php">
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
                    <a class="nav-link" href="profile.php">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../app/controllers/proses_logout.php">
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
                        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Dashboard</li>
                    </ol>
                    <!-- Changed title to Dashboard Guru -->
                    <h6 class="font-weight-bolder text-white mb-0">Dashboard Guru</h6>
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
                            <a href="../../app/controllers/proses_logout.php" class="nav-link text-white font-weight-bold px-0">
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
            <!-- Statistics Cards -->
            <div class="row">
                <?php
                // Query untuk menghitung kegiatan guru hari ini
                $query_today = "SELECT COUNT(*) FROM kegiatan WHERE tanggal = CURRENT_DATE AND id_guru = $1";
                $result_today = pg_query_params($conn, $query_today, array($current_guru_id));
                $total_today = $result_today ? pg_fetch_row($result_today)[0] : 0;

                // Query untuk menghitung kegiatan guru minggu ini
                $query_week = "SELECT COUNT(*) FROM kegiatan WHERE tanggal >= CURRENT_DATE - INTERVAL '7 days' AND tanggal <= CURRENT_DATE AND id_guru = $1";
                $result_week = pg_query_params($conn, $query_week, array($current_guru_id));
                $total_week = $result_week ? pg_fetch_row($result_week)[0] : 0;

                // Query untuk menghitung kegiatan guru bulan ini
                $query_month = "SELECT COUNT(*) FROM kegiatan WHERE EXTRACT(MONTH FROM tanggal) = EXTRACT(MONTH FROM CURRENT_DATE) AND EXTRACT(YEAR FROM tanggal) = EXTRACT(YEAR FROM CURRENT_DATE) AND id_guru = $1";
                $result_month = pg_query_params($conn, $query_month, array($current_guru_id));
                $total_month = $result_month ? pg_fetch_row($result_month)[0] : 0;

                // Query untuk menghitung total kegiatan guru
                $query_total = "SELECT COUNT(*) FROM kegiatan WHERE id_guru = $1";
                $result_total = pg_query_params($conn, $query_total, array($current_guru_id));
                $total_kegiatan = $result_total ? pg_fetch_row($result_total)[0] : 0;
                ?>
                
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <!-- Changed to show guru's activities today -->
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Kegiatan Hari Ini</p>
                                        <h5 class="font-weight-bolder"><?php echo $total_today; ?></h5>
                                        <p class="mb-0 text-success text-sm font-weight-bolder">Kegiatan Saya</p>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
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
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Minggu Ini</p>
                                        <h5 class="font-weight-bolder"><?php echo $total_week; ?></h5>
                                        <p class="mb-0 text-info text-sm font-weight-bolder">7 Hari Terakhir</p>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                        <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
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
                                        <h5 class="font-weight-bolder"><?php echo $total_month; ?></h5>
                                        <p class="mb-0 text-warning text-sm font-weight-bolder">Kegiatan Bulanan</p>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                        <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
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
                                        <!-- Changed to show total guru's activities -->
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Kegiatan</p>
                                        <h5 class="font-weight-bolder"><?php echo $total_kegiatan; ?></h5>
                                        <p class="mb-0 text-secondary text-sm font-weight-bolder">Semua Kegiatan Saya</p>
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

            <!-- Recent Activities -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <!-- Changed title and link for guru -->
                                <h6>Kegiatan Terbaru Saya</h6>
                                <div>
                                    <a href="kegiatan.php" class="btn btn-sm btn-outline-primary me-2">Lihat Semua</a>
                                    <a href="tambah_kegiatan.php" class="btn btn-sm btn-primary">Tambah Kegiatan</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0 border">
                                    <thead>
                                        <tr class="border-bottom">
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">No</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Kegiatan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Kelas</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Tanggal</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Waktu</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query_recent = "
                                            SELECT 
                                                k.id_kegiatan,
                                                jk.nama_kegiatan as jenis_kegiatan,
                                                CONCAT(kl.tingkat, ' ', kl.jurusan) as kelas,
                                                k.tanggal,
                                                k.jam_mulai,
                                                k.jam_selesai,
                                                k.id_status,
                                                sk.status as status_name
                                            FROM kegiatan k
                                            LEFT JOIN jenis_kegiatan jk ON k.id_jenis_kegiatan = jk.id_jenis_kegiatan
                                            LEFT JOIN kelas kl ON k.id_kelas = kl.id_kelas
                                            LEFT JOIN status_kegiatan sk ON k.id_status = sk.id_status
                                            WHERE k.id_guru = $1
                                            ORDER BY k.tanggal DESC, k.jam_mulai DESC
                                            LIMIT 5
                                        ";
                                        $result_recent = pg_query_params($conn, $query_recent, array($current_guru_id));

                                        if ($result_recent && pg_num_rows($result_recent) > 0) {
                                            $no = 1;
                                            while ($row = pg_fetch_assoc($result_recent)) {
                                                // Set status badge color
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
                                                            <h6 class="mb-0 text-sm"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></h6>
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
                                                <td class="align-middle text-center text-sm">
                                                    <span class="badge badge-sm <?php echo $status_class; ?>"><?php echo $row['status_name'] ?? 'N/A'; ?></span>
                                                </td>
                                            </tr>
                                        <?php
                                                $no++;
                                            }
                                        } else {
                                        ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <!-- Updated empty state message for guru -->
                                                    <div class="text-center">
                                                        <i class="fas fa-inbox fa-3x text-secondary mb-3"></i>
                                                        <p class="text-sm text-secondary mb-0">Belum ada kegiatan</p>
                                                        <p class="text-xs text-muted">Silakan tambah kegiatan baru</p>
                                                    </div>
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

            <!-- Footer -->
            <footer class="footer pt-3">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6 mb-lg-0 mb-4">
                            <div class="copyright text-center text-sm text-muted text-lg-start">
                                © <script>document.write(new Date().getFullYear())</script>,
                                <!-- Updated footer text for guru portal -->
                                Portal Guru - Kegiatan Guru
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <!-- Core JS Files -->
    <script src="../admin/assets/js/core/popper.min.js"></script>
    <script src="../admin/assets/js/core/bootstrap.min.js"></script>
    <script src="../admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../admin/assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../admin/assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>
