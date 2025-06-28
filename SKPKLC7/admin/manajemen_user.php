<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'admin' role
check_login('admin');

// Tambah/Edit/Reset/Hapus user
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';
$msg = '';

// Tambah user
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama_lengkap']);
    $role = $_POST['role'];
    $id_kelas = $_POST['id_kelas'] ?: null;
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    // Jika bukan siswa/admin, id_kelas NULL
    if ($role !== 'siswa' && $role !== 'admin') $id_kelas = null;
    $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role, id_kelas, email, no_hp) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssiss', $username, $password, $nama, $role, $id_kelas, $email, $no_hp);
    if ($stmt->execute()) $msg = 'User berhasil ditambah!';
    else $msg = 'Error: Username sudah digunakan.';
}
// Reset password
if ($action === 'reset' && $id) {
    $newpass = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param('si', $newpass, $id);
    $stmt->execute();
    $msg = 'Password direset ke 123456!';
}
// Hapus user
if ($action === 'delete' && $id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $msg = 'User dihapus!';
}
// Ambil data kelas
$kelas = $conn->query("SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas");
$kelas_arr = [];
while ($row = $kelas->fetch_assoc()) $kelas_arr[$row['id_kelas']] = $row['nama_kelas'];
// Ambil data user
$users = $conn->query("SELECT u.*, k.nama_kelas FROM users u LEFT JOIN kelas k ON u.id_kelas=k.id_kelas ORDER BY u.role, u.nama_lengkap");
?>
<?php
$page_title = 'Manajemen Pengguna';
$current_page = basename($_SERVER['PHP_SELF']);

// Include header and sidebar
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main">
    <div class="page-header">
        <h1><i class="fas fa-users-cog"></i> Manajemen Pengguna</h1>
        <?php if ($msg): ?>
        <div class="alert alert-<?= strpos($msg, 'Error') !== false ? 'danger' : 'success' ?>">
            <i class="fas <?= strpos($msg, 'Error') !== false ? 'fa-exclamation-circle' : 'fa-check-circle' ?>"></i>
            <?=htmlspecialchars($msg)?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-user-plus"></i> Tambah Pengguna Baru</h2>
        </div>
        <div class="card-body">
            <form method="post" action="?action=add" class="form-grid">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                </div>
                
                <div class="form-group">
                    <label for="nama_lengkap"><i class="fas fa-id-card"></i> Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
                </div>
                
                <div class="form-group">
                    <label for="role"><i class="fas fa-user-tag"></i> Peran</label>
                    <select id="role" name="role" required onchange="toggleKelasSelect()">
                        <option value="" disabled selected>Pilih Peran</option>
                        <option value="guru">Guru</option>
                        <option value="siswa">Siswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div class="form-group" id="kelasGroup" style="display: none;">
                    <label for="id_kelas"><i class="fas fa-school"></i> Kelas</label>
                    <select id="id_kelas" name="id_kelas">
                        <option value="">Pilih Kelas</option>
                        <?php foreach($kelas_arr as $kid=>$knama): ?>
                            <option value="<?=htmlspecialchars($kid)?>"><?=htmlspecialchars($knama)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" placeholder="contoh@email.com">
                </div>
                
                <div class="form-group">
                    <label for="no_hp"><i class="fas fa-phone"></i> No. HP</label>
                    <input type="text" id="no_hp" name="no_hp" placeholder="0812-3456-7890">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-key"></i> Password</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" placeholder="Buat password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-users"></i> Daftar Pengguna</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Peran</th>
                            <th>Kelas</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users->num_rows > 0): ?>
                            <?php $no = 1; while($u = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?=$no++?></td>
                                <td><?=htmlspecialchars($u['username'])?></td>
                                <td><?=htmlspecialchars($u['nama_lengkap'])?></td>
                                <td>
                                    <span class="role-badge role-<?=htmlspecialchars($u['role'])?>">
                                        <?=ucfirst(htmlspecialchars($u['role']))?>
                                    </span>
                                </td>
                                <td><?=!empty($u['nama_kelas']) ? htmlspecialchars($u['nama_kelas']) : '-'?></td>
                                <td><?=!empty($u['email']) ? htmlspecialchars($u['email']) : '-'?></td>
                                <td><?=!empty($u['no_hp']) ? htmlspecialchars($u['no_hp']) : '-'?></td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="?action=reset&id=<?=$u['id']?>" 
                                           onclick="return confirm('Reset password user ini ke 123456?')" 
                                           class="btn btn-warning btn-sm"
                                           title="Reset Password">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        <a href="?action=delete&id=<?=$u['id']?>" 
                                           onclick="return confirm('Hapus user ini? Tindakan ini tidak dapat dibatalkan.')" 
                                           class="btn btn-danger btn-sm"
                                           title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-user-slash"></i>
                                        <p>Belum ada data pengguna</p>
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

<script>
function toggleKelasSelect() {
    const roleSelect = document.getElementById('role');
    const kelasGroup = document.getElementById('kelasGroup');
    const kelasSelect = document.getElementById('id_kelas');
    
    if (roleSelect.value === 'siswa') {
        kelasGroup.style.display = 'block';
        kelasSelect.required = true;
    } else {
        kelasGroup.style.display = 'none';
        kelasSelect.required = false;
        kelasSelect.value = '';
    }
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = event.currentTarget.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<style>
/* General Styles */
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

.alert-danger {
    background-color: #ffebee;
    color: #c62828;
    border-left: 4px solid #f44336;
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

/* Form Styles */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.25rem;
    align-items: start;
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
.form-group input[type="email"],
.form-group input[type="password"],
.form-group input[type="tel"],
.form-group select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group input:focus,
.form-group select:focus {
    border-color: var(--biru-muda);
    box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.2);
    outline: none;
}

.password-input {
    position: relative;
    display: flex;
}

.password-input input {
    padding-right: 2.5rem;
}

.toggle-password {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #777;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: color 0.2s;
}

.toggle-password:hover {
    color: var(--biru-muda);
}

/* Buttons */
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

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
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

/* Table Styles */
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

/* Role Badges */
.role-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
}

.role-admin {
    background-color: #e3f2fd;
    color: #1565c0;
}

.role-guru {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.role-siswa {
    background-color: #fff3e0;
    color: #e65100;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

/* Empty State */
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

/* Responsive Styles */
@media (max-width: 1200px) {
    .data-table {
        min-width: 1000px;
    }
}

@media (max-width: 992px) {
    .form-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 1.5rem;
    }
    
    .card-header h2 {
        font-size: 1.1rem;
    }
    
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
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .btn-sm {
        width: 100%;
    }
}
</style>

<?php
// Include footer
require_once '../includes/footer.php';
?>
