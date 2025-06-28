<?php
// Mulai session tanpa cookie persistent
ini_set('session.cookie_lifetime', 0); // Session berakhir saat browser ditutup
ini_set('session.gc_maxlifetime', 1800); // Session timeout 30 menit
session_start();

/**
 * Check if user is logged in and has the required role
 * @param string $required_role (optional) The required role to access the page
 * @return void
 */
function check_login($required_role = null) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: /SKPKLC/index.php');
        exit();
    }
    
    // If role is specified, check if user has the required role
    if ($required_role !== null && (!isset($_SESSION['role']) || $_SESSION['role'] !== $required_role)) {
        header('Location: /SKPKLC/index.php');
        exit();
    }
}
?>
