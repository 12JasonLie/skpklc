<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'guru' role
check_login('guru');

$msg = '';
$kelas = $conn->query("SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");
$kelas_arr = [];
while ($row = $kelas->fetch_assoc()) $kelas_arr[$row['id_kelas']] = $row['nama_kelas'];

// Proses hapus tugas
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $idt = intval($_GET['hapus']);
    $q = $conn->query("SELECT file FROM tugas WHERE id=$idt AND id_guru=".$_SESSION['user_id']);
    if ($q && $row = $q->fetch_assoc()) {
        if ($row['file'] && file_exists("../uploads/tugas/".$row['file'])) {
            unlink("../uploads/tugas/".$row['file']);
        }
        // Hapus semua pengumpulan tugas terkait sebelum hapus tugas utama
        $conn->query("DELETE FROM tugas_pengumpulan WHERE id_tugas=$idt");
        $conn->query("DELETE FROM tugas WHERE id=$idt AND id_guru=".$_SESSION['user_id']);
        $msg = 'Tugas berhasil dihapus!';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $mapel = trim($_POST['mapel']);
    $id_kelas = $_POST['id_kelas'] ?: null;
    $untuk_semua = isset($_POST['untuk_semua']) ? 1 : 0;
    $tipe_pengumpulan = $_POST['tipe_pengumpulan'];
    $deadline = $_POST['deadline'];
    $file = '';
    if ($_FILES['file']['name']) {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $fname = 'tugas_'.date('Ymd_His').'_'.rand(100,999).'.'.$ext;
        $dest = '../uploads/tugas/'.$fname;
        if (!is_dir('../uploads/tugas')) mkdir('../uploads/tugas',0777,true);
        move_uploaded_file($_FILES['file']['tmp_name'], $dest);
        $file = $fname;
    }
    $stmt = $conn->prepare("INSERT INTO tugas (judul, deskripsi, file, tipe_pengumpulan, id_guru, id_kelas, mapel, deadline, untuk_semua_kelas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssisssi', $judul, $deskripsi, $file, $tipe_pengumpulan, $_SESSION['user_id'], $id_kelas, $mapel, $deadline, $untuk_semua);
    $stmt->execute();
    $msg = 'Tugas berhasil diupload!';
}
// --- FILTER ---
$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$filter_mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$where = ["t.id_guru=".$_SESSION['user_id']];
if ($filter_kelas) $where[] = "k.nama_kelas='".$conn->real_escape_string($filter_kelas)."'";
if ($filter_mapel) $where[] = "t.mapel='".$conn->real_escape_string($filter_mapel)."'";
if ($filter_bulan) $where[] = "MONTH(t.tanggal_upload)='".sprintf("%02d", (int)$filter_bulan)."'";
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';
$tugas = $conn->query("SELECT t.*, k.nama_kelas FROM tugas t LEFT JOIN kelas k ON t.id_kelas=k.id_kelas $where_sql ORDER BY t.tanggal_upload DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Tugas</title>
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
        <h2 class="section-title"><i class="fa-solid fa-upload"></i> Upload Tugas</h2>
    <?php if ($msg) echo '<div style="color:green;">'.$msg.'</div>'; ?>
    <?php
    // --- Pilihan kelas tetap ---
    $kelas_static = ['7A','7B','8A','8B','9A','9B'];
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
    $filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
    $filter_mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';
    $filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
    ?>
    <form method="post" enctype="multipart/form-data" class="upload-form">
        <input type="text" name="judul" placeholder="Judul Tugas" required><br>
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
        <select name="tipe_pengumpulan" required>
            <option value="fisik">Fisik</option>
            <option value="online">Online</option>
        </select> Tipe Pengumpulan<br>
        <input type="datetime-local" name="deadline" required> Deadline<br>
        <input type="file" name="file" accept=".pdf,.doc,.ppt,.pptx,.docx,.jpg,.png,.zip"><br>
        <button class="btn" type="submit" style="background:#2e86de;color:#fff;padding:10px 26px;border:none;border-radius:6px;font-size:1.08em;cursor:pointer;transition:background 0.2s;">Upload Tugas</button>
<style>
.btn:hover { background:#145a96!important; }
</style>
    </form>
    <h3>Daftar Tugas Anda</h3>
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
    <table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;width:100%;">
        <tr><th>Judul</th><th>Kelas</th><th>Mapel</th><th>Tipe</th><th>File</th><th>Deadline</th><th>Tanggal</th><th>Hapus</th><th>Pengumpulan</th></tr>
        <?php mysqli_data_seek($tugas, 0); while($t=$tugas->fetch_assoc()): ?>
        <tr>
            <td><?=htmlspecialchars($t['judul'])?></td>
            <td><?= $t['untuk_semua_kelas'] ? 'Semua' : htmlspecialchars($t['nama_kelas']) ?></td>
            <td><?=htmlspecialchars($t['mapel'])?></td>
            <td><?=htmlspecialchars($t['tipe_pengumpulan'])?></td>
            <td><?php if($t['file']): ?><a href="../uploads/tugas/<?=htmlspecialchars($t['file'])?>" target="_blank">File</a><?php endif; ?></td>
            <td><?=htmlspecialchars($t['deadline'])?></td>
            <td><?=htmlspecialchars($t['tanggal_upload'])?></td>
            <td><a href="?hapus=<?=$t['id']?>" onclick="return confirm('Hapus tugas ini?')" style="color:red;">Hapus</a></td>
            <td><a href="tugas_pengumpulan.php?id=<?=$t['id']?>" class="btn">Lihat Pengumpulan</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
