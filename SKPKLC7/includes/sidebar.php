<?php
if (session_status() == PHP_SESSION_NONE) session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
function get_active_class($page_to_check) {
    $current_file = basename($_SERVER['PHP_SELF']);
    return ($current_file === $page_to_check) ? 'active' : '';
}
?>
<style>
.sidebar {
    position: fixed;
    left: 0; top: 0; bottom: 0;
    width: 220px;
    background: var(--biru-tua, #183153);
    color: #fff;
    z-index: 1000;
    display: flex; flex-direction: column;
    transition: transform 0.25s;
}
.sidebar.collapsed { transform: translateX(-100%); }
.sidebar-header {
    display: flex; align-items: center; gap: 10px;
    padding: 18px 16px 12px 16px;
    background: var(--biru-tua, #183153);
    border-bottom: 1px solid #1e3a5c;
}
.sidebar-logo {
    width: 38px; height: 38px; object-fit: contain; border-radius: 8px;
    background: #fff; padding: 2px;
}
.logo { font-weight: bold; font-size: 1.18em; letter-spacing: 1px; }
.hamburger {
    margin-left: auto; font-size: 1.6em; color: #fff; background: none; border: none; cursor: pointer; display: none;
}
.sidebar-menu {
    flex: 1 1 auto;
    display: flex; flex-direction: column;
    padding: 14px 0;
}
.sidebar-menu a {
    color: #fff; text-decoration: none;
    padding: 12px 24px; display: flex; align-items: center; gap: 12px;
    font-size: 1em; border-left: 4px solid transparent;
    transition: background 0.2s, border-color 0.2s;
}
.sidebar-menu a.active, .sidebar-menu a:hover {
    background: var(--biru-muda, #1e3a5c);
    border-left: 4px solid #ffb300;
    color: #ffb300;
}
.sidebar-footer {
    padding: 16px 20px 16px 20px; border-top: 1px solid #1e3a5c;
}
.logout-btn { color: #fff; text-decoration: none; font-size: 1em; }
.logout-btn:hover { color: #ffb300; }
.user-info { margin-top: 10px; color: #a8b9d0; font-size: 0.98em; display: flex; align-items: center; gap: 7px; }
@media (max-width: 900px) {
    .sidebar { width: 170px; }
    .sidebar-header { padding: 12px 10px 10px 10px; }
    .sidebar-menu a { font-size: 0.97em; padding: 9px 16px; }
    .sidebar-footer { padding: 12px 12px; }
}
@media (max-width: 700px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.open { transform: translateX(0); }
    .hamburger { display: block; }
}
</style>
<script>
function toggleSidebar() {
    var sb = document.querySelector('.sidebar');
    sb.classList.toggle('open');
}
document.addEventListener('DOMContentLoaded', function() {
    var hamburger = document.querySelector('.hamburger');
    if (hamburger) {
        hamburger.addEventListener('click', toggleSidebar);
    }
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        var sb = document.querySelector('.sidebar');
        if (window.innerWidth <= 700 && sb.classList.contains('open')) {
            if (!sb.contains(e.target) && !e.target.classList.contains('hamburger')) {
                sb.classList.remove('open');
            }
        }
    });
});
</script>
<div class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/logo.png" alt="Logo" class="sidebar-logo">
        <div class="logo">SMPK Lawang</div>
        <button class="hamburger" aria-label="Menu"><i class="fa fa-bars"></i></button>
    </div>
    <div class="sidebar-menu">
    <?php if ($role === 'guru'): ?>
        <a href="dashboard.php" class="<?=get_active_class('dashboard.php')?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="profil.php" class="<?=get_active_class('profil.php')?>"><i class="fas fa-user"></i> Profil</a>
        <a href="absensi_harian.php" class="<?=get_active_class('absensi_harian.php')?>"><i class="fas fa-clipboard-check"></i> Absensi Harian</a>
        <a href="materi.php" class="<?=get_active_class('materi.php')?>"><i class="fas fa-book"></i> Upload Materi</a>
        <a href="tugas.php" class="<?=get_active_class('tugas.php')?>"><i class="fas fa-tasks"></i> Upload Tugas</a>
        <a href="nilai.php" class="<?=get_active_class('nilai.php')?>"><i class="fas fa-chart-line"></i> Upload Nilai</a>
        <a href="jadwal.php" class="<?=get_active_class('jadwal.php')?>"><i class="far fa-calendar-alt"></i> Upload Jadwal</a>
        <a href="agenda.php" class="<?=get_active_class('agenda.php')?>"><i class="far fa-calendar-plus"></i> Upload Agenda</a>
        <a href="ekskul.php" class="<?=get_active_class('ekskul.php')?>"><i class="fas fa-futbol"></i> Ekstrakurikuler</a>
        <a href="absensi_ekskul.php" class="<?=get_active_class('absensi_ekskul.php')?>"><i class="fas fa-clipboard-list"></i> Absensi Ekskul</a>
        <a href="rekap_absensi_harian.php" class="<?=get_active_class('rekap_absensi_harian.php')?>"><i class="fas fa-file-alt"></i> Rekap Absensi</a>
    <?php elseif ($role === 'siswa'): ?>
        <a href="dashboard.php" class="<?=get_active_class('dashboard.php')?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="profil.php" class="<?=get_active_class('profil.php')?>"><i class="fas fa-user"></i> Profil</a>
        <a href="presensi_harian.php" class="<?=get_active_class('presensi_harian.php')?>"><i class="fas fa-calendar-check"></i> Presensi Harian</a>
        <a href="absensi_ekskul.php" class="<?=get_active_class('absensi_ekskul.php')?>"><i class="fas fa-futbol"></i> Presensi Ekskul</a>
        <a href="rekap_presensi.php" class="<?=get_active_class('rekap_presensi.php')?>"><i class="fas fa-clipboard-list"></i> Rekap Presensi</a>
        <a href="materi.php" class="<?=get_active_class('materi.php')?>"><i class="fas fa-book"></i> Materi</a>
        <a href="tugas.php" class="<?=get_active_class('tugas.php')?>"><i class="fas fa-tasks"></i> Tugas</a>
        <a href="jadwal.php" class="<?=get_active_class('jadwal.php')?>"><i class="fas fa-calendar-alt"></i> Jadwal</a>
        <a href="agenda.php" class="<?=get_active_class('agenda.php')?>"><i class="fas fa-calendar-day"></i> Informasi/Agenda</a>
        <a href="ekskul.php" class="<?=get_active_class('ekskul.php')?>"><i class="fas fa-running"></i> Ekstrakurikuler</a>

    <?php elseif ($role === 'admin'): ?>
    <a href="dashboard.php" class="<?=get_active_class('dashboard.php')?>">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a href="manajemen_user.php" class="<?=get_active_class('manajemen_user.php')?>">
        <i class="fas fa-users-cog"></i> Manajemen Pengguna
    </a>
    <a href="kelas.php" class="<?=get_active_class('kelas.php')?>">
        <i class="fas fa-chalkboard-teacher"></i> Data Kelas
    </a>
    <a href="ekskul.php" class="<?=get_active_class('ekskul.php')?>">
        <i class="fas fa-futbol"></i> Data Ekskul
    </a>
    <a href="agenda.php" class="<?=get_active_class('agenda.php')?>">
        <i class="far fa-calendar-alt"></i> Data Agenda
    </a>
    <a href="profil.php" class="<?=get_active_class('profil.php')?>">
        <i class="fas fa-user-cog"></i> Profil
    </a>

    <?php endif; ?>
    </div>
    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <div class="user-info">
        <?php if ($role === 'guru'): ?>
            <i class="fas fa-chalkboard-teacher"></i> <span>Guru</span>
        <?php elseif ($role === 'siswa'): ?>
            <i class="fas fa-user-graduate"></i> <span>Siswa</span>
        <?php endif; ?>
        </div>
    </div>
</div>
