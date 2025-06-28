<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'guru' role
check_login('guru');
$id_guru = $_SESSION['user_id'];

// Pilih ekskul yang diampu
$ekskul = $conn->query("SELECT * FROM ekskul WHERE id_guru=$id_guru ORDER BY nama");
$ekskul_id = isset($_GET['ekskul']) ? intval($_GET['ekskul']) : 0;

// Proses simpan absensi
$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['tanggal']) && is_numeric($_POST['id_ekskul'])) {
    $id_ekskul = intval($_POST['id_ekskul']);
    $tanggal = $_POST['tanggal'];
    if (isset($_POST['absen']) && is_array($_POST['absen'])) {
        foreach ($_POST['absen'] as $id_siswa => $status) {
            $cek = $conn->query("SELECT id FROM absensi_ekskul WHERE id_ekskul=$id_ekskul AND id_siswa=$id_siswa AND tanggal='$tanggal'");
            if ($cek && $cek->num_rows) {
                $conn->query("UPDATE absensi_ekskul SET status='$status' WHERE id_ekskul=$id_ekskul AND id_siswa=$id_siswa AND tanggal='$tanggal'");
            } else {
                $conn->query("INSERT INTO absensi_ekskul (id_ekskul, id_siswa, tanggal, status, id_guru) VALUES ($id_ekskul, $id_siswa, '$tanggal', '$status', $id_guru)");
            }
        }
        $msg = 'Absensi berhasil disimpan!';
    }
}

// Data siswa ekskul terpilih
$siswa = [];
if ($ekskul_id) {
    $q = $conn->query("SELECT u.id, u.nama_lengkap, u.username FROM ekskul_siswa es JOIN users u ON es.id_siswa=u.id WHERE es.id_ekskul=$ekskul_id ORDER BY u.nama_lengkap");
    while($row = $q->fetch_assoc()) $siswa[] = $row;
}

// Ambil daftar absensi terakhir (jika ada)
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$absen_map = [];
if ($ekskul_id) {
    $absq = $conn->query("SELECT * FROM absensi_ekskul WHERE id_ekskul=$ekskul_id AND tanggal='$tanggal'");
    while($a = $absq->fetch_assoc()) $absen_map[$a['id_siswa']] = $a['status'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Ekskul - SKPKLC</title>
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
        
        .form-group {
            margin-bottom: 20px;
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
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-col {
            flex: 1;
            min-width: 200px;
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
        
        .select-status {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .select-status:focus {
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
        }
        
        .btn:hover {
            background-color: #2c5282;
        }
        
        .btn i {
            font-size: 0.9em;
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
            color: #ccc;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .main {
                padding: 15px;
            }
            
            .form-col {
                min-width: 100%;
            }
            
            .table th, 
            .table td {
                padding: 10px 8px;
                font-size: 0.9rem;
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
    <h1 class="page-title"><i class="fas fa-clipboard-check"></i> Absensi Ekstrakurikuler</h1>
    
    <?php if ($msg): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="get" class="form-row">
                <div class="form-col">
                    <label class="form-label">Pilih Ekskul</label>
                    <select name="ekskul" class="form-control" onchange="this.form.submit()" required>
                        <option value="">-- Pilih Ekskul --</option>
                        <?php 
                        $ekskul->data_seek(0); // Reset result set pointer
                        while($e = $ekskul->fetch_assoc()): ?>
                            <option value="<?= $e['id'] ?>" <?= $ekskul_id == $e['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nama']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-col">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal) ?>" onchange="this.form.submit()">
                </div>
            </form>
        </div>
    </div>
    
    <?php if ($ekskul_id && $siswa): ?>
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="id_ekskul" value="<?= $ekskul_id ?>">
                    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Username</th>
                                    <th>Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($siswa as $sw): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($sw['nama_lengkap']) ?></td>
                                    <td>@<?= htmlspecialchars($sw['username']) ?></td>
                                    <td>
                                        <select name="absen[<?= $sw['id'] ?>]" class="select-status">
                                            <option value="hadir" <?= isset($absen_map[$sw['id']]) && $absen_map[$sw['id']] === 'hadir' ? 'selected' : '' ?>>
                                                Hadir
                                            </option>
                                            <option value="tidak" <?= isset($absen_map[$sw['id']]) && $absen_map[$sw['id']] === 'tidak' ? 'selected' : '' ?>>
                                                Tidak Hadir
                                            </option>
                                        </select>
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
            </div>
        </div>
    <?php elseif($ekskul_id): ?>
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <h3>Belum Ada Siswa</h3>
            <p>Belum ada siswa yang terdaftar di ekskul ini.</p>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>Pilih Ekskul</h3>
            <p>Silakan pilih ekskul untuk melihat daftar siswa.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>
