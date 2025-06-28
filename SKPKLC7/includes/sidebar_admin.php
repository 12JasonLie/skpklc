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
        <a href="manajemen_user.php" class="<?php echo get_active_class($current_page, 'manajemen_user.php'); ?>">
            <i class="fas fa-users-cog"></i> Manajemen Pengguna
        </a>
        <a href="kelas.php" class="<?php echo get_active_class($current_page, 'kelas.php'); ?>">
            <i class="fas fa-chalkboard-teacher"></i> Data Kelas
        </a>
        <a href="ekskul.php" class="<?php echo get_active_class($current_page, 'ekskul.php'); ?>">
            <i class="fas fa-futbol"></i> Data Ekskul
        </a>
        <a href="agenda.php" class="<?php echo get_active_class($current_page, 'agenda.php'); ?>">
            <i class="far fa-calendar-alt"></i> Data Agenda
        </a>
        <a href="profil.php" class="<?php echo get_active_class($current_page, 'profil.php'); ?>">
            <i class="fas fa-user-cog"></i> Profil
        </a>
    </div>
    
    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <div class="user-info">
            <i class="fas fa-user-shield"></i>
            <span>Admin</span>
        </div>
    </div>
</div>
