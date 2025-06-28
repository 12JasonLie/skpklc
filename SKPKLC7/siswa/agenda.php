<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
check_login('siswa');

$id_kelas = $_SESSION['id_kelas'];
$filter_mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$where = [];
$where[] = "(a.id_kelas='$id_kelas' OR a.untuk_semua_kelas=1)";
if ($filter_mapel) $where[] = "a.mapel='".$conn->real_escape_string($filter_mapel)."'";
if ($filter_bulan) $where[] = "MONTH(a.tanggal_upload)='".sprintf('%02d',(int)$filter_bulan)."'";
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';
$agenda = $conn->query("SELECT a.*, k.nama_kelas, u.nama_lengkap as guru FROM agenda a LEFT JOIN kelas k ON a.id_kelas=k.id_kelas LEFT JOIN users u ON a.id_guru=u.id $where_sql ORDER BY a.tanggal_upload DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Agenda/Informasi Siswa - SKPKLC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .main {
            padding: 20px;
            max-width: 1100px;
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
        .filter-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .card-body { padding: 20px; }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 0;
        }
        .form-group { flex: 1; min-width: 160px; margin-bottom: 0; }
        .form-label { display: block; margin-bottom: 7px; font-weight: 500; color: #444; }
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
        .btn i { font-size: 0.9em; }
        .btn:hover { background-color: #2c5282; }
        .agenda-table-responsive { overflow-x: auto; }
        .agenda-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        .agenda-table th {
            background: var(--biru-muda);
            color: #fff;
            font-weight: 500;
            padding: 12px 15px;
        }
        .agenda-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .agenda-table tr:hover { background: #f8f9fa; }
        .agenda-table th, .agenda-table td { text-align: left; }
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
            .main { padding: 12px; }
            .agenda-table th, .agenda-table td { padding: 10px 8px; font-size: 0.95rem; }
            .form-row { flex-direction: column; }
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
    <h1 class="page-title"><i class="fas fa-calendar-alt"></i> Informasi & Agenda Kelas Anda</h1>
    <div class="filter-card">
        <div class="card-body">
            <form method="get" class="form-row" autocomplete="off">
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="mapel" class="form-control">
                        <option value="">Semua Mapel</option>
                        <option value="PAK-BP" <?=isset($_GET['mapel'])&&$_GET['mapel']=='PAK-BP'?'selected':''?>>PAK-BP</option>
                        <option value="PendPan" <?=isset($_GET['mapel'])&&$_GET['mapel']=='PendPan'?'selected':''?>>Pendidikan Pancasila</option>
                        <option value="Bahasa Indonesia" <?=isset($_GET['mapel'])&&$_GET['mapel']=='Bahasa Indonesia'?'selected':''?>>Bahasa Indonesia</option>
                        <option value="BIG-1" <?=isset($_GET['mapel'])&&$_GET['mapel']=='BIG-1'?'selected':''?>>(BIG-1:Teori)</option>
                        <option value="BIG-2" <?=isset($_GET['mapel'])&&$_GET['mapel']=='BIG-2'?'selected':''?>>(BIG-2:Praktik)</option>
                        <option value="SE" <?=isset($_GET['mapel'])&&$_GET['mapel']=='SE'?'selected':''?>>SE</option>
                        <option value="Matematika" <?=isset($_GET['mapel'])&&$_GET['mapel']=='Matematika'?'selected':''?>>Matematika</option>
                        <option value="Fisika/Fis" <?=isset($_GET['mapel'])&&$_GET['mapel']=='Fisika/Fis'?'selected':''?>>IPA (Fisika/Fis)</option>
                        <option value="Biokim" <?=isset($_GET['mapel'])&&$_GET['mapel']=='Biokim'?'selected':''?>>IPA (Biologi-Kimia/Biokim)</option>
                        <option value="Ekonomi-Sejarah" <?=isset($_GET['mapel'])&&$_GET['mapel']=='Ekonomi-Sejarah'?'selected':''?>>IPS (Ekonomi-Sejarah)</option>
                        <option value="Geografi-Sosiologi" <?=isset($_GET['mapel'])&&$_GET['mapel']=='Geografi-Sosiologi'?'selected':''?>>IPS(Geografi-Sosiologi)</option>
                        <option value="PJOK" <?=isset($_GET['mapel'])&&$_GET['mapel']=='PJOK'?'selected':''?>>PJOK</option>
                        <option value="Seni Rupa" <?=isset($_GET['mapel'])&&$_GET['mapel']=='Seni Rupa'?'selected':''?>>Seni Rupa</option>
                        <option value="Informatika" <?=isset($_GET['mapel'])&&$_GET['mapel']=='Informatika'?'selected':''?>>Informatika</option>
                        <option value="Bahasa Jawa" <?=isset($_GET['mapel'])&&$_GET['mapel']=='Bahasa Jawa'?'selected':''?>>Bahasa Jawa</option>
                        <option value="Bahasa Mandarin" <?=isset($_GET['mapel'])&&$_GET['mapel']=='Bahasa Mandarin'?'selected':''?>>Bahasa Mandarin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-control">
                        <option value="">Semua Bulan</option>
                        <option value="01" <?=isset($_GET['bulan'])&&$_GET['bulan']=='01'?'selected':''?>>Januari</option>
                        <option value="02" <?=isset($_GET['bulan'])&&$_GET['bulan']=='02'?'selected':''?>>Februari</option>
                        <option value="03" <?=isset($_GET['bulan'])&&$_GET['bulan']=='03'?'selected':''?>>Maret</option>
                        <option value="04" <?=isset($_GET['bulan'])&&$_GET['bulan']=='04'?'selected':''?>>April</option>
                        <option value="05" <?=isset($_GET['bulan'])&&$_GET['bulan']=='05'?'selected':''?>>Mei</option>
                        <option value="06" <?=isset($_GET['bulan'])&&$_GET['bulan']=='06'?'selected':''?>>Juni</option>
                        <option value="07" <?=isset($_GET['bulan'])&&$_GET['bulan']=='07'?'selected':''?>>Juli</option>
                        <option value="08" <?=isset($_GET['bulan'])&&$_GET['bulan']=='08'?'selected':''?>>Agustus</option>
                        <option value="09" <?=isset($_GET['bulan'])&&$_GET['bulan']=='09'?'selected':''?>>September</option>
                        <option value="10" <?=isset($_GET['bulan'])&&$_GET['bulan']=='10'?'selected':''?>>Oktober</option>
                        <option value="11" <?=isset($_GET['bulan'])&&$_GET['bulan']=='11'?'selected':''?>>November</option>
                        <option value="12" <?=isset($_GET['bulan'])&&$_GET['bulan']=='12'?'selected':''?>>Desember</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body agenda-table-responsive">
            <table class="agenda-table">
                <thead>
                <tr>
                    <th>Judul</th>
                    <th>Guru</th>
                    <th>Kelas</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Upload</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($agenda && $agenda->num_rows > 0): while($a=$agenda->fetch_assoc()): ?>
                <tr>
                    <td><?=htmlspecialchars($a['judul'])?></td>
                    <td><i class="fas fa-chalkboard-teacher"></i> <?=htmlspecialchars($a['guru'])?></td>
                    <td><?= $a['untuk_semua_kelas'] ? '<span title="Semua Kelas"><i class="fas fa-users"></i> Semua</span>' : htmlspecialchars($a['nama_kelas']) ?></td>
                    <td><i class="far fa-calendar-alt"></i> <?=htmlspecialchars($a['tanggal'])?></td>
                    <td><?=htmlspecialchars($a['deskripsi'])?></td>
                    <td><?=htmlspecialchars($a['tanggal_upload'])?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="6"><div class="empty-state"><i class="far fa-calendar-times"></i><h3>Tidak Ada Agenda</h3><p>Belum ada agenda atau informasi untuk kelas Anda.</p></div></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
</body>
</html>
