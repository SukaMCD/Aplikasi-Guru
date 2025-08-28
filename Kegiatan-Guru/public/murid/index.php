<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'murid') {
    header('Location: ../admin/index.php');
    exit();
}
include '../../config/koneksi.php';

$current_user_id = $_SESSION['user_id'];
$query_murid_id = "SELECT id_murid FROM murid WHERE id_user = $1";
$result_murid_id = pg_query_params($conn, $query_murid_id, array($current_user_id));
$current_murid_id = null;
if ($result_murid_id && pg_num_rows($result_murid_id) > 0) {
    $current_murid_id = pg_fetch_assoc($result_murid_id)['id_murid'];
}

// Get user info
$query_user = "SELECT username FROM users WHERE id_user = $1";
$result_user = pg_query_params($conn, $query_user, array($current_user_id));
$user_info = pg_fetch_assoc($result_user);

// Get statistics for all activities (not class-specific)
$stats = array(
    'today' => 0,
    'week' => 0,
    'month' => 0,
    'total' => 0
);

// Today's activities
$query_today = "SELECT COUNT(*) as count FROM kegiatan WHERE tanggal = CURRENT_DATE";
$result_today = pg_query($conn, $query_today);
$stats['today'] = pg_fetch_assoc($result_today)['count'];

// This week's activities
$query_week = "SELECT COUNT(*) as count FROM kegiatan WHERE tanggal >= date_trunc('week', CURRENT_DATE)";
$result_week = pg_query($conn, $query_week);
$stats['week'] = pg_fetch_assoc($result_week)['count'];

// This month's activities
$query_month = "SELECT COUNT(*) as count FROM kegiatan WHERE tanggal >= date_trunc('month', CURRENT_DATE)";
$result_month = pg_query($conn, $query_month);
$stats['month'] = pg_fetch_assoc($result_month)['count'];

// Total activities
$query_total = "SELECT COUNT(*) as count FROM kegiatan";
$result_total = pg_query($conn, $query_total);
$stats['total'] = pg_fetch_assoc($result_total)['count'];

// Get recent ongoing activities (max 5) - all activities, not class-specific
$recent_activities = array();
$query_recent = "
    SELECT 
        k.id_kegiatan,
        jk.nama_kegiatan as jenis_kegiatan,
        CONCAT(kl.tingkat, ' ', kl.jurusan) as kelas,
        k.tanggal,
        k.jam_mulai,
        k.jam_selesai,
        k.id_status,
        sk.status as status_name,
        g.nama_guru
    FROM kegiatan k
    LEFT JOIN jenis_kegiatan jk ON k.id_jenis_kegiatan = jk.id_jenis_kegiatan
    LEFT JOIN kelas kl ON k.id_kelas = kl.id_kelas
    LEFT JOIN status_kegiatan sk ON k.id_status = sk.id_status
    LEFT JOIN guru g ON k.id_guru = g.id_guru
    WHERE k.id_status = 2
    ORDER BY k.tanggal DESC, k.jam_mulai DESC
    LIMIT 5
";

$result_recent = pg_query($conn, $query_recent);
if ($result_recent) {
    while ($row = pg_fetch_assoc($result_recent)) {
        $recent_activities[] = $row;
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
    <title>Dashboard Murid - Portal Murid</title>
    
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
    <!-- Added custom CSS to fix compressed table columns -->
    <style>
        .table-responsive {
            min-height: 300px;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6 !important;
            vertical-align: middle;
            padding: 12px 15px;
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6 !important;
            font-weight: bold;
        }
        .table-bordered {
            border: 1px solid #dee2e6 !important;
        }
        /* Set specific column widths to match guru dashboard proportions */
        .table th:nth-child(1), .table td:nth-child(1) { width: 8%; text-align: center; } /* NO */
        .table th:nth-child(2), .table td:nth-child(2) { width: 25%; } /* KEGIATAN */
        .table th:nth-child(3), .table td:nth-child(3) { width: 20%; } /* KELAS */
        .table th:nth-child(4), .table td:nth-child(4) { width: 15%; } /* TANGGAL */
        .table th:nth-child(5), .table td:nth-child(5) { width: 17%; } /* WAKTU */
        .table th:nth-child(6), .table td:nth-child(6) { width: 15%; } /* STATUS */
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">
    <div class="min-height-300 bg-primary position-absolute w-100"></div>
    
    <!-- Sidebar -->
    <aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="#">
                <img src="../admin/assets/img/favicon.png" width="26px" height="26px" class="navbar-brand-img h-100" alt="main_logo">
                <span class="ms-1 font-weight-bold">Portal Murid</span>
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
                    <a class="nav-link" href="lihat_kegiatan.php">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Lihat Kegiatan</span>
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
                        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Dashboard</li>
                    </ol>
                    <h6 class="font-weight-bolder text-white mb-0">Dashboard Murid</h6>
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

        <div class="container-fluid py-4">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Kegiatan Hari Ini</p>
                                        <h5 class="font-weight-bolder">
                                            <?php echo $stats['today']; ?>
                                        </h5>
                                        <p class="mb-0">
                                            <span class="text-success text-sm font-weight-bolder">Semua Kegiatan</span>
                                        </p>
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
                                        <h5 class="font-weight-bolder">
                                            <?php echo $stats['week']; ?>
                                        </h5>
                                        <p class="mb-0">
                                            <span class="text-danger text-sm font-weight-bolder">7 Hari Terakhir</span>
                                        </p>
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
                                        <h5 class="font-weight-bolder">
                                            <?php echo $stats['month']; ?>
                                        </h5>
                                        <p class="mb-0">
                                            <span class="text-success text-sm font-weight-bolder">Semua Kegiatan</span>
                                        </p>
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
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Kegiatan</p>
                                        <h5 class="font-weight-bolder">
                                            <?php echo $stats['total']; ?>
                                        </h5>
                                        <p class="mb-0">
                                            <span class="text-warning text-sm font-weight-bolder">Semua Kegiatan</span>
                                        </p>
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
                                <h6>Kegiatan Berlangsung Semua Kelas</h6>
                                <div>
                                    <a href="lihat_kegiatan.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table table-bordered align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">NO</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">KEGIATAN</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">KELAS</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TANGGAL</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">WAKTU</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recent_activities)): ?>
                                            <?php foreach ($recent_activities as $index => $activity): ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <h6 class="mb-0 text-sm"><?php echo $index + 1; ?></h6>
                                                    </td>
                                                    <td class="text-center">
                                                        <h6 class="mb-0 text-sm"><?php echo $activity['jenis_kegiatan'] ?? 'N/A'; ?></h6>
                                                    </td>
                                                    <td class="text-center">
                                                        <h6 class="mb-0 text-sm"><?php echo $activity['kelas'] ?? 'N/A'; ?></h6>
                                                    </td>
                                                    <td class="text-center">
                                                        <h6 class="mb-0 text-sm"><?php echo date('d/m/Y', strtotime($activity['tanggal'])); ?></h6>
                                                    </td>
                                                    <td class="text-center">
                                                        <h6 class="mb-0 text-sm">
                                                            <?php
                                                            if (!empty($activity['jam_mulai']) && !empty($activity['jam_selesai'])) {
                                                                echo date('H:i', strtotime($activity['jam_mulai'])) . ' - ' . date('H:i', strtotime($activity['jam_selesai']));
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                            ?>
                                                        </h6>
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        <span class="badge badge-sm bg-gradient-warning"><?php echo $activity['status_name'] ?? 'N/A'; ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="text-center">
                                                        <i class="fas fa-inbox fa-3x text-secondary mb-3"></i>
                                                        <p class="text-sm text-secondary mb-0">Tidak ada kegiatan berlangsung saat ini</p>
                                                        <p class="text-xs text-muted">Kegiatan akan muncul ketika guru memulai aktivitas</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
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
                                Portal Murid - Kegiatan Guru
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
