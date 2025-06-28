<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'admin' role
check_login('admin');

$page_title = 'Data Agenda';
$current_page = basename($_SERVER['PHP_SELF']);

// Handle tambah agenda
$msg = '';
if (isset($_POST['aksi']) && $_POST['aksi']==='tambah') {
    $judul = trim($_POST['judul']);
    $tanggal = trim($_POST['tanggal']);
    $deskripsi = trim($_POST['deskripsi']);
    if ($judul && $tanggal) {
        $stmt = $conn->prepare("INSERT INTO agenda (judul, tanggal, deskripsi) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $judul, $tanggal, $deskripsi);
        $stmt->execute();
        $msg = 'Agenda berhasil ditambah!';
    }
}
// Handle hapus agenda
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM agenda WHERE id=$id");
    $msg = 'Agenda berhasil dihapus!';
}
// Data agenda
$agenda = $conn->query("SELECT * FROM agenda ORDER BY tanggal DESC");

// Include header
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main">
    <div class="page-header">
        <h1><i class="far fa-calendar-alt"></i> Data Agenda</h1>
        <?php if($msg): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?=htmlspecialchars($msg)?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-plus-circle"></i> Tambah Agenda Baru</h2>
        </div>
        <div class="card-body">
            <form method="post" class="form-grid">
                <input type="hidden" name="aksi" value="tambah">
                <div class="form-group">
                    <label for="judul"><i class="fas fa-heading"></i> Judul Agenda</label>
                    <input type="text" id="judul" name="judul" placeholder="Masukkan judul agenda" required>
                </div>
                <div class="form-group">
                    <label for="tanggal"><i class="far fa-calendar"></i> Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi"><i class="fas fa-align-left"></i> Deskripsi</label>
                    <input type="text" id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi singkat">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Daftar Agenda</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($agenda->num_rows > 0): ?>
                            <?php $no = 1; while($row = $agenda->fetch_assoc()): ?>
                            <tr>
                                <td><?=$no++?></td>
                                <td><?=htmlspecialchars($row['judul'])?></td>
                                <td><?=date('d M Y', strtotime($row['tanggal']))?></td>
                                <td><?=htmlspecialchars($row['deskripsi'])?></td>
                                <td class="text-center">
                                    <a href="?hapus=<?=$row['id']?>" 
                                       onclick="return confirm('Yakin hapus agenda ini?')" 
                                       class="btn btn-danger btn-sm"
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="empty-state">
                                        <i class="far fa-calendar-times"></i>
                                        <p>Belum ada data agenda</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    color: var(--biru-tua);
    font-size: 1.8rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background-color: #e8f5e9;
    color: #2e7d32;
    border-left: 4px solid #4caf50;
}

.card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
    overflow: hidden;
}

.card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.card-header h2 {
    font-size: 1.25rem;
    margin: 0;
    color: var(--biru-tua);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-body {
    padding: 1.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.25rem;
    align-items: end;
}

.form-group {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #555;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group input[type="text"],
.form-group input[type="date"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group input:focus {
    border-color: var(--biru-muda);
    box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.2);
    outline: none;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    border: none;
    transition: all 0.2s;
    font-size: 1rem;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
}

.btn-primary {
    background-color: var(--biru-muda);
    color: white;
}

.btn-primary:hover {
    background-color: var(--biru-tua);
    transform: translateY(-1px);
}

.btn-danger {
    background-color: #f44336;
    color: white;
}

.btn-danger:hover {
    background-color: #d32f2f;
    transform: translateY(-1px);
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

.data-table th,
.data-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #f0f0f0;
}

.data-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #555;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}

.data-table tbody tr:hover {
    background-color: #f9f9f9;
}

.text-center {
    text-align: center;
}

.empty-state {
    padding: 3rem 1rem;
    text-align: center;
    color: #777;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    margin: 0.5rem 0 0;
    font-size: 1.1rem;
}

/* Responsive styles */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .data-table th,
    .data-table td {
        padding: 0.75rem 0.5rem;
    }
    
    .btn {
        padding: 0.65rem 1.25rem;
    }
}

@media (max-width: 480px) {
    .page-header h1 {
        font-size: 1.5rem;
    }
    
    .card-header h2 {
        font-size: 1.1rem;
    }
    
    .data-table {
        font-size: 0.9rem;
    }
}
</style>

<?php
// Include footer
require_once '../includes/footer.php';
?>
