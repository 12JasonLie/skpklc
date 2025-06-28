<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'siswa' role
check_login('siswa');
$id_siswa = $_SESSION['user_id'];
$id_kelas = $_SESSION['id_kelas'];

// Ambil data presensi harian siswa dari absensi_harian (yang diinput guru)
$presensi = $conn->query("SELECT tanggal, status, keterangan, waktu_input, id_guru FROM absensi_harian WHERE id_siswa='$id_siswa' AND id_kelas='$id_kelas' ORDER BY tanggal DESC, waktu_input DESC");
// Ambil nama guru
$guru_nama = [];
$res = $conn->query("SELECT id, nama_lengkap FROM users WHERE role='guru'");
while($g = $res->fetch_assoc()) $guru_nama[$g['id']] = $g['nama_lengkap'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Presensi Harian - SKPKLC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .main {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .page-title {
            color: var(--biru-tua);
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-title i {
            color: var(--biru-muda);
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card-header {
            background: var(--biru-muda);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .card-title {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-title i {
            font-size: 1.1em;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
            background-color: rgba(255,255,255,0.2);
        }
        
        .table-responsive {
            overflow-x: auto;
            margin-bottom: 0;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 500;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid #eee;
        }
        
        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px 4px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-hadir { background-color: #e8f5e9; color: #2e7d32; }
        .status-izin { background-color: #fff3e0; color: #e65100; }
        .status-sakit { background-color: #e3f2fd; color: #1565c0; }
        .status-alpa { background-color: #ffebee; color: #c62828; }
        
        .status-hadir i { color: #43a047; }
        .status-izin i { color: #fb8c00; }
        .status-sakit i { color: #1e88e5; }
        .status-alpa i { color: #e53935; }
        
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
        
        .empty-state h3 {
            margin: 0 0 10px;
            font-size: 1.3rem;
            color: #444;
        }
        
        .empty-state p {
            margin: 0;
            color: #777;
        }
        
        .text-muted {
            color: #6c757d;
            font-size: 0.9em;
        }
        
        @media (max-width: 768px) {
            .main {
                padding: 15px;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .table th, 
            .table td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }
            
            .page-title {
                font-size: 1.5rem;
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
        <i class="fas fa-calendar-day"></i> Presensi Harian
    </h1>
    
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-clipboard-list"></i> Daftar Presensi
            </h2>
            <span class="badge">
                <i class="fas fa-calendar-alt"></i> 
                <?= $presensi ? $presensi->num_rows : '0' ?> Catatan Kehadiran
            </span>
        </div>
        
        <div class="table-responsive">
            <?php if ($presensi && $presensi->num_rows > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Guru</th>
                            <th>Waktu Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $presensi->fetch_assoc()): 
                            $status = strtolower($row['status']);
                            $statusText = ucfirst($status);
                            $statusIcon = [
                                'hadir' => 'check-circle',
                                'izin' => 'user-clock',
                                'sakit' => 'procedures',
                                'alpa' => 'times-circle'
                            ][$status] ?? 'question-circle';
                            
                            $guru = $guru_nama[$row['id_guru']] ?? 'Tidak Diketahui';
                            $waktuInput = date('H:i d/m/Y', strtotime($row['waktu_input']));
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <span><?= date('d M Y', strtotime($row['tanggal'])) ?></span>
                                    <small class="text-muted"><?= date('l', strtotime($row['tanggal'])) ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $status ?>">
                                    <i class="fas fa-<?= $statusIcon ?>" style="width: 16px; text-align: center;"></i>
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td><?= $row['keterangan'] ? htmlspecialchars($row['keterangan']) : '-' ?></td>
                            <td><?= htmlspecialchars($guru) ?></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span><?= date('H:i', strtotime($row['waktu_input'])) ?></span>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($row['waktu_input'])) ?></small>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="far fa-calendar-times"></i>
                    <h3>Belum Ada Data Presensi</h3>
                    <p>Anda belum memiliki catatan presensi harian.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>
