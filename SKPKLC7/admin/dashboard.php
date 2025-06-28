<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'admin' role
check_login('admin');

$page_title = 'Admin Dashboard';
$current_page = basename($_SERVER['PHP_SELF']);

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main">
    <div class="dashboard-welcome">Selamat Datang, <?=htmlspecialchars($_SESSION['nama_lengkap'])?></div>
    <div class="dashboard-role">Anda login sebagai <strong>Admin</strong></div>
    
    <!-- Quick Links -->
    <div class="dashboard-quicklinks">
        <a href="manajemen_user.php"><i class="fas fa-users-cog"></i> Kelola Pengguna</a>
        <a href="kelas.php"><i class="fas fa-chalkboard-teacher"></i> Data Kelas</a>
        <a href="ekskul.php"><i class="fas fa-futbol"></i> Data Ekskul</a>
        <a href="agenda.php"><i class="far fa-calendar-alt"></i> Data Agenda</a>
    </div>
    
    <div class="dashboard-boxes">
        <?php
        $jml_guru = $conn->query("SELECT COUNT(*) FROM users WHERE role='guru'")->fetch_row()[0];
        $jml_siswa = $conn->query("SELECT COUNT(*) FROM users WHERE role='siswa'")->fetch_row()[0];
        $jml_kelas = $conn->query("SELECT COUNT(*) FROM kelas")->fetch_row()[0];
        $jml_ekskul = $conn->query("SELECT COUNT(*) FROM ekskul")->fetch_row()[0];
        ?>
        
        <div class="dashboard-box">
            <h3><i class="fas fa-chalkboard-teacher"></i> Jumlah Guru</h3>
            <div class="big"><?=$jml_guru?></div>
        </div>
        
        <div class="dashboard-box">
            <h3><i class="fas fa-user-graduate"></i> Jumlah Siswa</h3>
            <div class="big"><?=$jml_siswa?></div>
        </div>
        
        <div class="dashboard-box">
            <h3><i class="fas fa-school"></i> Jumlah Kelas</h3>
            <div class="big"><?=$jml_kelas?></div>
        </div>
        
        <div class="dashboard-box">
            <h3><i class="fas fa-futbol"></i> Jumlah Ekskul</h3>
            <div class="big"><?=$jml_ekskul?></div>
        </div>
    </div>
    
    <style>
    .dashboard-welcome {
        font-size: 1.8rem;
        margin: 1rem 0 0.5rem;
        color: var(--biru-tua);
        font-weight: 600;
    }
    
    .dashboard-role {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 1.5rem;
    }
    
    .dashboard-boxes {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        margin: 1.5rem 0;
    }
    
    .dashboard-box {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #eaeaea;
        text-align: center;
    }
    
    .dashboard-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }
    
    .dashboard-box h3 {
        font-size: 1.1rem;
        margin: 0 0 1rem 0;
        color: var(--biru-tua);
        font-weight: 600;
        padding-bottom: 0.8rem;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .dashboard-box .big {
        font-size: 2.2rem;
        font-weight: bold;
        color: var(--biru-muda);
        margin: 0.5rem 0;
    }
    
    .dashboard-quicklinks {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        margin: 0 0 1.5rem 0;
    }
    
    .dashboard-quicklinks a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--biru-muda);
        color: #fff;
        padding: 0.6rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    
    .dashboard-quicklinks a:hover {
        background: var(--biru-tua);
        transform: translateY(-1px);
    }
    
    @media (max-width: 768px) {
        .dashboard-welcome {
            font-size: 1.5rem;
            margin-top: 1rem;
        }
        
        .dashboard-role {
            font-size: 1rem;
        }
        
        .dashboard-boxes {
            grid-template-columns: 1fr 1fr;
        }
        
        .dashboard-quicklinks {
            flex-direction: column;
        }
        
        .dashboard-box {
            margin-bottom: 0;
        }
    }
    
    @media (max-width: 480px) {
        .dashboard-boxes {
            grid-template-columns: 1fr;
        }
    }
    </style>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>


</body>
</html>
