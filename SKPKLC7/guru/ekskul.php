<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'guru' role
check_login('guru');

// Handle tambah/edit/hapus ekskul
$msg = '';
if (isset($_POST['aksi']) && $_POST['aksi']==='tambah') {
    $stmt = $conn->prepare("INSERT INTO ekskul (nama, jadwal_jam, hari, tempat, info, id_guru) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssi', $_POST['nama'], $_POST['jadwal'], $_POST['hari'], $_POST['tempat'], $_POST['info'], $_SESSION['user_id']);
    $stmt->execute();
    $msg = 'Ekskul berhasil ditambah!';
}
if (isset($_POST['aksi']) && $_POST['aksi']==='edit' && is_numeric($_POST['id'])) {
    $stmt = $conn->prepare("UPDATE ekskul SET nama=?, jadwal_jam=?, hari=?, tempat=?, info=? WHERE id=? AND id_guru=?");
    $stmt->bind_param('sssssii', $_POST['nama'], $_POST['jadwal'], $_POST['hari'], $_POST['tempat'], $_POST['info'], $_POST['id'], $_SESSION['user_id']);
    $stmt->execute();
    $msg = 'Ekskul berhasil diubah!';
}
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM absensi_ekskul WHERE id_ekskul=$id");
    $conn->query("DELETE FROM ekskul_siswa WHERE id_ekskul=$id");
    $conn->query("DELETE FROM ekskul WHERE id=$id AND id_guru=".$_SESSION['user_id']);
    $msg = 'Ekskul berhasil dihapus!';
}

// Data ekskul milik guru
$ekskul = $conn->query("SELECT * FROM ekskul WHERE id_guru=".$_SESSION['user_id']." ORDER BY hari, jadwal_jam, nama");

// Data siswa untuk tambah ke ekskul
$siswa = $conn->query("SELECT id, nama_lengkap, username FROM users WHERE role='siswa' ORDER BY nama_lengkap");

// Tambah siswa ke ekskul
if (isset($_POST['aksi']) && $_POST['aksi']==='tambah_siswa' && is_numeric($_POST['id_ekskul']) && is_numeric($_POST['id_siswa'])) {
    $cek = $conn->query("SELECT * FROM ekskul_siswa WHERE id_ekskul={$_POST['id_ekskul']} AND id_siswa={$_POST['id_siswa']}");
    if (!$cek->num_rows) {
        $conn->query("INSERT INTO ekskul_siswa (id_ekskul, id_siswa) VALUES ({$_POST['id_ekskul']}, {$_POST['id_siswa']})");
        $msg = 'Siswa berhasil ditambahkan ke ekskul!';
    }
}
// Hapus siswa dari ekskul
if (isset($_GET['hapus_siswa']) && is_numeric($_GET['hapus_siswa']) && is_numeric($_GET['ekskul'])) {
    $conn->query("DELETE FROM ekskul_siswa WHERE id={$_GET['hapus_siswa']} AND id_ekskul={$_GET['ekskul']}");
    $msg = 'Siswa berhasil dihapus dari ekskul!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Ekstrakurikuler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @media (max-width: 600px) {
            table, th, td { font-size: 0.95em; }
            .main { padding: 10px; }
        }
        .ex-form { margin-bottom: 24px; background: #f8f8fa; padding: 12px 18px; border-radius: 7px; }
        .ex-form input, .ex-form select, .ex-form textarea { margin: 4px 0 10px 0; width: 100%; max-width: 340px; }
        .ex-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .ex-table th, .ex-table td { border: 1px solid #bbb; padding: 6px 8px; }
        .ex-table th { background: #f0f0ff; }
        .btn { padding: 4px 12px; font-size: 0.95em; }
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
    <div class="card" style="max-width:700px;margin:auto;margin-bottom:24px;padding:24px 20px 20px 20px;box-shadow:0 2px 10px #e0e7ef;background:#fff;border-radius:12px;">
        <h2 style="margin-top:0;margin-bottom:18px;"><i class="fa-solid fa-people-group"></i> Manajemen Ekstrakurikuler</h2>
    <?php if ($msg) echo '<div style="color:green;margin-bottom:10px;">'.$msg.'</div>'; ?>
    <div class="ex-form">
        <form method="post">
            <input type="hidden" name="aksi" value="tambah">
            <b>Tambah Ekskul Baru</b><br>
            <input type="text" name="nama" placeholder="Nama Ekskul" required><br>
            <input type="text" name="jadwal" placeholder="Jam (misal: 15:00-17:00)" required><br>
            <select name="hari" required>
                <option value="">Pilih Hari</option>
                <option>Senin</option><option>Selasa</option><option>Rabu</option><option>Kamis</option><option>Jumat</option><option>Sabtu</option>
            </select><br>
            <input type="text" name="tempat" placeholder="Tempat" required><br>
            <textarea name="info" placeholder="Info tambahan"></textarea><br>
            <button class="btn" type="submit">Tambah Ekskul</button>
        </form>
    </div>
    <?php while($e = $ekskul->fetch_assoc()): ?>
    <div class="ex-form">
        <form method="post" style="display:inline-block;">
            <input type="hidden" name="aksi" value="edit">
            <input type="hidden" name="id" value="<?=$e['id']?>">
            <b>Edit Ekskul:</b> <input type="text" name="nama" value="<?=htmlspecialchars($e['nama'])?>" required>
            <input type="text" name="jadwal" value="<?=htmlspecialchars($e['jadwal_jam'])?>" required>
            <select name="hari" required>
                <option <?=$e['hari']==='Senin'?'selected':''?>>Senin</option>
                <option <?=$e['hari']==='Selasa'?'selected':''?>>Selasa</option>
                <option <?=$e['hari']==='Rabu'?'selected':''?>>Rabu</option>
                <option <?=$e['hari']==='Kamis'?'selected':''?>>Kamis</option>
                <option <?=$e['hari']==='Jumat'?'selected':''?>>Jumat</option>
                <option <?=$e['hari']==='Sabtu'?'selected':''?>>Sabtu</option>
            </select>
            <input type="text" name="tempat" value="<?=htmlspecialchars($e['tempat'])?>" required>
            <textarea name="info" placeholder="Info tambahan"><?=htmlspecialchars($e['info'])?></textarea>
            <button class="btn" type="submit">Simpan</button>
            <a href="?hapus=<?=$e['id']?>" onclick="return confirm('Hapus ekskul ini?')" style="color:red;">Hapus</a>
        </form>
        <div style="margin-top:10px;">
            <b>Daftar Siswa:</b>
            <table class="ex-table">
                <tr><th>Nama</th><th>Username</th><th>Hapus</th></tr>
                <?php
                $sis = $conn->query("SELECT ekskul_siswa.id, users.nama_lengkap, users.username FROM ekskul_siswa JOIN users ON ekskul_siswa.id_siswa=users.id WHERE ekskul_siswa.id_ekskul=".$e['id']);
                while($s=$sis->fetch_assoc()): ?>
                    <tr><td><?=htmlspecialchars($s['nama_lengkap'])?></td><td><?=htmlspecialchars($s['username'])?></td><td><a href="?ekskul=<?=$e['id']?>&hapus_siswa=<?=$s['id']?>" onclick="return confirm('Hapus siswa dari ekskul?')" style="color:red;">Hapus</a></td></tr>
                <?php endwhile; ?>
            </table>
            <form method="post" style="margin-top:7px;">
                <input type="hidden" name="aksi" value="tambah_siswa">
                <input type="hidden" name="id_ekskul" value="<?=$e['id']?>">
                <select name="id_siswa" required>
                    <option value="">Tambah siswa...</option>
                    <?php mysqli_data_seek($siswa, 0); while($sw=$siswa->fetch_assoc()): ?>
                        <option value="<?=$sw['id']?>"><?=$sw['nama_lengkap']?> (<?=$sw['username']?>)</option>
                    <?php endwhile; ?>
                </select>
                <button class="btn" type="submit">Tambah</button>
            </form>
        </div>
    </div>
    <?php endwhile; ?>
</div>
</body>
</html>
