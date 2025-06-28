<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'admin' role
check_login('admin');

// Handle tambah ekskul
$msg = '';
if (isset($_POST['aksi']) && $_POST['aksi']==='tambah') {
    $nama = trim($_POST['nama']);
    $jadwal = trim($_POST['jadwal']);
    $hari = trim($_POST['hari']);
    $tempat = trim($_POST['tempat']);
    if ($nama && $jadwal && $hari && $tempat) {
        $stmt = $conn->prepare("INSERT INTO ekskul (nama, jadwal_jam, hari, tempat) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $nama, $jadwal, $hari, $tempat);
        $stmt->execute();
        $msg = 'Ekskul berhasil ditambah!';
    }
}
// Handle hapus ekskul
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM ekskul WHERE id=$id");
    $msg = 'Ekskul berhasil dihapus!';
}
// Data ekskul
$ekskul = $conn->query("SELECT * FROM ekskul ORDER BY nama");
?>
<?php
$page_title = 'Data Ekstrakurikuler';
$current_page = basename($_SERVER['PHP_SELF']);

// Include header and sidebar
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main">
    <div class="page-header">
        <h1><i class="fas fa-futbol"></i> Data Ekstrakurikuler</h1>
        <?php if($msg): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?=htmlspecialchars($msg)?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-plus-circle"></i> Tambah Ekskul Baru</h2>
        </div>
        <div class="card-body">
            <form method="post" class="form-grid">
                <input type="hidden" name="aksi" value="tambah">
                
                <div class="form-group">
                    <label for="nama"><i class="fas fa-tag"></i> Nama Ekskul</label>
                    <input type="text" id="nama" name="nama" placeholder="Contoh: Futsal, Pramuka, Paskibra" required>
                </div>
                
                <div class="form-group">
                    <label for="hari"><i class="far fa-calendar"></i> Hari</label>
                    <select id="hari" name="hari" required>
                        <option value="" disabled selected>Pilih Hari</option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="jadwal"><i class="far fa-clock"></i> Jam Pelaksanaan</label>
                    <input type="text" id="jadwal" name="jadwal" placeholder="Contoh: 15:00-17:00" required>
                </div>
                
                <div class="form-group">
                    <label for="tempat"><i class="fas fa-map-marker-alt"></i> Tempat</label>
                    <input type="text" id="tempat" name="tempat" placeholder="Contoh: Lapangan Sekolah, Aula" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Ekskul
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Daftar Ekstrakurikuler</h2>
        </div>
        <div class="card-body">
            <?php if ($ekskul->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Ekskul</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Tempat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while($e = $ekskul->fetch_assoc()): ?>
                            <tr>
                                <td><?=$no++?></td>
                                <td><?=htmlspecialchars($e['nama'])?></td>
                                <td>
                                    <span class="badge">
                                        <?=htmlspecialchars($e['hari']) ?>
                                    </span>
                                </td>
                                <td><?=htmlspecialchars($e['jadwal_jam'])?></td>
                                <td><?=htmlspecialchars($e['tempat'])?></td>
                                <td class="text-center">
                                    <a href="?hapus=<?=$e['id']?>" 
                                       onclick="return confirm('Hapus ekskul ini? Tindakan ini tidak dapat dibatalkan.')" 
                                       class="btn btn-danger btn-sm"
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>Belum ada data ekskul</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Custom styles for Ekskul page */
.badge {
    display: inline-block;
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
    background-color: var(--biru-muda);
}

/* Override some styles from the main style */
.form-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .data-table th,
    .data-table td {
        padding: 0.75rem 0.5rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .page-header h1 {
        font-size: 1.5rem;
    }
    
    .card-header h2 {
        font-size: 1.1rem;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php
// Include footer
require_once '../includes/footer.php';
?>
