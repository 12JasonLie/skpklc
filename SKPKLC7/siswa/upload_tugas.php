<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'siswa' role
check_login('siswa');

$id_tugas = $_GET['id'] ?? '';
$id_siswa = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_tugas) {
    if ($_FILES['file']['name']) {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $fname = 'kumpul_'.date('Ymd_His').'_'.rand(100,999).'.'.$ext;
        $dest = '../uploads/tugas/'.$fname;
        if (!is_dir('../uploads/tugas')) mkdir('../uploads/tugas',0777,true);
        move_uploaded_file($_FILES['file']['tmp_name'], $dest);
        $file = $fname;
        $stmt = $conn->prepare("INSERT INTO tugas_pengumpulan (id_tugas, id_siswa, file, status, tanggal_kumpul) VALUES (?, ?, ?, 'sudah', NOW()) ON DUPLICATE KEY UPDATE file=VALUES(file), status='sudah', tanggal_kumpul=NOW()");
        $stmt->bind_param('iis', $id_tugas, $id_siswa, $file);
        $stmt->execute();
    }
}
header('Location: tugas.php');
exit();
