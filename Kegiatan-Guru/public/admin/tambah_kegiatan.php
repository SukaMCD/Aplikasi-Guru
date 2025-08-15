<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../murid/index.php');
    exit();
}

include '../../config/koneksi.php';

$guru_query = "SELECT id_guru, nama_guru FROM guru ORDER BY nama_guru";
$guru_result = pg_query($conn, $guru_query);

$tingkat_query = "SELECT DISTINCT tingkat FROM kelas ORDER BY tingkat";
$tingkat_result = pg_query($conn, $tingkat_query);

$jurusan_query = "SELECT DISTINCT jurusan FROM kelas ORDER BY jurusan";
$jurusan_result = pg_query($conn, $jurusan_query);

$kelas_query = "SELECT id_kelas, tingkat, jurusan FROM kelas ORDER BY tingkat, jurusan";
$kelas_result = pg_query($conn, $kelas_query);

$jenis_kegiatan_query = "SELECT id_jenis_kegiatan, nama_kegiatan FROM jenis_kegiatan ORDER BY nama_kegiatan";
$jenis_kegiatan_result = pg_query($conn, $jenis_kegiatan_query);

$message = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'success') {
        $message = '<div class="alert alert-success">Kegiatan berhasil ditambahkan!</div>';
    } elseif ($_GET['msg'] === 'error_insert') {
        $message = '<div class="alert alert-danger">Gagal menambahkan kegiatan karena masalah database!</div>';
    } elseif ($_GET['msg'] === 'error_empty') {
        $message = '<div class="alert alert-danger">Semua field harus diisi!</div>';
    } elseif ($_GET['msg'] === 'error_time') {
        $message = '<div class="alert alert-danger">Format waktu tidak valid!</div>';
    } elseif ($_GET['msg'] === 'error_time_order') {
        $message = '<div class="alert alert-danger">Jam selesai harus lebih besar dari jam mulai!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Tambah Kegiatan - Kegiatan Guru</title>
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
                                <h6>Tambah Kegiatan Baru</h6>
                                <a href="kegiatan.php" class="btn btn-sm btn-outline-primary">Kembali ke Kegiatan</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php echo $message; ?>

                            <form action="../../app/controllers/proses_tambah_kegiatan.php" method="POST" onsubmit="return validateForm()">
                                <div class="form-group">
                                    <label for="id_guru">Guru</label>
                                    <select class="form-control" id="id_guru" name="id_guru" required>
                                        <option value="">Pilih Guru</option>
                                        <?php while ($guru = pg_fetch_assoc($guru_result)): ?>
                                            <option value="<?php echo $guru['id_guru']; ?>"
                                                <?php echo (isset($_POST['id_guru']) && $_POST['id_guru'] == $guru['id_guru']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($guru['nama_guru']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_jenis_kegiatan">Jenis Kegiatan</label>
                                    <select class="form-control" id="id_jenis_kegiatan" name="id_jenis_kegiatan" required>
                                        <option value="">Pilih Jenis Kegiatan</option>
                                        <?php while ($jenis = pg_fetch_assoc($jenis_kegiatan_result)): ?>
                                            <option value="<?php echo $jenis['id_jenis_kegiatan']; ?>"
                                                <?php echo (isset($_POST['id_jenis_kegiatan']) && $_POST['id_jenis_kegiatan'] == $jenis['id_jenis_kegiatan']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($jenis['nama_kegiatan']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tingkat">Tingkat</label>
                                            <select class="form-control" id="tingkat" name="tingkat" required>
                                                <option value="">Pilih Tingkat</option>
                                                <?php while ($tingkat = pg_fetch_assoc($tingkat_result)): ?>
                                                    <option value="<?php echo $tingkat['tingkat']; ?>"
                                                        <?php echo (isset($_POST['tingkat']) && $_POST['tingkat'] == $tingkat['tingkat']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($tingkat['tingkat']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jurusan">Jurusan</label>
                                            <select class="form-control" id="jurusan" name="jurusan" required>
                                                <option value="">Pilih Jurusan</option>
                                                <?php while ($jurusan = pg_fetch_assoc($jurusan_result)): ?>
                                                    <option value="<?php echo $jurusan['jurusan']; ?>"
                                                        <?php echo (isset($_POST['jurusan']) && $_POST['jurusan'] == $jurusan['jurusan']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($jurusan['jurusan']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="id_kelas" name="id_kelas" required>

                                <div class="form-group">
                                    <label for="tanggal">Tanggal Kegiatan</label>
                                    <input type="date" name="tanggal" class="form-control" id="tanggal"
                                        min="<?php echo date('Y-m-d'); ?>"
                                        value="<?php echo isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : ''; ?>"
                                        required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jam_mulai">Jam Mulai</label>
                                            <input type="time" name="jam_mulai" class="form-control" id="jam_mulai"
                                                value="<?php echo isset($_POST['jam_mulai']) ? htmlspecialchars($_POST['jam_mulai']) : ''; ?>"
                                                required>
                                            <!-- Added time format hint -->
                                            <small class="form-text text-muted">Format: HH:MM (24 jam)</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jam_selesai">Jam Selesai</label>
                                            <input type="time" name="jam_selesai" class="form-control" id="jam_selesai"
                                                value="<?php echo isset($_POST['jam_selesai']) ? htmlspecialchars($_POST['jam_selesai']) : ''; ?>"
                                                required>
                                            <!-- Added time format hint -->
                                            <small class="form-text text-muted">Format: HH:MM (24 jam)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="laporan">Laporan Kegiatan</label>
                                    <textarea class="form-control" id="laporan" name="laporan" rows="4"
                                        placeholder="Masukkan laporan kegiatan..." required><?php echo isset($_POST['laporan']) ? htmlspecialchars($_POST['laporan']) : ''; ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Tambah Kegiatan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Store all kelas data
        const kelasData = [
            <?php
            pg_result_seek($kelas_result, 0);
            $kelas_array = [];
            while ($kelas = pg_fetch_assoc($kelas_result)) {
                $kelas_array[] = "{id: '" . $kelas['id_kelas'] . "', tingkat: '" . $kelas['tingkat'] . "', jurusan: '" . $kelas['jurusan'] . "'}";
            }
            echo implode(',', $kelas_array);
            ?>
        ];

        const tingkatSelect = document.getElementById('tingkat');
        const jurusanSelect = document.getElementById('jurusan');
        const idKelasField = document.getElementById('id_kelas');

        function populateJurusanDropdown() {
            const selectedTingkat = tingkatSelect.value;

            // Clear existing options
            jurusanSelect.innerHTML = '<option value="">Pilih Jurusan</option>';

            if (selectedTingkat) {
                // Find unique jurusans for the selected tingkat
                const availableJurusans = [...new Set(
                    kelasData
                    .filter(kelas => kelas.tingkat === selectedTingkat)
                    .map(kelas => kelas.jurusan)
                )];

                // Populate the jurusan dropdown
                availableJurusans.forEach(jurusan => {
                    const option = document.createElement('option');
                    option.value = jurusan;
                    option.textContent = jurusan;
                    jurusanSelect.appendChild(option);
                });
            }
        }

        function updateKelasId() {
            const tingkat = tingkatSelect.value;
            const jurusan = jurusanSelect.value;

            if (tingkat && jurusan) {
                const matchingKelas = kelasData.find(kelas =>
                    kelas.tingkat === tingkat && kelas.jurusan === jurusan
                );

                if (matchingKelas) {
                    idKelasField.value = matchingKelas.id;
                } else {
                    idKelasField.value = '';
                }
            } else {
                idKelasField.value = '';
            }
        }

        function validateForm() {
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamSelesai = document.getElementById('jam_selesai').value;
            
            if (jamMulai && jamSelesai) {
                const startTime = new Date('2000-01-01 ' + jamMulai);
                const endTime = new Date('2000-01-01 ' + jamSelesai);
                
                if (endTime <= startTime) {
                    alert('Jam selesai harus lebih besar dari jam mulai!');
                    return false;
                }
            }
            
            return true;
        }

        // Add event listeners
        tingkatSelect.addEventListener('change', populateJurusanDropdown);
        tingkatSelect.addEventListener('change', updateKelasId);
        jurusanSelect.addEventListener('change', updateKelasId);

        // Initial population of the jurusan dropdown based on any pre-selected tingkat
        populateJurusanDropdown();
    </script>

    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>
