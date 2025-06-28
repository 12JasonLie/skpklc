<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'siswa' role
check_login('siswa');
$id_siswa = $_SESSION['user_id'];

// Ambil daftar ekskul yang diikuti
$ekskul = $conn->query("SELECT e.* FROM ekskul e JOIN ekskul_siswa es ON e.id=es.id_ekskul WHERE es.id_siswa=$id_siswa ORDER BY e.nama");

// Ambil data absensi
$absensi = [];
while($e = $ekskul->fetch_assoc()) {
    $eid = $e['id'];
    $absen = $conn->query("SELECT tanggal, status FROM absensi_ekskul WHERE id_ekskul=$eid AND id_siswa=$id_siswa ORDER BY tanggal DESC");
    $absensi[$eid] = [
        'nama' => $e['nama'],
        'data' => $absen->fetch_all(MYSQLI_ASSOC)
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Absensi Ekskul - SKPKLC</title>
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
        
        .ekskul-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .ekskul-header {
            background: var(--biru-muda);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .ekskul-title {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .ekskul-title i {
            font-size: 1.1em;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            background-color: rgba(255,255,255,0.2);
        }
        
        .table-responsive {
            overflow-x: auto;
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
        
        .status-hadir {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-tidak {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .status-hadir i {
            color: #43a047;
        }
        
        .status-tidak i {
            color: #e53935;
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
        
        .empty-state h3 {
            margin: 0 0 10px;
            font-size: 1.3rem;
            color: #444;
        }
        
        .empty-state p {
            margin: 0;
            color: #777;
        }
        
        @media (max-width: 768px) {
            .main {
                padding: 15px;
            }
            
            .ekskul-header {
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
        <i class="fas fa-clipboard-list"></i> Riwayat Absensi Ekskul
    </h1>
    
    <?php if (empty($absensi)): ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-question"></i>
            <h3>Belum Terdaftar Ekskul</h3>
            <p>Anda belum terdaftar di ekskul manapun.</p>
        </div>
    <?php else: ?>
        <?php foreach($absensi as $eid => $ekskul): ?>
            <div class="ekskul-card">
                <div class="ekskul-header">
                    <h2 class="ekskul-title">
                        <i class="fas fa-futbol"></i> <?= htmlspecialchars($ekskul['nama']) ?>
                    </h2>
                    <span class="badge">
                        <i class="fas fa-calendar-alt"></i> 
                        <?= count($ekskul['data']) ?> Catatan Kehadiran
                    </span>
                </div>
                
                <?php if (empty($ekskul['data'])): ?>
                    <div class="empty-state" style="margin: 20px; border-radius: 4px;">
                        <i class="far fa-calendar-times"></i>
                        <h3>Belum Ada Riwayat</h3>
                        <p>Belum ada catatan kehadiran untuk ekskul ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($ekskul['data'] as $absen): 
                                    $statusClass = $absen['status'] === 'hadir' ? 'hadir' : 'tidak';
                                    $statusIcon = $absen['status'] === 'hadir' ? 'check-circle' : 'times-circle';
                                    $statusText = $absen['status'] === 'hadir' ? 'Hadir' : 'Tidak Hadir';
                                ?>
                                <tr>
                                    <td>
                                        <i class="far fa-calendar-alt" style="color: #666; margin-right: 8px;"></i>
                                        <?= date('d M Y', strtotime($absen['tanggal'])) ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $statusClass ?>">
                                            <i class="fas fa-<?= $statusIcon ?>"></i>
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>
