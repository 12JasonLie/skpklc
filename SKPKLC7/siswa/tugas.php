<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'siswa' role
check_login('siswa');

$id_kelas = $_SESSION['id_kelas'];
$id_siswa = $_SESSION['user_id'];
// Tugas sesuai kelas dan semua kelas
$filter_mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$where = [];
$where[] = "(t.id_kelas='$id_kelas' OR t.untuk_semua_kelas=1)";
if ($filter_mapel) $where[] = "t.mapel='".$conn->real_escape_string($filter_mapel)."'";
if ($filter_bulan) $where[] = "MONTH(t.deadline)='".sprintf('%02d',(int)$filter_bulan)."'";
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';
$tugas = $conn->query("SELECT t.*, k.nama_kelas, u.nama_lengkap as guru FROM tugas t LEFT JOIN kelas k ON t.id_kelas=k.id_kelas LEFT JOIN users u ON t.id_guru=u.id $where_sql ORDER BY t.deadline DESC");
// Status pengumpulan
$pengumpulan = [];
$res = $conn->query("SELECT id_tugas, status, file, nilai FROM tugas_pengumpulan WHERE id_siswa='$id_siswa'");
while($row = $res->fetch_assoc()) $pengumpulan[$row['id_tugas']] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tugas Siswa</title>
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
        <h2 class="section-title"><i class="fa-solid fa-list-check"></i> Tugas Kelas Anda</h2>
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
           <table class="table table-striped" border="1" cellpadding="6" style="border-collapse:collapse;">
            <thead>
                <tr><th>Judul</th><th>Guru</th><th>Mapel</th><th>Deadline</th><th>Tipe</th><th>File</th><th>Status</th><th>Nilai</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php while($t=$tugas->fetch_assoc()): $id_t=$t['id']; ?>
                <tr>
                    <td><?=htmlspecialchars($t['judul'])?></td>
                    <td><?=htmlspecialchars($t['guru'])?></td>
                    <td><?=htmlspecialchars($t['mapel'])?></td>
                    <td><?=htmlspecialchars($t['deadline'])?></td>
                    <td><?=htmlspecialchars($t['tipe_pengumpulan'])?></td>
                    <td><?php if($t['file']): ?><a href="../uploads/tugas/<?=htmlspecialchars($t['file'])?>" target="_blank"><i class="fas fa-file-download"></i> File</a><?php endif; ?></td>
                    <td>
                        <?php
                        if($t['tipe_pengumpulan']==='fisik') {
                            if(isset($pengumpulan[$id_t]) && $pengumpulan[$id_t]['status']=='sudah') {
                                echo '<span class="badge badge-success">Sudah (Dicentang Guru)</span>';
                            } else {
                                echo '<span class="badge badge-warning">Belum Dicentang Guru</span>';
                            }
                        } else {
                            if(isset($pengumpulan[$id_t])) {
                                echo $pengumpulan[$id_t]['status']=='sudah' ? '<span class="badge badge-success">Sudah</span>' : '<span class="badge badge-warning">Belum</span>';
                            } else {
                                echo '<span class="badge badge-warning">Belum</span>';
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        // Display score, if available and greater than 0, otherwise display '-'
                        $nilai = isset($pengumpulan[$id_t]['nilai']) && $pengumpulan[$id_t]['nilai'] !== null && $pengumpulan[$id_t]['nilai'] !== '' ? $pengumpulan[$id_t]['nilai'] : '-';
                        echo htmlspecialchars($nilai);
                        ?>
                    </td>
                    <td>
                        <?php if($t['tipe_pengumpulan']=='online' && (!isset($pengumpulan[$id_t]) || $pengumpulan[$id_t]['status']=='belum')): ?>
                        <form method="post" enctype="multipart/form-data" style="margin:0;" action="upload_tugas.php?id=<?=$id_t?>">
                            <input type="file" name="file" required>
                            <button class="btn" type="submit">Upload</button>
                        </form>
                        <?php elseif(isset($pengumpulan[$id_t]) && $pengumpulan[$id_t]['file']): ?>
                        <a href="../uploads/tugas/<?=htmlspecialchars($pengumpulan[$id_t]['file'])?>" target="_blank" class="btn btn-info btn-sm">Lihat File Upload</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>