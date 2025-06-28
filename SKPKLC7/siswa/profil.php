<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'siswa' role
check_login('siswa');

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $password = $_POST['password'];
    if ($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET email=?, no_hp=?, password=? WHERE id=?");
        $stmt->bind_param('sssi', $email, $no_hp, $hash, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET email=?, no_hp=? WHERE id=?");
        $stmt->bind_param('ssi', $email, $no_hp, $user_id);
    }
    $stmt->execute();
    $msg = 'Profil berhasil diperbarui!';
}
$stmt = $conn->prepare("SELECT username, email, no_hp FROM users WHERE id=?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $no_hp);
$stmt->fetch();
$stmt->close();
?>
<?php
$page_title = 'Profil Siswa';
$current_page = basename($_SERVER['PHP_SELF']);

// Include header and sidebar
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main">
    <div class="page-header">
        <h1><i class="fas fa-user-graduate"></i> Profil Saya</h1>
        <?php if (isset($msg)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?=htmlspecialchars($msg)?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-user-edit"></i> Informasi Akun</h2>
        </div>
        <div class="card-body">
            <form method="post" class="profile-form">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <div class="form-control-static"><?=htmlspecialchars($username)?></div>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" value="<?=htmlspecialchars($email)?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="no_hp"><i class="fas fa-phone"></i> No. HP</label>
                    <input type="text" id="no_hp" name="no_hp" value="<?=htmlspecialchars($no_hp)?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-key"></i> Password Baru</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin ganti" class="form-control">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Profile page specific styles */
.profile-form {
    max-width: 600px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 1.5rem;
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

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
    border-color: var(--biru-muda);
    box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.2);
    outline: none;
}

.form-control-static {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border: 1px solid #eee;
    border-radius: 8px;
    color: #666;
}

.password-input {
    position: relative;
    display: flex;
}

.password-input .form-control {
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

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #6c757d;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
    text-align: right;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .page-header h1 {
        font-size: 1.5rem;
    }
    
    .card-header h2 {
        font-size: 1.1rem;
    }
    
    .btn {
        width: 100%;
    }
}
</style>

<script>
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

<?php
// Include footer
require_once '../includes/footer.php';
?>
