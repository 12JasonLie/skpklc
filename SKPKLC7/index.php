<?php
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/session.php';

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php'); exit();
    } elseif ($_SESSION['role'] === 'guru') {
        header('Location: guru/dashboard.php'); exit();
    } elseif ($_SESSION['role'] === 'siswa') {
        header('Location: siswa/dashboard.php'); exit();
    }
}

// Ambil error dari session jika ada, lalu hapus agar tidak muncul setelah refresh
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            $_SESSION['id_kelas'] = $row['id_kelas'];
            header('Location: '.$row['role'].'/dashboard.php');
            exit();
        } else {
            $_SESSION['login_error'] = 'Password salah.';
            header('Location: index.php');
            exit();
        }
    } else {
        $_SESSION['login_error'] = 'Username tidak ditemukan.';
        header('Location: index.php');
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SMPK Lawang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div style="display:flex;height:100vh;align-items:center;justify-content:center;">
    <form method="post" style="min-width:320px;padding:32px 28px;background:#fff;border-radius:8px;box-shadow:0 2px 12px #0001;">
        <h2 style="color:var(--biru-tua);margin-bottom:18px;">Login SMPK Lawang</h2>
        <?php if ($error): ?><div style="color:var(--merah);margin-bottom:14px;"> <?=htmlspecialchars($error)?> </div><?php endif; ?>
        <input type="text" name="username" placeholder="Username" required autofocus style="width:100%;">
        <div style="display:flex;align-items:center;width:100%;margin-bottom:10px;position:relative;">
            <input type="password" name="password" id="password" placeholder="Password" required style="flex:1;min-width:0;padding-right:38px;height:38px;box-sizing:border-box;">
            <span onclick="togglePassword()" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;user-select:none;" title="Lihat/Sembunyikan Password">
                <svg id="eyeicon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/></svg>
            </span>
        </div>
        <button class="btn" type="submit" style="width:100%;margin-top:10px;">Login</button>
        <script>
        function togglePassword() {
            var pw = document.getElementById('password');
            var eye = document.getElementById('eyeicon');
            if (pw.type === 'password') {
                pw.type = 'text';
                eye.innerHTML = '<line x1="1" y1="1" x2="23" y2="23" stroke="#e53935" stroke-width="2" /><circle cx="12" cy="12" r="3"/><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>';
            } else {
                pw.type = 'password';
                eye.innerHTML = '<circle cx="12" cy="12" r="3"/><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>';
            }
        }
        </script>
    </form>
</div>
</body>
</html>
