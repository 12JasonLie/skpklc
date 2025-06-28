<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'admin' role
check_login('admin');

// Handle tambah kelas
$msg = '';
if (isset($_POST['aksi']) && $_POST['aksi']==='tambah') {
    $nama = trim($_POST['nama']);
    if ($nama) {
        $stmt = $conn->prepare("INSERT INTO kelas (nama_kelas) VALUES (?)");
        $stmt->bind_param('s', $nama);
        $stmt->execute();
        $msg = 'Kelas berhasil ditambah!';
    }
}
// Handle hapus kelas
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM kelas WHERE id_kelas=$id");
    $msg = 'Kelas berhasil dihapus!';
}
// Data kelas
$kelas = $conn->query("SELECT * FROM kelas ORDER BY nama_kelas");
?>
<?php
$page_title = 'Manajemen Kelas';
$current_page = basename($_SERVER['PHP_SELF']);

// Include header and sidebar
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main">
    <div class="page-header">
        <h1><i class="fas fa-chalkboard"></i> Manajemen Kelas</h1>
        <?php if($msg): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?=htmlspecialchars($msg)?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-plus-circle"></i> Tambah Kelas Baru</h2>
        </div>
        <div class="card-body">
            <form method="post" class="form-inline">
                <input type="hidden" name="aksi" value="tambah">
                <div class="form-group" style="flex: 1;">
                    <label for="nama" class="sr-only">Nama Kelas</label>
                    <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan nama kelas" required>
                </div>
                <button type="submit" class="btn btn-primary ml-2">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </button>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Daftar Kelas</h2>
        </div>
        <div class="card-body">
            <?php if ($kelas->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Kelas</th>
                                <th class="text-center">Jumlah Siswa</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Reset the result set pointer
                            $kelas->data_seek(0);
                            $no = 1; 
                            while($k = $kelas->fetch_assoc()): 
                                // Get student count for each class
                                $stmt = $conn->prepare("SELECT COUNT(*) as jumlah FROM users WHERE id_kelas = ?");
                                $stmt->bind_param('i', $k['id_kelas']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $count = $result->fetch_assoc()['jumlah'];
                            ?>
                            <tr>
                                <td><?=$no++?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-chalkboard-teacher mr-2 text-primary"></i>
                                        <span><?=htmlspecialchars($k['nama_kelas'])?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge">
                                        <?=$count?> Siswa
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="?hapus=<?=$k['id_kelas']?>" 
                                       onclick="return confirm('Hapus kelas ini? Semua data yang terkait dengan kelas ini akan terpengaruh.')" 
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
                    <i class="fas fa-chalkboard"></i>
                    <p>Belum ada data kelas</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Custom styles for Kelas page */
.form-inline {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.form-control {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    width: 100%;
}

.form-control:focus {
    border-color: var(--biru-muda);
    box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.2);
    outline: none;
}

.ml-2 {
    margin-left: 0.5rem;
}

.mr-2 {
    margin-right: 0.5rem;
}

.d-flex {
    display: flex;
}

.align-items-center {
    align-items: center;
}

.badge {
    display: inline-block;
    padding: 0.35em 0.65em;
    font-size: 0.85em;
    font-weight: 600;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 10rem;
    background-color: var(--biru-muda);
}

.text-primary {
    color: var(--biru-muda) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .form-inline {
        flex-direction: column;
        align-items: stretch;
    }
    
    .ml-2 {
        margin-left: 0;
        margin-top: 0.75rem;
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
