<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'guru' role
check_login('guru');

$msg = '';
$kelas = $conn->query("SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");
$kelas_arr = [];
while ($row = $kelas->fetch_assoc()) $kelas_arr[$row['id_kelas']] = $row['nama_kelas'];

// Proses hapus jadwal
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $idj = intval($_GET['hapus']);
    $q = $conn->query("SELECT file_pdf FROM jadwal WHERE id=$idj");
    if ($q && $row = $q->fetch_assoc()) {
        if ($row['file_pdf'] && file_exists("../uploads/jadwal/".$row['file_pdf'])) {
            unlink("../uploads/jadwal/".$row['file_pdf']);
        }
        $conn->query("DELETE FROM jadwal WHERE id=$idj");
        $msg = 'Jadwal berhasil dihapus!';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kelas = $_POST['id_kelas'] ?: null;
    $tipe = $_POST['tipe'];
    $file = '';
    if ($_FILES['file']['name']) {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $fname = 'jadwal_'.date('Ymd_His').'_'.rand(100,999).'.'.$ext;
        $dest = '../uploads/jadwal/'.$fname;
        if (!is_dir('../uploads/jadwal')) mkdir('../uploads/jadwal',0777,true);
        move_uploaded_file($_FILES['file']['tmp_name'], $dest);
        $file = $fname;
    }
    $stmt = $conn->prepare("INSERT INTO jadwal (id_kelas, file_pdf, tipe) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $id_kelas, $file, $tipe);
    $stmt->execute();
    $msg = 'Jadwal berhasil diupload!';
}
$jadwal = $conn->query("SELECT j.*, k.nama_kelas FROM jadwal j LEFT JOIN kelas k ON j.id_kelas=k.id_kelas WHERE 1 ORDER BY j.tanggal_upload DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Jadwal</title>
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
        <h2 class="section-title"><i class="fa-solid fa-calendar-plus"></i> Upload Jadwal</h2>
    <?php if ($msg) echo '<div style="color:green;">'.$msg.'</div>'; ?>
    <form method="post" enctype="multipart/form-data">
        <select name="id_kelas">
            <option value="">Pilih Kelas</option>
            <?php foreach($kelas_arr as $kid=>$knama) echo "<option value='$kid'>$knama</option>";?>
        </select>
        <select name="tipe" required>
            <option value="pelajaran">Pelajaran</option>
            <option value="SUM">SUM</option>
            <option value="Rem">Rem</option>
            <option value="Peltam">Peltam</option>
        </select> Tipe Jadwal<br>
        <input type="file" name="file" accept=".pdf" required><br>
        <button class="btn" type="submit">Upload</button>
    </form>
    <h3>Daftar Jadwal</h3>
    <table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;">
        <tr><th>Kelas</th><th>Tipe</th><th>File</th><th>Tanggal</th><th>Hapus</th></tr>
        <?php while($j=$jadwal->fetch_assoc()): ?>
        <tr>
            <td><?=htmlspecialchars($j['nama_kelas'])?></td>
            <td><?=htmlspecialchars($j['tipe'])?></td>
            <td><?php if($j['file_pdf']): ?><a href="../uploads/jadwal/<?=htmlspecialchars($j['file_pdf'])?>" target="_blank">File</a><?php endif; ?></td>
            <td><?=htmlspecialchars($j['tanggal_upload'])?></td>
            <td><a href="?hapus=<?=$j['id']?>" onclick="return confirm('Hapus jadwal ini?')" style="color:red;">Hapus</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
