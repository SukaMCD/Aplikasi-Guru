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
    $murid_data = pg_fetch_assoc($result_murid_id);
    $current_murid_id = $murid_data['id_murid'];
}

// Filter parameters
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with filters - removed class filtering
$where_conditions = array();
$params = array();
$param_count = 0;


if (!empty($filter_status)) {
    $param_count++;
    $where_conditions[] = "k.id_status = $$param_count";
    $params[] = $filter_status;
}

if (!empty($filter_bulan)) {
    $param_count++;
    $where_conditions[] = "EXTRACT(MONTH FROM k.tanggal) = $$param_count";
    $params[] = $filter_bulan;
}

if (!empty($search)) {
    $param_count++;
    $where_conditions[] = "(jk.nama_kegiatan ILIKE $$param_count OR g.nama_guru ILIKE $$param_count)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $param_count++;
}

$where_clause = implode(' AND ', $where_conditions);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../admin/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Lihat Kegiatan - Portal Murid</title>
    
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
    <!-- Added custom CSS to fix table layout and prevent bottom cutoff -->
    <style>
        .table-responsive {
            min-height: 400px;
        }
        .main-content {
            padding-bottom: 100px !important;
        }
        .footer {
            margin-top: 50px;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6 !important;
            vertical-align: middle;
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6 !important;
        }
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
                    <a class="nav-link" href="index.php">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="lihat_kegiatan.php">
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
                        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Kegiatan</li>
                    </ol>
                    <h6 class="font-weight-bolder text-white mb-0">Kegiatan Kelas</h6>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <div class="input-group">
                            <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" placeholder="Cari kegiatan..." id="searchInput" value="<?php echo htmlspecialchars($search); ?>">
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
            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="1" <?php echo $filter_status == '1' ? 'selected' : ''; ?>>Direncanakan</option>
                                        <option value="2" <?php echo $filter_status == '2' ? 'selected' : ''; ?>>Berlangsung</option>
                                        <option value="3" <?php echo $filter_status == '3' ? 'selected' : ''; ?>>Selesai</option>
                                        <option value="4" <?php echo $filter_status == '4' ? 'selected' : ''; ?>>Dibatalkan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Bulan</label>
                                    <select name="bulan" class="form-select">
                                        <option value="">Semua Bulan</option>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo $filter_bulan == $i ? 'selected' : ''; ?>>
                                                <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pencarian</label>
                                    <input type="text" name="search" class="form-control" placeholder="Cari kegiatan atau guru..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activities Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>Daftar Kegiatan Kelas</h6>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table table-bordered align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jenis Kegiatan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kelas</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Guru</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Laporan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Waktu</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($current_murid_id !== null) {
                                            $query_kegiatan = "
                                                SELECT 
                                                    k.id_kegiatan,
                                                    jk.nama_kegiatan as jenis_kegiatan,
                                                    CONCAT(kl.tingkat, ' ', kl.jurusan) as kelas,
                                                    k.tanggal,
                                                    k.jam_mulai,
                                                    k.jam_selesai,
                                                    k.id_status,
                                                    sk.status as status_name,
                                                    k.laporan,
                                                    g.nama_guru
                                                FROM kegiatan k
                                                LEFT JOIN jenis_kegiatan jk ON k.id_jenis_kegiatan = jk.id_jenis_kegiatan
                                                LEFT JOIN kelas kl ON k.id_kelas = kl.id_kelas
                                                LEFT JOIN status_kegiatan sk ON k.id_status = sk.id_status
                                                LEFT JOIN guru g ON k.id_guru = g.id_guru
                                                " . (!empty($where_conditions) ? "WHERE " . implode(' AND ', $where_conditions) : "") . "
                                                ORDER BY k.tanggal DESC, k.jam_mulai DESC
                                            ";
                                            
                                            $result_kegiatan = pg_query_params($conn, $query_kegiatan, $params);
                                        } else {
                                            $result_kegiatan = false;
                                        }

                                        if ($result_kegiatan && pg_num_rows($result_kegiatan) > 0) {
                                            while ($row = pg_fetch_assoc($result_kegiatan)) {
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
                                            <tr>
                                                <td class="text-center">
                                                    <h6 class="mb-0 text-sm"><?php echo $row['jenis_kegiatan'] ?? 'N/A'; ?></h6>
                                                </td>
                                                <td class="text-center">
                                                    <h6 class="mb-0 text-sm"><?php echo $row['kelas'] ?? 'N/A'; ?></h6>
                                                </td>
                                                <td class="text-center">
                                                    <h6 class="mb-0 text-sm"><?php echo $row['nama_guru'] ?? 'N/A'; ?></h6>
                                                </td>
                                                <td class="text-center">
                                                    <h6 class="mb-0 text-sm"><?php echo !empty($row['laporan']) ? substr($row['laporan'], 0, 50) . (strlen($row['laporan']) > 50 ? '...' : '') : 'N/A'; ?></h6>
                                                </td>
                                                <td class="text-center">
                                                    <h6 class="mb-0 text-sm">
                                                        <?php
                                                        if (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) {
                                                            echo date('H:i', strtotime($row['jam_mulai'])) . ' - ' . date('H:i', strtotime($row['jam_selesai']));
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                        ?>
                                                    </h6>
                                                </td>
                                                <td class="text-center">
                                                    <h6 class="mb-0 text-sm"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></h6>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="badge badge-sm <?php echo $status_class; ?>"><?php echo $row['status_name'] ?? 'N/A'; ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <button class="btn btn-sm btn-outline-info" onclick="showDetail(<?php echo $row['id_kegiatan']; ?>)">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                            }
                                        } else {
                                        ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="text-center">
                                                        <i class="fas fa-inbox fa-3x text-secondary mb-3"></i>
                                                        <p class="text-sm text-secondary mb-0">Tidak ada kegiatan ditemukan</p>
                                                        <p class="text-xs text-muted">Coba ubah filter atau tunggu guru menambahkan kegiatan</p>
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
                                Portal Murid - Kegiatan Guru
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="../admin/assets/js/core/popper.min.js"></script>
    <script src="../admin/assets/js/core/bootstrap.min.js"></script>
    <script src="../admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../admin/assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../admin/assets/js/argon-dashboard.min.js?v=2.1.0"></script>

    <script>
        function showDetail(id) {
            // Fetch activity details via AJAX
            fetch(`../app/controllers/get_kegiatan_detail.php?id=${id}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detailContent').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('detailModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail kegiatan');
                });
        }

        // Auto-submit search on Enter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                window.location.href = `?search=${encodeURIComponent(this.value)}`;
            }
        });
    </script>
</body>

</html>
