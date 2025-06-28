<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'guru' role
check_login('guru');

// Ambil daftar kelas
$kelas = $conn->query("SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");

// Ambil filter
$id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$id_siswa = isset($_GET['id_siswa']) ? $_GET['id_siswa'] : '';

// Ambil siswa sesuai kelas
$siswa_list = [];
if ($id_kelas) {
    $q = $conn->query("SELECT id, nama_lengkap FROM users WHERE role='siswa' AND id_kelas='".$conn->real_escape_string($id_kelas)."' ORDER BY nama_lengkap");
    while ($row = $q->fetch_assoc()) $siswa_list[] = $row;
}

// Rekap absensi
$rekap = [];
if ($id_kelas && $bulan && $tahun) {
    $where = "WHERE a.id_kelas='".$conn->real_escape_string($id_kelas)."' AND MONTH(a.tanggal)='".sprintf('%02d',(int)$bulan)."' AND YEAR(a.tanggal)='$tahun'";
    if ($id_siswa) $where .= " AND a.id_siswa='".$conn->real_escape_string($id_siswa)."'";
    $sql = "SELECT a.id_siswa, u.nama_lengkap, a.status, COUNT(*) as jml FROM absensi_harian a JOIN users u ON a.id_siswa=u.id $where GROUP BY a.id_siswa, a.status";
    $q = $conn->query($sql);
    while ($row = $q->fetch_assoc()) {
        $rekap[$row['id_siswa']]['nama'] = $row['nama_lengkap'];
        $rekap[$row['id_siswa']][$row['status']] = $row['jml'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Harian - SKPKLC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .main { padding: 20px; max-width: 1100px; margin: 0 auto; }
        .page-title { color: var(--biru-tua); margin-bottom: 20px; font-size: 1.8rem; font-weight: 600; display: flex; align-items: center; gap: 12px; }
        .filter-card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 25px; overflow: hidden; }
        .card-body { padding: 20px; }
        .form-row { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 0; }
        .form-group { flex: 1; min-width: 160px; margin-bottom: 0; }
        .form-label { display: block; margin-bottom: 7px; font-weight: 500; color: #444; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; transition: border-color 0.3s; }
        .form-control:focus { border-color: var(--biru-muda); outline: none; box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2); }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background-color: var(--biru-tua); color: white; border: none; border-radius: 4px; font-size: 1rem; font-weight: 500; cursor: pointer; transition: background-color 0.3s; text-decoration: none; height: 42px; align-self: flex-end; }
        .btn i { font-size: 0.9em; }
        .btn:hover { background-color: #2c5282; }
        .absensi-table-responsive { overflow-x: auto; }
        .absensi-table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 0; }
        .absensi-table th { background: var(--biru-muda); color: #fff; font-weight: 500; padding: 12px 15px; }
        .absensi-table td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        .absensi-table tr:hover { background: #f8f9fa; }
        .absensi-table th, .absensi-table td { text-align: left; }
        .badge-status { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 12px; font-size: 0.92rem; font-weight: 500; }
        .badge-hadir { background: #e8f5e9; color: #2e7d32; }
        .badge-izin { background: #fff3e0; color: #e65100; }
        .badge-sakit { background: #e3f2fd; color: #1565c0; }
        .badge-alpa { background: #ffebee; color: #c62828; }
        .empty-state { text-align: center; padding: 40px 20px; color: #666; background: #f9f9f9; border-radius: 8px; margin: 20px 0; }
        .empty-state i { font-size: 2.5rem; color: #ddd; margin-bottom: 15px; }
        .empty-state h3 { margin: 0 0 10px; font-size: 1.3rem; color: #444; }
        .empty-state p { margin: 0; color: #777; }
        @media (max-width: 768px) { .main { padding: 12px; } .absensi-table th, .absensi-table td { padding: 10px 8px; font-size: 0.95rem; } .form-row { flex-direction: column; } }
        @media print { .filter-card, .bottom-navbar, .btn-cetak { display: none !important; } }
    </style>
</head>
<body>
<?php 
$current_page = basename($_SERVER['PHP_SELF']);
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>
<div class="main">
    <h1 class="page-title"><i class="fas fa-table-list"></i> Rekap Absensi Harian Siswa</h1>
    <div class="filter-card">
        <div class="card-body">
            <form method="get" class="form-row" autocomplete="off">
                <div class="form-group">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-control" onchange="this.form.submit()" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelas as $k): ?>
                        <option value="<?=$k['id_kelas']?>" <?=$id_kelas==$k['id_kelas']?'selected':''?>><?=$k['nama_kelas']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-control" onchange="this.form.submit()">
                        <?php for($b=1;$b<=12;$b++): ?>
                        <option value="<?=sprintf('%02d',$b)?>" <?=$bulan==sprintf('%02d',$b)?'selected':''?>><?=date('F',mktime(0,0,0,$b,1))?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-control" onchange="this.form.submit()">
                        <?php for($y=date('Y')-3;$y<=date('Y');$y++): ?>
                        <option value="<?=$y?>" <?=$tahun==$y?'selected':''?>><?=$y?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Siswa</label>
                    <select name="id_siswa" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Siswa</option>
                        <?php foreach($siswa_list as $s): ?>
                        <option value="<?=$s['id']?>" <?=$id_siswa==$s['id']?'selected':''?>><?=$s['nama_lengkap']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn"><i class="fas fa-search"></i> Tampilkan</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body absensi-table-responsive">
            <table class="absensi-table">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th><span class="badge-status badge-hadir"><i class="fas fa-check-circle"></i> Hadir</span></th>
                    <th><span class="badge-status badge-izin"><i class="fas fa-user-clock"></i> Izin</span></th>
                    <th><span class="badge-status badge-sakit"><i class="fas fa-procedures"></i> Sakit</span></th>
                    <th><span class="badge-status badge-alpa"><i class="fas fa-times-circle"></i> Alpa</span></th>
                </tr>
                </thead>
                <tbody>
                <?php if($rekap): $no=1; foreach($rekap as $id=>$r): ?>
                <tr>
                    <td><?=$no++?></td>
                    <td><?=htmlspecialchars($r['nama'])?></td>
            <td><?=isset($r['Hadir'])?$r['Hadir']:0?></td>
            <td><?=isset($r['Izin'])?$r['Izin']:0?></td>
            <td><?=isset($r['Sakit'])?$r['Sakit']:0?></td>
            <td><?=isset($r['Alpa'])?$r['Alpa']:0?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6">Tidak ada data absensi untuk filter ini.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
