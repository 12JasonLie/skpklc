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
        <a href="presensi_harian.php" class="<?php echo get_active_class($current_page, 'presensi_harian.php'); ?>">
            <i class="fas fa-calendar-check"></i> Presensi Harian
        </a>
        <a href="absensi_ekskul.php" class="<?php echo get_active_class($current_page, 'absensi_ekskul.php'); ?>">
            <i class="fas fa-futbol"></i> Presensi Ekskul
        </a>
        <a href="rekap_presensi.php" class="<?php echo get_active_class($current_page, 'rekap_presensi.php'); ?>">
            <i class="fas fa-clipboard-list"></i> Rekap Presensi
        </a>
        <a href="materi.php" class="<?php echo get_active_class($current_page, 'materi.php'); ?>">
            <i class="fas fa-book"></i> Materi
        </a>
        <a href="tugas.php" class="<?php echo get_active_class($current_page, 'tugas.php'); ?>">
            <i class="fas fa-tasks"></i> Tugas
        </a>
        <a href="jadwal.php" class="<?php echo get_active_class($current_page, 'jadwal.php'); ?>">
            <i class="fas fa-calendar-alt"></i> Jadwal
        </a>
        <a href="agenda.php" class="<?php echo get_active_class($current_page, 'agenda.php'); ?>">
            <i class="fas fa-calendar-day"></i> Informasi/Agenda
        </a>
        <a href="ekskul.php" class="<?php echo get_active_class($current_page, 'ekskul.php'); ?>">
            <i class="fas fa-running"></i> Ekstrakurikuler
        </a>
    </div>
    
    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <div class="user-info">
            <i class="fas fa-user-graduate"></i>
            <span>Siswa</span>
        </div>
    </div>
</div>
