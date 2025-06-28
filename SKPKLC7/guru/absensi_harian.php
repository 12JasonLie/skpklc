<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'guru' role
check_login('guru');

$id_guru = $_SESSION['user_id'];
$msg = '';

// Ambil daftar kelas
$kelas = $conn->query("SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");

// Proses simpan absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tanggal'], $_POST['id_kelas'], $_POST['absensi'])) {
    $tanggal = $_POST['tanggal'];
    $id_kelas = $_POST['id_kelas'];
    $absensi = $_POST['absensi']; // [id_siswa => status]
    $keterangan = $_POST['keterangan']; // [id_siswa => ket]
    $stmt = $conn->prepare("INSERT INTO absensi_harian (id_siswa, id_kelas, tanggal, status, keterangan, id_guru) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status=VALUES(status), keterangan=VALUES(keterangan), id_guru=VALUES(id_guru), waktu_input=NOW()");
foreach ($absensi as $id_siswa => $status) {
    $ket = isset($keterangan[$id_siswa]) ? $keterangan[$id_siswa] : '';
    $stmt->bind_param('iisssi', $id_siswa, $id_kelas, $tanggal, $status, $ket, $id_guru);
    $stmt->execute();
}
    $msg = 'Absensi berhasil disimpan!';
}

// Ambil siswa jika kelas & tanggal dipilih
$siswa = [];
if (isset($_GET['id_kelas'], $_GET['tanggal'])) {
    $id_kelas = $_GET['id_kelas'];
    $tanggal = $_GET['tanggal'];
    $q = $conn->query("SELECT id, nama_lengkap FROM users WHERE role='siswa' AND id_kelas='".$conn->real_escape_string($id_kelas)."' ORDER BY nama_lengkap");
    while ($row = $q->fetch_assoc()) $siswa[] = $row;
    // Ambil absensi yang sudah ada
    $absen_map = [];
    $q2 = $conn->query("SELECT id_siswa, status, keterangan FROM absensi_harian WHERE id_kelas='$id_kelas' AND tanggal='$tanggal'");
    while ($r2 = $q2->fetch_assoc()) $absen_map[$r2['id_siswa']] = $r2;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Harian Siswa - SKPKLC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .main {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-title {
            color: var(--biru-tua);
            margin-bottom: 20px;
            font-size: 1.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-title i {
            color: var(--biru-muda);
        }
        
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 0.95rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .filter-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 0;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
            margin-bottom: 0;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--biru-muda);
            outline: none;
            box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background-color: var(--biru-tua);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            height: 42px;
            align-self: flex-end;
        }
        
        .btn i {
            font-size: 0.9em;
        }
        
        .btn:hover {
            background-color: #2c5282;
        }
        
        .table-responsive {
            overflow-x: auto;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 8px;
            background: white;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background-color: var(--biru-muda);
            color: white;
            font-weight: 500;
            text-align: left;
            padding: 12px 15px;
        }
        
        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .status-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 0.95rem;
            cursor: pointer;
            min-width: 120px;
            transition: all 0.3s;
        }
        
        .status-select:focus {
            border-color: var(--biru-muda);
            outline: none;
            box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
        }
        
        .status-hadir { background-color: #e8f5e9; color: #2e7d32; }
        .status-izin { background-color: #fff3e0; color: #e65100; }
        .status-sakit { background-color: #e3f2fd; color: #1565c0; }
        .status-alpa { background-color: #ffebee; color: #c62828; }
        
        .keterangan-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }
        
        .keterangan-input:focus {
            border-color: var(--biru-muda);
            outline: none;
            box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            background: #f9f9f9;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .empty-state i {
            font-size: 2.5rem;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .main {
                padding: 15px;
            }
            
            .form-group {
                min-width: 100%;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .table th, 
            .table td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }
            
            .status-select {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
<?php 
$current_page = basename($_SERVER['PHP_SELF']);
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main">
    <h1 class="page-title">
        <i class="fas fa-clipboard-check"></i> Absensi Harian Siswa
    </h1>
    
    <?php if($msg): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>
    
    <div class="filter-card">
        <div class="card-body">
            <form method="get" class="form-row">
                <div class="form-group">
                    <label class="form-label">Pilih Kelas</label>
                    <select name="id_kelas" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelas as $k): ?>
                        <option value="<?= $k['id_kelas'] ?>" <?= isset($_GET['id_kelas']) && $_GET['id_kelas'] == $k['id_kelas'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama_kelas']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" 
                           value="<?= isset($_GET['tanggal']) ? htmlspecialchars($_GET['tanggal']) : date('Y-m-d') ?>" 
                           required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if($siswa): ?>
        <form method="post">
            <input type="hidden" name="id_kelas" value="<?= htmlspecialchars($id_kelas) ?>">
            <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Siswa</th>
                            <th style="min-width: 150px;">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($siswa as $i => $s): 
                            $id = $s['id'];
                            $status = isset($absen_map[$id]) ? $absen_map[$id]['status'] : '';
                            $keterangan = isset($absen_map[$id]) ? $absen_map[$id]['keterangan'] : '';
                        ?>
                        <tr>
                            <td><?= ($i + 1) ?></td>
                            <td><?= htmlspecialchars($s['nama_lengkap']) ?></td>
                            <td>
                                <select name="absensi[<?= $id ?>]" class="status-select status-<?= strtolower($status) ?>" 
                                        onchange="this.className='status-select status-'+this.options[this.selectedIndex].value.toLowerCase()">
                                    <option value="Hadir" <?= $status === 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                    <option value="Izin" <?= $status === 'Izin' ? 'selected' : '' ?>>Izin</option>
                                    <option value="Sakit" <?= $status === 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                                    <option value="Alpa" <?= $status === 'Alpa' || empty($status) ? 'selected' : '' ?>>Alpa</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="keterangan[<?= $id ?>]" 
                                       class="keterangan-input" 
                                       value="<?= htmlspecialchars($keterangan) ?>" 
                                       placeholder="Keterangan (opsi)">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-right">
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Simpan Absensi
                </button>
            </div>
        </form>
    <?php elseif(isset($_GET['id_kelas'])): ?>
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <h3>Tidak Ada Siswa</h3>
            <p>Tidak ada siswa yang terdaftar di kelas ini.</p>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>Pilih Kelas dan Tanggal</h3>
            <p>Silakan pilih kelas dan tanggal untuk melihat daftar siswa.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>
