<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'guru' role
check_login('guru');

$id_tugas = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id_tugas) die('Tugas tidak ditemukan.');

// Ambil info tugas
$q_tugas = $conn->query("SELECT * FROM tugas WHERE id=$id_tugas AND id_guru=".$_SESSION['user_id']);
$tugas = $q_tugas ? $q_tugas->fetch_assoc() : null;
if (!$tugas) die('Tugas tidak ditemukan atau Anda tidak berhak mengakses.');

// Proses update nilai
if (isset($_POST['action']) && $_POST['action'] === 'update_nilai') {
    $id_pengumpulan = intval($_POST['id_pengumpulan']);
    $nilai = intval($_POST['nilai']);
    if ($nilai >= 0 && $nilai <= 100) {
        $conn->query("UPDATE tugas_pengumpulan SET nilai=$nilai WHERE id=$id_pengumpulan");
    }
    header('Location: tugas_pengumpulan.php?id='.$id_tugas);
    exit();
}

// Proses hapus pengumpulan siswa
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id_peng = intval($_GET['hapus']);
    $q = $conn->query("SELECT file FROM tugas_pengumpulan WHERE id=$id_peng");
    if ($q && $row = $q->fetch_assoc()) {
        if ($row['file'] && file_exists("../uploads/tugas/".$row['file'])) {
            unlink("../uploads/tugas/".$row['file']);
        }
        $conn->query("DELETE FROM tugas_pengumpulan WHERE id=$id_peng");
    }
    header('Location: tugas_pengumpulan.php?id='.$id_tugas);
    exit();
}

// Proses checklist fisik
if (isset($_GET['cekfisik']) && is_numeric($_GET['cekfisik'])) {
    if ($tugas['tipe_pengumpulan'] === 'fisik') {
        $id_siswa = intval($_GET['cekfisik']);
        // Cek apakah sudah ada record
        $cek = $conn->query("SELECT id FROM tugas_pengumpulan WHERE id_tugas=$id_tugas AND id_siswa=$id_siswa");
        if ($cek && $cek->num_rows) {
            $conn->query("UPDATE tugas_pengumpulan SET status='sudah', tanggal_kumpul=NOW() WHERE id_tugas=$id_tugas AND id_siswa=$id_siswa");
        } else {
            $conn->query("INSERT INTO tugas_pengumpulan (id_tugas, id_siswa, status, tanggal_kumpul) VALUES ($id_tugas, $id_siswa, 'sudah', NOW())");
        }
        header('Location: tugas_pengumpulan.php?id='.$id_tugas);
        exit();
    }
}

// Ambil daftar siswa di kelas tugas
$kelas_id = $tugas['id_kelas'];
$stmt = $conn->prepare("SELECT id, nama_lengkap, username FROM users WHERE role='siswa' AND id_kelas=? ORDER BY nama_lengkap");
$stmt->bind_param("i", $kelas_id);
$stmt->execute();
$siswa = $stmt->get_result();
// Ambil daftar pengumpulan siswa
$pengumpulan = $conn->query("SELECT tp.*, u.nama_lengkap, u.username FROM tugas_pengumpulan tp JOIN users u ON tp.id_siswa=u.id WHERE tp.id_tugas=$id_tugas ORDER BY tp.tanggal_kumpul DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengumpulan Tugas Siswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="main">
    <h2>Pengumpulan Tugas: <?=htmlspecialchars($tugas['judul'])?></h2>
    <a href="tugas.php">&laquo; Kembali ke Daftar Tugas</a>
    <?php if($tugas['tipe_pengumpulan']==='fisik'): ?>
    <table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;margin-top:16px;">
        <tr><th>Nama Siswa</th><th>Username</th><th>Status</th><th>Tanggal Kumpul</th><th>Nilai</th><th>Checklist</th></tr>
        <?php
        // Ambil status pengumpulan per siswa
        $pengumpulan_map = [];
        $pengq = $conn->query("SELECT * FROM tugas_pengumpulan WHERE id_tugas=$id_tugas");
        while($p=$pengq->fetch_assoc()) $pengumpulan_map[$p['id_siswa']] = $p;
        if($siswa && $siswa->num_rows): while($row=$siswa->fetch_assoc()):
            $id_siswa = $row['id'];
            $status = isset($pengumpulan_map[$id_siswa]) && $pengumpulan_map[$id_siswa]['status'] === 'sudah' ? '<span style="color:green;">Sudah</span>' : '<span style="color:orange;">Belum</span>';
            $tgl = isset($pengumpulan_map[$id_siswa]) ? htmlspecialchars($pengumpulan_map[$id_siswa]['tanggal_kumpul']) : '-';
            $nilai = isset($pengumpulan_map[$id_siswa]) ? htmlspecialchars($pengumpulan_map[$id_siswa]['nilai']) : '';
        ?>
        <tr>
            <td><?=htmlspecialchars($row['nama_lengkap'])?></td>
            <td><?=htmlspecialchars($row['username'])?></td>
            <td><?=$status?></td>
            <td><?=$tgl?></td>
            <td><?php if(isset($pengumpulan_map[$id_siswa]) && $pengumpulan_map[$id_siswa]['status'] === 'sudah'): ?>
                <form method="post" style="margin:0;">
                    <input type="hidden" name="action" value="update_nilai">
                    <input type="hidden" name="id_pengumpulan" value="<?=htmlspecialchars($pengumpulan_map[$id_siswa]['id'])?>">
                    <input type="number" name="nilai" value="<?=$nilai?>" min="0" max="100" style="width:60px;">
                    <button type="submit" style="margin-left:8px;">Simpan</button>
                </form>
            <?php endif; ?></td>
            <td><?php if(!isset($pengumpulan_map[$id_siswa]) || $pengumpulan_map[$id_siswa]['status']!=='sudah'): ?><a href="?id=<?=$id_tugas?>&cekfisik=<?=$id_siswa?>" onclick="return confirm('Tandai siswa sudah mengumpulkan tugas fisik?')" style="color:green;">Centang</a><?php else: ?>✔<?php endif; ?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="6">Tidak ada siswa di kelas ini.</td></tr>
        <?php endif; ?>
    </table>
    <?php else: ?>
    <table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;margin-top:16px;">
        <tr><th>Nama Siswa</th><th>Username</th><th>File</th><th>Tanggal Kumpul</th><th>Nilai</th><th>Hapus</th></tr>
        <?php if($pengumpulan && $pengumpulan->num_rows): while($row=$pengumpulan->fetch_assoc()): ?>
        <tr>
            <td><?=htmlspecialchars($row['nama_lengkap'])?></td>
            <td><?=htmlspecialchars($row['username'])?></td>
            <td><?php if($row['file']): ?><a href="../uploads/tugas/<?=htmlspecialchars($row['file'])?>" target="_blank">Lihat File</a><?php else: ?>-<?php endif; ?></td>
            <td><?=htmlspecialchars($row['tanggal_kumpul'])?></td>
            <td>
                <form method="post" style="margin:0;">
                    <input type="hidden" name="action" value="update_nilai">
                    <input type="hidden" name="id_pengumpulan" value="<?=htmlspecialchars($row['id'])?>">
                    <input type="number" name="nilai" value="<?=htmlspecialchars($row['nilai'])?>" min="0" max="100" style="width:60px;">
                    <button type="submit" style="margin-left:8px;">Simpan</button>
                </form>
            </td>
            <td><a href="?id=<?=$id_tugas?>&hapus=<?=$row['id']?>" onclick="return confirm('Hapus file tugas siswa ini?')" style="color:red;">Hapus</a></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="6">Belum ada siswa yang mengumpulkan tugas ini.</td></tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>
</div>
</body>
</html>
