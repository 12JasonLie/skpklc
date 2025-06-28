<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'siswa' role
check_login('siswa');
$id_siswa = $_SESSION['user_id'];
$ekskul = $conn->query("SELECT e.*, u.nama_lengkap as guru FROM ekskul_siswa es JOIN ekskul e ON es.id_ekskul=e.id JOIN users u ON e.id_guru=u.id WHERE es.id_siswa=$id_siswa ORDER BY e.hari, e.jadwal_jam, e.nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ekstrakurikuler Saya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @media (max-width: 600px) {
            table, th, td { font-size: 0.98em; }
            .main { padding: 10px; }
        }
        .ex-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .ex-table th, .ex-table td { border: 1px solid #bbb; padding: 6px 8px; }
        .ex-table th { background: #f0f0ff; }
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
        <h2 style="margin-top:0;margin-bottom:18px;"><i class="fa-solid fa-people-group"></i> Ekstrakurikuler yang Diikuti</h2>
    <table class="ex-table">
        <tr><th>Nama Ekskul</th><th>Hari</th><th>Jam</th><th>Tempat</th><th>Pembimbing</th><th>Info</th></tr>
        <?php while($e = $ekskul->fetch_assoc()): ?>
        <tr>
            <td><?=htmlspecialchars($e['nama'])?></td>
            <td><?=htmlspecialchars($e['hari'])?></td>
            <td><?=htmlspecialchars($e['jadwal_jam'])?></td>
            <td><?=htmlspecialchars($e['tempat'])?></td>
            <td><?=htmlspecialchars($e['guru'])?></td>
            <td><?=htmlspecialchars($e['info'])?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
