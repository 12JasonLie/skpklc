<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'siswa' role
check_login('siswa');

$id_kelas = $_SESSION['id_kelas'];
$filter_mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$where = [];
$where[] = "(m.id_kelas='$id_kelas' OR m.untuk_semua_kelas=1)";
if ($filter_mapel) $where[] = "m.mapel='".$conn->real_escape_string($filter_mapel)."'";
if ($filter_bulan) $where[] = "MONTH(m.tanggal_upload)='".sprintf('%02d',(int)$filter_bulan)."'";
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';
$materi = $conn->query("SELECT m.*, k.nama_kelas, u.nama_lengkap as guru FROM materi m LEFT JOIN kelas k ON m.id_kelas=k.id_kelas LEFT JOIN users u ON m.id_guru=u.id $where_sql ORDER BY m.tanggal_upload DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Materi Siswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php
$current_page = basename($_SERVER['PHP_SELF']);
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>
<div class="main">
    <div class="card card-section">
        <h2 class="section-title"><i class="fa-solid fa-book"></i> Materi Kelas Anda</h2>
        <form method="get" class="filter-form">
            <select name="mapel" class="form-control">
                <option value="">Semua Mapel</option>
                <option value="PAK-BP" <?=isset($_GET['mapel'])&&$_GET['mapel']=='PAK-BP'?'selected':''?>>Pendidikan Agama Kristen dan Budi Pekerti (PAK-BP)</option>
                <option value="PendPan" <?=isset($_GET['mapel'])&&$_GET['mapel']=='PendPan'?'selected':''?>>Pendidikan Pancasila (PendPan)</option>
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
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        </form>
    </div>
   <div class="table-responsive">
    <table class="table-striped" border="1" cellpadding="6" style="border-collapse:collapse;">
        <thead>
            <tr><th>Judul</th><th>Deskripsi</th><th>Guru</th><th>Mapel</th><th>File</th><th>Tanggal</th></tr>
        </thead>
        <tbody>
            <?php while($m=$materi->fetch_assoc()): ?>
            <tr>
                <td><?=htmlspecialchars($m['judul'])?></td>
                <td><?=htmlspecialchars($m['deskripsi'])?></td>
                <td><?=htmlspecialchars($m['guru'])?></td>
                <td><?=htmlspecialchars($m['mapel'])?></td>
                <td><?php if($m['file']): ?><a href="../uploads/materi/<?=htmlspecialchars($m['file'])?>" target="_blank"><i class="fas fa-file-download"></i> File</a><?php endif; ?></td>
                <td><?=htmlspecialchars($m['tanggal_upload'])?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
</body>
</html>