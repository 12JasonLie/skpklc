<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'guru' role
check_login('guru');

$msg = '';
$kelas = $conn->query("SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");
$kelas_arr = [];
while ($row = $kelas->fetch_assoc()) $kelas_arr[$row['id_kelas']] = $row['nama_kelas'];

// Proses hapus agenda
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $ida = intval($_GET['hapus']);
    $conn->query("DELETE FROM agenda WHERE id=$ida AND id_guru=".$_SESSION['user_id']);
    $msg = 'Agenda berhasil dihapus!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $tanggal = $_POST['tanggal'];
    $id_kelas = $_POST['id_kelas'] ?: null;
    $untuk_semua = isset($_POST['untuk_semua']) ? 1 : 0;
    $stmt = $conn->prepare("INSERT INTO agenda (judul, deskripsi, tanggal, id_guru, id_kelas, untuk_semua_kelas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssiii', $judul, $deskripsi, $tanggal, $_SESSION['user_id'], $id_kelas, $untuk_semua);
    $stmt->execute();
    $msg = 'Agenda berhasil diupload!';
}
$agenda = $conn->query("SELECT a.*, k.nama_kelas FROM agenda a LEFT JOIN kelas k ON a.id_kelas=k.id_kelas WHERE a.id_guru=".$_SESSION['user_id']." ORDER BY a.tanggal_upload DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Agenda - SKPKLC</title>
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
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 0.95rem;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .card-body {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            margin-bottom: 7px;
            font-weight: 500;
            color: #444;
        }
        .form-control, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-control:focus, textarea:focus {
            border-color: var(--biru-muda);
            outline: none;
            box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
        }
        textarea {
            min-height: 70px;
            resize: vertical;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .form-col {
            flex: 1;
            min-width: 200px;
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
        }
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
        .agenda-table td .fa-trash { color: #e53e3e; }
        .agenda-table td .fa-trash:hover { color: #c53030; }
        .agenda-table th, .agenda-table td { text-align: left; }
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
    <h1 class="page-title"><i class="fas fa-calendar-plus"></i> Upload Agenda / Informasi</h1>
    <?php if ($msg): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <div class="card">
        <div class="card-body">
            <form method="post" class="form-row" autocomplete="off">
                <div class="form-col">
                    <label class="form-label">Judul Agenda</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="form-col">
                    <label class="form-label">Tanggal Agenda</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>
                <div class="form-col">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-control">
                        <option value="">Pilih Kelas</option>
                        <?php foreach($kelas_arr as $kid=>$knama) echo "<option value='$kid'>$knama</option>";?>
                    </select>
                    <label style="margin-top:7px;display:block;"><input type="checkbox" name="untuk_semua"> Untuk Semua Kelas</label>
                </div>
                <div class="form-col" style="flex:2;min-width:260px;">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" placeholder="Deskripsi (opsional)"></textarea>
                </div>
                <div class="form-col" style="align-self:flex-end;min-width:160px;">
                    <button class="btn" type="submit"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body agenda-table-responsive">
            <h2 style="margin-top:0;margin-bottom:12px;font-size:1.2rem;color:var(--biru-tua);font-weight:600;"><i class="fas fa-list"></i> Daftar Agenda Anda</h2>
            <table class="agenda-table">
                <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kelas</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Upload</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php while($a=$agenda->fetch_assoc()): ?>
                <tr>
                    <td><?=htmlspecialchars($a['judul'])?></td>
                    <td><?= $a['untuk_semua_kelas'] ? '<span title="Semua Kelas"><i class="fas fa-users"></i> Semua</span>' : htmlspecialchars($a['nama_kelas']) ?></td>
                    <td><i class="far fa-calendar-alt"></i> <?=htmlspecialchars($a['tanggal'])?></td>
                    <td><?=htmlspecialchars($a['deskripsi'])?></td>
                    <td><?=htmlspecialchars($a['tanggal_upload'])?></td>
                    <td>
                        <a href="?hapus=<?=$a['id']?>" onclick="return confirm('Hapus agenda ini?')" title="Hapus Agenda"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
</body>
</html>