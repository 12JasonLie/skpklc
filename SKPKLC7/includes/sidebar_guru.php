<?php
function get_active_class($current_page, $page_to_check) {
    $current_file = basename($_SERVER['PHP_SELF']);
    return ($current_file === $page_to_check) ? 'active' : '';
}
?>
<div class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/logo.png" alt="Logo" class="sidebar-logo">
        <div class="logo">SMPK Lawang</div>
    </div>
    
    <div class="sidebar-menu">
        <a href="dashboard.php" class="<?php echo get_active_class($current_page, 'dashboard.php'); ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="profil.php" class="<?php echo get_active_class($current_page, 'profil.php'); ?>">
            <i class="fas fa-user"></i> Profil
        </a>
        <a href="absensi_harian.php" class="<?php echo get_active_class($current_page, 'absensi_harian.php'); ?>">
            <i class="fas fa-clipboard-check"></i> Absensi Harian
        </a>
        <a href="materi.php" class="<?php echo get_active_class($current_page, 'materi.php'); ?>">
            <i class="fas fa-book"></i> Upload Materi
        </a>
        <a href="tugas.php" class="<?php echo get_active_class($current_page, 'tugas.php'); ?>">
            <i class="fas fa-tasks"></i> Upload Tugas
        </a>
        <a href="nilai.php" class="<?php echo get_active_class($current_page, 'nilai.php'); ?>">
            <i class="fas fa-chart-line"></i> Upload Nilai
        </a>
        <a href="jadwal.php" class="<?php echo get_active_class($current_page, 'jadwal.php'); ?>">
            <i class="far fa-calendar-alt"></i> Upload Jadwal
        </a>
        <a href="agenda.php" class="<?php echo get_active_class($current_page, 'agenda.php'); ?>">
            <i class="far fa-calendar-plus"></i> Upload Agenda
        </a>
        <a href="ekskul.php" class="<?php echo get_active_class($current_page, 'ekskul.php'); ?>">
            <i class="fas fa-futbol"></i> Ekstrakurikuler
        </a>
        <a href="absensi_ekskul.php" class="<?php echo get_active_class($current_page, 'absensi_ekskul.php'); ?>">
            <i class="fas fa-clipboard-list"></i> Absensi Ekskul
        </a>
        <a href="rekap_absensi_harian.php" class="<?php echo get_active_class($current_page, 'rekap_absensi_harian.php'); ?>">
            <i class="fas fa-file-alt"></i> Rekap Absensi
        </a>
    </div>
    
    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <div class="user-info">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Guru</span>
        </div>
    </div>
</div>
