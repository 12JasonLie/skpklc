<?php
require_once '../includes/session.php';
require_once '../includes/db.php'; // Make sure DB connection is available
check_login('siswa');

$id_kelas = $_SESSION['id_kelas'];
$id_siswa = $_SESSION['user_id'];

// Latest 2 Tugas
$tugas = $conn->query("SELECT t.judul, u.nama_lengkap as guru, t.tanggal_upload FROM tugas t LEFT JOIN users u ON t.id_guru=u.id WHERE (t.id_kelas='$id_kelas' OR t.untuk_semua_kelas=1) ORDER BY t.tanggal_upload DESC LIMIT 2");
// Latest 2 Materi
$materi = $conn->query("SELECT m.judul, u.nama_lengkap as guru, m.tanggal_upload FROM materi m LEFT JOIN users u ON m.id_guru=u.id WHERE (m.id_kelas='$id_kelas' OR m.untuk_semua_kelas=1) ORDER BY m.tanggal_upload DESC LIMIT 2");
// Latest 2 Agenda
$agenda = $conn->query("SELECT a.judul, u.nama_lengkap as guru, a.tanggal_upload FROM agenda a LEFT JOIN users u ON a.id_guru=u.id WHERE (a.id_kelas='$id_kelas' OR a.untuk_semua_kelas=1) ORDER BY a.tanggal_upload DESC LIMIT 2");
// Latest 2 Absensi Ekskul (per student)
$absensi = $conn->query("SELECT e.nama, a.status, a.tanggal, u.nama_lengkap as guru FROM absensi_ekskul a LEFT JOIN ekskul e ON a.id_ekskul=e.id LEFT JOIN users u ON a.id_guru=u.id WHERE a.id_siswa='$id_siswa' ORDER BY a.tanggal DESC LIMIT 2");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Siswa Dashboard - SMPK Lawang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Mobile menu toggle */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1000;
            background: var(--biru-tua);
            color: white;
            border: none;
            font-size: 24px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        /* Responsive sidebar */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 900;
                height: 100%;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main {
                margin-left: 0;
                padding: 70px 15px 20px;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .dashboard-welcome {
                margin: 0.5em 0 0.3em;
                font-size: 1.5rem;
                text-align: center;
            }
            
            .dashboard-class {
                margin: 0 0 1em;
                text-align: center;
                font-size: 1rem;
            }
            
            .dashboard-boxes {
                flex-direction: column;
                margin: 1em 0;
                gap: 1em;
            }
            
            .dashboard-box {
                width: 100%;
                max-width: 100%;
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
<button class="menu-toggle" id="menuToggle">
    <i class="fas fa-bars"></i>
</button>
<?php 
$current_page = basename($_SERVER['PHP_SELF']);
require_once '../includes/sidebar.php';
?>
<script>
    // Toggle sidebar on mobile
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });
    
    // Close sidebar when clicking outside
    document.addEventListener('click', function(event) {
        const sidebar = document.querySelector('.sidebar');
        const menuToggle = document.getElementById('menuToggle');
        if (window.innerWidth <= 768 && !sidebar.contains(event.target) && event.target !== menuToggle) {
            sidebar.classList.remove('active');
        }
    });
    
    // Close sidebar when a link is clicked on mobile
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                document.querySelector('.sidebar').classList.remove('active');
            }
        });
    });
</script>
<style>
/* Dashboard Layout */
.dashboard-welcome {
    font-size: 1.8rem;
    margin: 1rem 0 0.5rem;
    color: var(--biru-tua);
    font-weight: 600;
    text-align: center;
}

.dashboard-class {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 1.5rem;
    text-align: center;
}

.dashboard-boxes {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin: 1.5rem 0;
    padding: 0 1rem;
}

.dashboard-box {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 1.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #eaeaea;
    display: flex;
    flex-direction: column;
}

.dashboard-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.dashboard-box h3 {
    font-size: 1.2rem;
    margin: 0 0 1.2rem 0;
    color: var(--biru-tua);
    font-weight: 600;
    padding-bottom: 0.8rem;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dashboard-box h3 i {
    color: var(--biru-muda);
}

.dashboard-entry {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f0f0f0;
    line-height: 1.5;
}

.dashboard-entry:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.dashboard-entry b {
    color: #333;
    font-weight: 600;
    display: block;
    margin-bottom: 0.3rem;
}

.dashboard-entry span {
    display: block;
    font-size: 0.9rem;
    color: #666;
    margin-top: 0.2rem;
}

.dashboard-entry span i {
    margin-right: 0.5rem;
    color: var(--biru-muda);
    width: 16px;
    text-align: center;
}

.dashboard-fallback {
    color: #888;
    font-style: italic;
    padding: 1rem 0;
    text-align: center;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .dashboard-boxes {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }
}

@media (max-width: 768px) {
    .dashboard-welcome {
        font-size: 1.6rem;
        margin-top: 1.5rem;
    }
    
    .dashboard-boxes {
        grid-template-columns: 1fr;
        gap: 1.2rem;
        padding: 0;
    }
    
    .dashboard-box {
        padding: 1.2rem;
    }
}

@media (max-width: 480px) {
    .dashboard-welcome {
        font-size: 1.4rem;
    }
    
    .dashboard-box h3 {
        font-size: 1.1rem;
    }
    
    .dashboard-entry {
        font-size: 0.95rem;
    }
}
</style>
<div class="main">
    <div class="dashboard-welcome">Selamat datang, <?=htmlspecialchars($_SESSION['nama_lengkap'])?>!</div>
    
    <div class="dashboard-boxes">
        <!-- Tugas Terbaru -->
        <div class="dashboard-box">
            <h3><i class="fas fa-tasks"></i> Tugas Terbaru</h3>
            <?php if ($tugas && $tugas->num_rows > 0): ?>
                <?php while($row = $tugas->fetch_assoc()): ?>
                    <div class="dashboard-entry">
                        <b><?=htmlspecialchars($row['judul'])?></b>
                        <span><i class="fas fa-user-tie"></i> <?=htmlspecialchars($row['guru'])?></span>
                        <span><i class="far fa-calendar-alt"></i> <?=htmlspecialchars(date('d/m/Y', strtotime($row['tanggal_upload'])))?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="dashboard-fallback">Tidak ada tugas terbaru</div>
            <?php endif; ?>
        </div>
        
        <!-- Materi Terbaru -->
        <div class="dashboard-box">
            <h3><i class="fas fa-book"></i> Materi Terbaru</h3>
            <?php if ($materi && $materi->num_rows > 0): ?>
                <?php while($row = $materi->fetch_assoc()): ?>
                    <div class="dashboard-entry">
                        <b><?=htmlspecialchars($row['judul'])?></b>
                        <span><i class="fas fa-user-tie"></i> <?=htmlspecialchars($row['guru'])?></span>
                        <span><i class="far fa-calendar-alt"></i> <?=htmlspecialchars(date('d/m/Y', strtotime($row['tanggal_upload'])))?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="dashboard-fallback">Tidak ada materi terbaru</div>
            <?php endif; ?>
        </div>
        
        <!-- Agenda Terbaru -->
        <div class="dashboard-box">
            <h3><i class="far fa-calendar-alt"></i> Agenda Terbaru</h3>
            <?php if ($agenda && $agenda->num_rows > 0): ?>
                <?php while($row = $agenda->fetch_assoc()): ?>
                    <div class="dashboard-entry">
                        <b><?=htmlspecialchars($row['judul'])?></b>
                        <span><i class="fas fa-user-tie"></i> <?=htmlspecialchars($row['guru'])?></span>
                        <span><i class="far fa-calendar-alt"></i> <?=htmlspecialchars(date('d/m/Y', strtotime($row['tanggal_upload'])))?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="dashboard-fallback">Tidak ada agenda terbaru</div>
            <?php endif; ?>
        </div>
        
        <!-- Presensi Ekskul Terbaru -->
        <div class="dashboard-box">
            <h3><i class="fas fa-futbol"></i> Presensi Ekskul</h3>
            <?php if ($absensi && $absensi->num_rows > 0): ?>
                <?php while($row = $absensi->fetch_assoc()): ?>
                    <div class="dashboard-entry">
                        <b><?=htmlspecialchars($row['nama'])?></b>
                        <span>
                            <i class="fas <?php 
                                echo $row['status'] === 'Hadir' ? 'fa-check-circle text-success' : 
                                    ($row['status'] === 'Izin' ? 'fa-info-circle text-warning' : 'fa-times-circle text-danger');
                            ?>"></i> 
                            <?=htmlspecialchars($row['status'])?>
                        </span>
                        <span><i class="far fa-calendar-alt"></i> <?=htmlspecialchars(date('d/m/Y', strtotime($row['tanggal'])))?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="dashboard-fallback">Tidak ada riwayat presensi ekskul</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
