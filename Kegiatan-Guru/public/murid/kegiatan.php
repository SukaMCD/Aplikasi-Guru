<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'murid') {
    header('Location: ../admin/index.php');
    exit();
}
include '../../config/koneksi.php';

// Debug: Check database connection status
echo "<script>console.log('[v0] Database connection status: " . (pg_connection_status($conn) === PGSQL_CONNECTION_OK ? 'OK' : 'ERROR') . "');</script>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../admin/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Kegiatan - Portal Murid</title>
    
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
                    <a class="nav-link active" href="kegiatan.php">
                        <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Kegiatan</span>
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
                    <h6 class="font-weight-bolder text-white mb-0">Daftar Kegiatan</h6>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        <div class="input-group">
                            <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" placeholder="Cari kegiatan..." id="searchInput">
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
            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Filter Tanggal</label>
                                    <input type="date" class="form-control" id="filterDate">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Filter Status</label>
                                    <select class="form-control" id="filterStatus">
                                        <option value="">Semua Status</option>
                                        <option value="1">Direncanakan</option>
                                        <option value="2">Berlangsung</option>
                                        <option value="3">Selesai</option>
                                        <option value="4">Dibatalkan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Filter Kelas</label>
                                    <select class="form-control" id="filterKelas">
                                        <option value="">Semua Kelas</option>
                                        <?php
                                        $query_kelas = "SELECT DISTINCT CONCAT(tingkat, ' ', jurusan) as kelas FROM kelas ORDER BY tingkat, jurusan";
                                        $result_kelas = pg_query($conn, $query_kelas);
                                        while ($kelas = pg_fetch_assoc($result_kelas)) {
                                            echo '<option value="' . $kelas['kelas'] . '">' . $kelas['kelas'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button class="btn btn-primary me-2" onclick="applyFilters()">Filter</button>
                                    <button class="btn btn-outline-secondary" onclick="clearFilters()">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activities Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <?php
                            // Query untuk menghitung total kegiatan
                            $query_total = "SELECT COUNT(*) as total FROM kegiatan";
                            $result_total = pg_query($conn, $query_total);
                            $total_kegiatan = pg_fetch_assoc($result_total)['total'];
                            ?>
                            <div class="d-flex justify-content-between">
                                <h6>Daftar Kegiatan (<?php echo $total_kegiatan; ?> total)</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0 border" id="kegiatanTable">
                                    <thead>
                                        <tr class="border-bottom">
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">No</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Guru</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Jenis Kegiatan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Kelas</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Laporan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Waktu</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Tanggal</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 border-end">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query untuk mengambil semua kegiatan
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
                                            ORDER BY k.tanggal DESC, k.jam_mulai ASC
                                        ";
                                        
                                        $result_kegiatan = pg_query($conn, $query_kegiatan);
                                        
                                        // Debug: Check for database errors
                                        if (!$result_kegiatan) {
                                            echo "<script>console.log('[v0] Database query error: " . pg_last_error($conn) . "');</script>";
                                            echo '<tr><td colspan="9" class="text-center py-4"><p class="text-xs text-danger mb-0">Error: ' . pg_last_error($conn) . '</p></td></tr>';
                                        } else {
                                            $row_count = pg_num_rows($result_kegiatan);
                                            echo "<script>console.log('[v0] Query executed successfully. Found " . $row_count . " rows');</script>";
                                            
                                            if ($row_count > 0) {
                                                $no = 1;
                                                while ($row = pg_fetch_assoc($result_kegiatan)) {
                                                    // Debug: Log each row data
                                                    echo "<script>console.log('[v0] Processing row " . $no . ": " . json_encode($row) . "');</script>";
                                                    
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
                                                        (strlen($row['laporan']) > 20 ? substr($row['laporan'], 0, 20) . '...' : $row['laporan']) :
                                                        'Belum ada laporan';
                                        ?>
                                            <tr class="border-bottom" 
                                                data-tanggal="<?php echo $row['tanggal']; ?>"
                                                data-status="<?php echo $row['id_status']; ?>"
                                                data-kelas="<?php echo $row['kelas']; ?>">
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
                                                    <span class="badge badge-sm <?php echo $detail_class; ?> cursor-pointer"
                                                        onclick="showDetailModal('<?php echo $row['id_kegiatan']; ?>',
                                                                                  '<?php echo addslashes($row['nama_guru'] ?? 'N/A'); ?>', 
                                                                                  '<?php echo addslashes($row['jenis_kegiatan'] ?? 'N/A'); ?>', 
                                                                                  '<?php echo addslashes($row['kelas'] ?? 'N/A'); ?>', 
                                                                                  '<?php echo addslashes($row['laporan'] ?? 'Belum ada laporan'); ?>', 
                                                                                  '<?php echo date('d/m/Y', strtotime($row['tanggal'])); ?>',
                                                                                  '<?php echo (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) ? date('H:i', strtotime($row['jam_mulai'])) . ' - ' . date('H:i', strtotime($row['jam_selesai'])) : 'N/A'; ?>',
                                                                                  '<?php echo $row['status_name'] ?? 'N/A'; ?>')">
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
                                                <td colspan="9" class="text-center py-4">
                                                    <div class="text-center">
                                                        <i class="fas fa-inbox fa-3x text-secondary mb-3"></i>
                                                        <p class="text-sm text-secondary mb-0">Belum ada data kegiatan</p>
                                                        <p class="text-xs text-muted">Silakan hubungi guru untuk menambahkan kegiatan</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                            }
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
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Guru:</strong> <span id="modalGuru"></span></p>
                            <p><strong>Jenis Kegiatan:</strong> <span id="modalJenis"></span></p>
                            <p><strong>Kelas:</strong> <span id="modalKelas"></span></p>
                            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tanggal:</strong> <span id="modalTanggal"></span></p>
                            <p><strong>Waktu:</strong> <span id="modalWaktu"></span></p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Laporan Kegiatan:</strong></p>
                            <div class="bg-light p-3 rounded">
                                <p id="modalLaporan" class="mb-0"></p>
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

    <!-- Core JS Files -->
    <script src="../admin/assets/js/core/popper.min.js"></script>
    <script src="../admin/assets/js/core/bootstrap.min.js"></script>
    <script src="../admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../admin/assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../admin/assets/js/argon-dashboard.min.js?v=2.1.0"></script>

    <script>
        // Function to show detail modal
        function showDetailModal(id, guru, jenis, kelas, laporan, tanggal, waktu, status) {
            document.getElementById('modalGuru').textContent = guru;
            document.getElementById('modalJenis').textContent = jenis;
            document.getElementById('modalKelas').textContent = kelas;
            document.getElementById('modalLaporan').textContent = laporan;
            document.getElementById('modalTanggal').textContent = tanggal;
            document.getElementById('modalWaktu').textContent = waktu;
            document.getElementById('modalStatus').textContent = status;
            
            var modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.querySelectorAll('#kegiatanTable tbody tr');
            
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                if (text.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Filter functions
        function applyFilters() {
            var dateFilter = document.getElementById('filterDate').value;
            var statusFilter = document.getElementById('filterStatus').value;
            var kelasFilter = document.getElementById('filterKelas').value;
            var rows = document.querySelectorAll('#kegiatanTable tbody tr');
            
            rows.forEach(function(row) {
                var showRow = true;
                
                if (dateFilter && row.dataset.tanggal !== dateFilter) {
                    showRow = false;
                }
                
                if (statusFilter && row.dataset.status !== statusFilter) {
                    showRow = false;
                }
                
                if (kelasFilter && row.dataset.kelas !== kelasFilter) {
                    showRow = false;
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        }

        function clearFilters() {
            document.getElementById('filterDate').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterKelas').value = '';
            document.getElementById('searchInput').value = '';
            
            var rows = document.querySelectorAll('#kegiatanTable tbody tr');
            rows.forEach(function(row) {
                row.style.display = '';
            });
        }
    </script>
</body>

</html>
