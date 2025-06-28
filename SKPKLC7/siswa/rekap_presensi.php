<?php
require_once '../includes/session.php';
require_once '../includes/db.php';

// Check if user is logged in and has 'siswa' role
check_login('siswa');
$id_siswa = $_SESSION['user_id'];
$id_kelas = $_SESSION['id_kelas'];

// Ambil data presensi harian
$harian = $conn->query("SELECT tanggal, status, keterangan FROM absensi_harian WHERE id_siswa='$id_siswa' AND id_kelas='$id_kelas' ORDER BY tanggal DESC");
// Ambil data presensi ekskul
$ekskul = $conn->query("SELECT e.nama, a.tanggal, a.status FROM absensi_ekskul a JOIN ekskul e ON a.id_ekskul=e.id WHERE a.id_siswa='$id_siswa' ORDER BY a.tanggal DESC");


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi Siswa - SKPKLC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .main { padding: 20px; max-width: 1100px; margin: 0 auto; }
        .page-title { color: var(--biru-tua); margin-bottom: 20px; font-size: 1.8rem; font-weight: 600; display: flex; align-items: center; gap: 12px; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 25px; overflow: hidden; }
        .card-body { padding: 20px; }
        .table-responsive { overflow-x: auto; }
        .rekap-table { width: 100%; border-collapse: collapse; background: #fff; }
        .rekap-table th { background: var(--biru-muda); color: #fff; font-weight: 500; padding: 12px 15px; }
        .rekap-table td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        .rekap-table tr:hover { background: #f8f9fa; }
        .rekap-table th, .rekap-table td { text-align: left; }
        .badge-status { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 12px; font-size: 0.92rem; font-weight: 500; }
        .badge-hadir { background: #e8f5e9; color: #2e7d32; }
        .badge-izin { background: #fff3e0; color: #e65100; }
        .badge-sakit { background: #e3f2fd; color: #1565c0; }
        .badge-alpa { background: #ffebee; color: #c62828; }
        .empty-state { text-align: center; padding: 40px 20px; color: #666; background: #f9f9f9; border-radius: 8px; margin: 20px 0; }
        .empty-state i { font-size: 2.5rem; color: #ddd; margin-bottom: 15px; }
        .empty-state h3 { margin: 0 0 10px; font-size: 1.3rem; color: #444; }
        .empty-state p { margin: 0; color: #777; }
        @media (max-width: 768px) { .main { padding: 12px; } .rekap-table th, .rekap-table td { padding: 10px 8px; font-size: 0.95rem; } }
    </style>
</head>
<body>
<?php 
$current_page = basename($_SERVER['PHP_SELF']);
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>
<div class="main">
    <h1 class="page-title"><i class="fas fa-table-list"></i> Rekap Presensi Siswa</h1>
    <div class="card">
        <div class="card-body table-responsive">
            <h2 style="margin-top:0;margin-bottom:16px;font-size:1.2rem;color:var(--biru-tua);font-weight:600;"><i class="fas fa-calendar-check"></i> Presensi Harian</h2>
            <table class="rekap-table">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($harian && $harian->num_rows > 0): foreach($harian as $row): 
                    $status = strtolower($row['status']);
                    $statusText = ucfirst($status);
                    $statusIcon = [
                        'hadir' => 'check-circle',
                        'izin' => 'user-clock',
                        'sakit' => 'procedures',
                        'alpa' => 'times-circle'
                    ][$status] ?? 'question-circle';
                ?>
                <tr>
                    <td><i class="far fa-calendar-alt"></i> <?=date('d M Y', strtotime($row['tanggal']))?></td>
                    <td><span class="badge-status badge-<?= $status ?>"><i class="fas fa-<?= $statusIcon ?>"></i> <?= $statusText ?></span></td>
                    <td><?=htmlspecialchars($row['keterangan'])?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="3"><div class="empty-state"><i class="far fa-calendar-times"></i><h3>Tidak Ada Data</h3><p>Belum ada presensi harian.</p></div></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-body table-responsive">
            <h2 style="margin-top:0;margin-bottom:16px;font-size:1.2rem;color:var(--biru-tua);font-weight:600;"><i class="fas fa-users"></i> Presensi Ekskul</h2>
            <table class="rekap-table">
                <thead>
                <tr>
                    <th>Ekskul</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($ekskul && $ekskul->num_rows > 0): foreach($ekskul as $row): 
                    $status = strtolower($row['status']);
                    $statusText = ucfirst($status);
                    $statusIcon = [
                        'hadir' => 'check-circle',
                        'izin' => 'user-clock',
                        'sakit' => 'procedures',
                        'alpa' => 'times-circle'
                    ][$status] ?? 'question-circle';
                ?>
                <tr>
                    <td><i class="fas fa-medal"></i> <?=htmlspecialchars($row['nama'])?></td>
                    <td><i class="far fa-calendar-alt"></i> <?=date('d M Y', strtotime($row['tanggal']))?></td>
                    <td><span class="badge-status badge-<?= $status ?>"><i class="fas fa-<?= $statusIcon ?>"></i> <?= $statusText ?></span></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="3"><div class="empty-state"><i class="far fa-calendar-times"></i><h3>Tidak Ada Data</h3><p>Belum ada presensi ekskul.</p></div></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
</body>
</html>
