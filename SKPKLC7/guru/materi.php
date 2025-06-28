<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'guru' role
check_login('guru');

$msg = '';
// Ambil kelas
$kelas = $conn->query("SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");
$kelas_arr = [];
while ($row = $kelas->fetch_assoc()) $kelas_arr[$row['id_kelas']] = $row['nama_kelas'];

// Proses hapus materi
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $idm = intval($_GET['hapus']);
    $q = $conn->query("SELECT file FROM materi WHERE id=$idm AND id_guru=".$_SESSION['user_id']);
    if ($q && $row = $q->fetch_assoc()) {
        if ($row['file'] && file_exists("../uploads/materi/".$row['file'])) {
            unlink("../uploads/materi/".$row['file']);
        }
        $conn->query("DELETE FROM materi WHERE id=$idm AND id_guru=".$_SESSION['user_id']);
        $msg = 'Materi berhasil dihapus!';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $mapel = trim($_POST['mapel']);
    $id_kelas = $_POST['id_kelas'] ?: null;
    $untuk_semua = isset($_POST['untuk_semua']) ? 1 : 0;
    $file = '';
    if ($_FILES['file']['name']) {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $fname = 'materi_'.date('Ymd_His').'_'.rand(100,999).'.'.$ext;
        $dest = '../uploads/materi/'.$fname;
        if (!is_dir('../uploads/materi')) mkdir('../uploads/materi',0777,true);
        move_uploaded_file($_FILES['file']['tmp_name'], $dest);
        $file = $fname;
    }
    $stmt = $conn->prepare("INSERT INTO materi (judul, deskripsi, file, id_guru, id_kelas, mapel, untuk_semua_kelas) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssissi', $judul, $deskripsi, $file, $_SESSION['user_id'], $id_kelas, $mapel, $untuk_semua);
    $stmt->execute();
    $msg = 'Materi berhasil diupload!';
}
// --- FILTER ---
$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$filter_mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';

$mapel_list = [
    'PAK-BP' => 'Pendidikan Agama Kristen dan Budi Pekerti (PAK-BP)',
    'PendPan' => 'Pendidikan Pancasila (PendPan)',
    'Bahasa Indonesia' => 'Bahasa Indonesia',
    'BIG-1' => 'BIG-1: Teori',
    'BIG-2' => 'BIG-2: Praktik',
    'SE' => 'SE',
    'Matematika' => 'Matematika',
    'Fisika/Fis' => 'IPA (Fisika/Fis)',
    'Biokim' => 'IPA (Biologi-Kimia/Biokim)',
    'Ekonomi-Sejarah' => 'IPS (Ekonomi-Sejarah)',
    'Geografi-Sosiologi' => 'IPS (Geografi-Sosiologi)',
    'PJOK' => 'PJOK',
    'Seni Rupa' => 'Seni Rupa',
    'Informatika' => 'Informatika',
    'Bahasa Jawa' => 'Bahasa Jawa',
    'Bahasa Mandarin' => 'Bahasa Mandarin'
];
$bulan_list = [
    '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
    '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
];

$where = ["m.id_guru=".$_SESSION['user_id']];
if ($filter_kelas) $where[] = "k.nama_kelas='".$conn->real_escape_string($filter_kelas)."'";
if ($filter_mapel) $where[] = "m.mapel='".$conn->real_escape_string($filter_mapel)."'";
if ($filter_bulan) $where[] = "MONTH(m.tanggal_upload)='".sprintf("%02d", (int)$filter_bulan)."'";
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';
$materi = $conn->query("SELECT m.*, k.nama_kelas FROM materi m LEFT JOIN kelas k ON m.id_kelas=k.id_kelas $where_sql ORDER BY m.tanggal_upload DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Materi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .sidebar {
            overflow-y: auto;
            max-height: 100vh;
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
    <div class="card card-section">
        <h2 class="section-title"><i class="fa-solid fa-upload"></i> Upload Materi</h2>
        <h2 style="margin-top:0;margin-bottom:18px;"><i class="fa-solid fa-upload"></i> Upload Materi</h2>
    <?php if ($msg) echo '<div style="color:green;">'.$msg.'</div>'; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="judul" placeholder="Judul Materi" required><br>
        <textarea name="deskripsi" placeholder="Deskripsi"></textarea><br>
        <select name="mapel" required style="min-width:180px;">
    <option value="">Pilih Mata Pelajaran</option>
    <option value="PAK-BP" <?=isset($_POST['mapel'])&&$_POST['mapel']=='PAK-BP'?'selected':''?>>Pendidikan Agama Kristen dan Budi Pekerti (PAK-BP)</option>
    <option value="PendPan" <?=isset($_POST['mapel'])&&$_POST['mapel']=='PendPan'?'selected':''?>>Pendidikan Pancasila (PendPan)</option>
    <option value="Bahasa Indonesia" <?=isset($_POST['mapel'])&&$_POST['mapel']=='Bahasa Indonesia'?'selected':''?>>Bahasa Indonesia</option>
    <option value="BIG-1" <?=isset($_POST['mapel'])&&$_POST['mapel']=='BIG-1'?'selected':''?>>(BIG-1:Teori)</option>
    <option value="BIG-2" <?=isset($_POST['mapel'])&&$_POST['mapel']=='BIG-2'?'selected':''?>>(BIG-2:Praktik)</option>
    <option value="SE" <?=isset($_POST['mapel'])&&$_POST['mapel']=='SE'?'selected':''?>>SE</option>
    <option value="Matematika" <?=isset($_POST['mapel'])&&$_POST['mapel']=='Matematika'?'selected':''?>>Matematika</option>
    <option value="Fisika/Fis" <?=isset($_POST['mapel'])&&$_POST['mapel']=='Fisika/Fis'?'selected':''?>>IPA (Fisika/Fis)</option>
    <option value="Biokim" <?=isset($_POST['mapel'])&&$_POST['mapel']=='Biokim'?'selected':''?>>IPA (Biologi-Kimia/Biokim)</option>
    <option value="Ekonomi-Sejarah" <?=isset($_POST['mapel'])&&$_POST['mapel']=='Ekonomi-Sejarah'?'selected':''?>>IPS (Ekonomi-Sejarah)</option>
    <option value="Geografi-Sosiologi" <?=isset($_POST['mapel'])&&$_POST['mapel']=='Geografi-Sosiologi'?'selected':''?>>IPS(Geografi-Sosiologi)</option>
    <option value="PJOK" <?=isset($_POST['mapel'])&&$_POST['mapel']=='PJOK'?'selected':''?>>PJOK</option>
    <option value="Seni Rupa" <?=isset($_POST['mapel'])&&$_POST['mapel']=='Seni Rupa'?'selected':''?>>Seni Rupa</option>
    <option value="Informatika" <?=isset($_POST['mapel'])&&$_POST['mapel']=='Informatika'?'selected':''?>>Informatika</option>
    <option value="Bahasa Jawa" <?=isset($_POST['mapel'])&&$_POST['mapel']=='Bahasa Jawa'?'selected':''?>>Bahasa Jawa</option>
    <option value="Bahasa Mandarin" <?=isset($_POST['mapel'])&&$_POST['mapel']=='Bahasa Mandarin'?'selected':''?>>Bahasa Mandarin</option>
</select><br>
        <select name="id_kelas">
            <option value="">Pilih Kelas</option>
            <?php foreach($kelas_arr as $kid=>$knama) echo "<option value='$kid'>$knama</option>";?>
        </select>
        <label><input type="checkbox" name="untuk_semua"> Untuk Semua Kelas</label><br>
        <input type="file" name="file" accept=".pdf,.doc,.ppt,.pptx,.docx,.jpg,.png,.zip"><br>
        <button class="btn" type="submit">Upload</button>
    </form>
    <?php
// --- Pilihan kelas tetap ---
$kelas_static = ['7A','7B','8A','8B','9A','9B'];
?>
<form method="get" class="filter-form" style="margin-bottom:18px;display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
    <select name="kelas" class="form-control" style="min-width:110px;">
        <option value="">Semua Kelas</option>
        <?php foreach($kelas_static as $k): ?>
            <option value="<?=$k?>" <?=($filter_kelas==$k?'selected':'')?>><?=$k?></option>
        <?php endforeach; ?>
    </select>
    <select name="mapel" class="form-control" style="min-width:140px;">
        <option value="">Semua Mapel</option>
        <?php foreach($mapel_list as $kode=>$nama): ?>
            <option value="<?=$kode?>" <?=($filter_mapel==$kode?'selected':'')?>><?=$nama?></option>
        <?php endforeach; ?>
    </select>
    <select name="bulan" class="form-control" style="min-width:110px;">
        <option value="">Semua Bulan</option>
        <?php foreach($bulan_list as $kode=>$nama): ?>
            <option value="<?=$kode?>" <?=($filter_bulan==$kode?'selected':'')?>><?=$nama?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn" style="padding:6px 18px;">Filter</button>
</form>
<h3>Daftar Materi Anda</h3>
    <table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;">
        <tr><th>Judul</th><th>Kelas</th><th>Mapel</th><th>File</th><th>Tanggal</th><th>Hapus</th></tr>
        <?php while($m=$materi->fetch_assoc()): ?>
        <tr>
            <td><?=htmlspecialchars($m['judul'])?></td>
            <td><?= $m['untuk_semua_kelas'] ? 'Semua' : htmlspecialchars($m['nama_kelas']) ?></td>
            <td><?=htmlspecialchars($m['mapel'])?></td>
            <td><?php if($m['file']): ?><a href="../uploads/materi/<?=htmlspecialchars($m['file'])?>" target="_blank">File</a><?php endif; ?></td>
            <td><?=htmlspecialchars($m['tanggal_upload'])?></td>
            <td><a href="?hapus=<?=$m['id']?>" onclick="return confirm('Hapus materi ini?')" style="color:red;">Hapus</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
