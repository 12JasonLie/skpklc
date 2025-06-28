<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
// Check if user is logged in and has 'guru' role
check_login('guru');

$id_guru = $_SESSION['user_id'];

// Latest 2 Tugas uploaded by this teacher
$tugas = $conn->query("SELECT t.judul, k.nama_kelas, t.tanggal_upload FROM tugas t LEFT JOIN kelas k ON t.id_kelas=k.id_kelas WHERE t.id_guru='$id_guru' ORDER BY t.tanggal_upload DESC LIMIT 2");
// Latest 2 Materi uploaded by this teacher
$materi = $conn->query("SELECT m.judul, k.nama_kelas, m.tanggal_upload FROM materi m LEFT JOIN kelas k ON m.id_kelas=k.id_kelas WHERE m.id_guru='$id_guru' ORDER BY m.tanggal_upload DESC LIMIT 2");
// Latest 2 Agenda created by this teacher
$agenda = $conn->query("SELECT a.judul, k.nama_kelas, a.tanggal_upload FROM agenda a LEFT JOIN kelas k ON a.id_kelas=k.id_kelas WHERE a.id_guru='$id_guru' ORDER BY a.tanggal_upload DESC LIMIT 2");
// Latest 2 Presensi Harian recorded by this teacher with student names and class
$presensi = $conn->query("SELECT u.nama_lengkap as siswa_nama, k.nama_kelas, a.status, a.tanggal, a.keterangan 
                        FROM absensi_harian a 
                        LEFT JOIN users u ON a.id_siswa=u.id 
                        LEFT JOIN kelas k ON a.id_kelas=k.id_kelas 
                        WHERE a.id_guru='$id_guru' 
                        ORDER BY a.tanggal DESC, u.nama_lengkap 
                        LIMIT 2");
?>
<?php
$page_title = 'Dashboard Guru';
$current_page = basename($_SERVER['PHP_SELF']);

// Include header and sidebar
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<style>
/* Dashboard Layout */
.dashboard-welcome {
    font-size: 1.8rem;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.dashboard-role {
    color: #6c757d;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.dashboard-quicklinks {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    margin-bottom: 2rem;
}

.dashboard-quicklinks a {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--biru-muda);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dashboard-quicklinks a:hover {
    background: var(--biru-tua);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.dashboard-boxes {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.dashboard-box {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 1.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.dashboard-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
}

.dashboard-box h3 {
    font-size: 1.2rem;
    margin: 0 0 1.2rem 0;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding-bottom: 0.8rem;
    border-bottom: 1px solid #f0f0f0;
}

.dashboard-box h3 i {
    color: var(--biru-muda);
}

.dashboard-entry {
    padding: 0.9rem 0;
    border-bottom: 1px solid #f5f5f5;
    transition: background 0.2s;
}

.dashboard-entry:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.dashboard-entry:hover {
    background: #fafafa;
    margin: 0 -1rem;
    padding: 0.9rem 1rem;
    border-radius: 8px;
}

.dashboard-fallback {
    color: #6c757d;
    font-style: italic;
    padding: 1rem 0;
    text-align: center;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .dashboard-boxes {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 992px) {
    .dashboard-boxes {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    }
}

@media (max-width: 768px) {
    .dashboard-welcome {
        font-size: 1.6rem;
    }
    
    .dashboard-role {
        font-size: 1rem;
    }
    
    .dashboard-quicklinks {
        gap: 0.6rem;
    }
    
    .dashboard-quicklinks a {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .dashboard-boxes {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .dashboard-welcome {
        font-size: 1.4rem;
    }
    
    .dashboard-quicklinks {
        flex-direction: column;
    }
    
    .dashboard-quicklinks a {
        width: 100%;
        justify-content: center;
    }
}
</style>
<div class="main">
    <div class="card card-section" style="margin-bottom: 2rem;">
        <h2 class="section-title"><i class="fas fa-user-circle"></i> Dashboard Guru</h2>
        <div class="dashboard-welcome">
            <i class="fas fa-user-circle"></i> Selamat datang, <?=htmlspecialchars($_SESSION['nama_lengkap'])?>
        </div>
        <div class="dashboard-role">
            <i class="fas fa-chalkboard-teacher"></i> Anda login sebagai <span class="text-primary">Guru</span>
        </div>
    </div>
    
    <div class="dashboard-quicklinks">
        <a href="tugas.php">
            <i class="fas fa-tasks"></i> Tambah Tugas
        </a>
        <a href="materi.php">
            <i class="fas fa-book"></i> Tambah Materi
        </a>
        <a href="agenda.php">
            <i class="fas fa-calendar-alt"></i> Tambah Agenda
        </a>
        <a href="absensi_ekskul.php">
            <i class="fas fa-clipboard-check"></i> Absensi Ekskul
        </a>
        <a href="nilai.php">
            <i class="fas fa-star"></i> Input Nilai
        </a>
    </div>
    <div class="dashboard-boxes card card-section" style="margin-bottom: 2rem;">
        <!-- Tugas Terbaru -->
        <div class="dashboard-box">
            <h3 class="section-title"><i class="fas fa-tasks"></i> Tugas Terbaru</h3>
            <?php if ($tugas && $tugas->num_rows > 0): ?>
                <?php while($row = $tugas->fetch_assoc()): ?>
                    <div class="dashboard-entry">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="fw-semibold text-truncate" style="max-width: 80%;">
                                <?=htmlspecialchars($row['judul'])?>
                            </div>
                            <div class="badge bg-light text-dark">
                                <?=date('d M', strtotime($row['tanggal_upload']))?>
                            </div>
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="fas fa-users me-1"></i> <?=htmlspecialchars($row['nama_kelas'])?>
                        </div>
                    </div>
                <?php endwhile; ?>
                <div class="text-end mt-2">
                    <a href="tugas.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
            <?php else: ?>
                <div class="dashboard-fallback">
                    <i class="fas fa-inbox"></i><br>
                    Belum ada tugas terbaru
                </div>
            <?php endif; ?>
        </div>
        <!-- Materi Terbaru -->
        <div class="dashboard-box">
            <h3 class="section-title"><i class="fas fa-book"></i> Materi Terbaru</h3>
            <?php if ($materi && $materi->num_rows > 0): ?>
                <?php while($row = $materi->fetch_assoc()): ?>
                    <div class="dashboard-entry">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="fw-semibold text-truncate" style="max-width: 80%;">
                                <?=htmlspecialchars($row['judul'])?>
                            </div>
                            <div class="badge bg-light text-dark">
                                <?=date('d M', strtotime($row['tanggal_upload']))?>
                            </div>
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="fas fa-users me-1"></i> <?=htmlspecialchars($row['nama_kelas'])?>
                        </div>
                    </div>
                <?php endwhile; ?>
                <div class="text-end mt-2">
                    <a href="materi.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
            <?php else: ?>
                <div class="dashboard-fallback">
                    <i class="fas fa-inbox"></i><br>
                    Belum ada materi terbaru
                </div>
            <?php endif; ?>
        </div>
        <!-- Agenda Terbaru -->
        <div class="dashboard-box">
            <h3 class="section-title"><i class="fas fa-calendar-alt"></i> Agenda Terbaru</h3>
            <?php if ($agenda && $agenda->num_rows > 0): ?>
                <?php while($row = $agenda->fetch_assoc()): ?>
                    <div class="dashboard-entry">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="fw-semibold text-truncate" style="max-width: 80%;">
                                <?=htmlspecialchars($row['judul'])?>
                            </div>
                            <div class="badge bg-light text-dark">
                                <?=date('d M', strtotime($row['tanggal_upload']))?>
                            </div>
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="fas fa-users me-1"></i> <?=htmlspecialchars($row['nama_kelas'])?>
                        </div>
                    </div>
                <?php endwhile; ?>
                <div class="text-end mt-2">
                    <a href="agenda.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
            <?php else: ?>
                <div class="dashboard-fallback">
                    <i class="fas fa-inbox"></i><br>
                    Belum ada agenda terbaru
                </div>
            <?php endif; ?>
        </div>
        <!-- Presensi Harian Terbaru -->
        <div class="dashboard-box">
            <h3 class="section-title"><i class="fas fa-clipboard-list"></i> Presensi Terbaru</h3>
            <?php if ($presensi && $presensi->num_rows > 0): ?>
                <?php 
                while($row = $presensi->fetch_assoc()): 
                    $status_class = '';
                    $status_icon = '';
                    switch(strtolower($row['status'])) {
                        case 'hadir': 
                            $status_class = 'bg-success bg-opacity-10 text-success';
                            $status_icon = 'fa-check-circle';
                            break;
                        case 'tidak hadir': 
                            $status_class = 'bg-danger bg-opacity-10 text-danger';
                            $status_icon = 'fa-times-circle';
                            break;
                        case 'izin': 
                            $status_class = 'bg-warning bg-opacity-10 text-warning';
                            $status_icon = 'fa-info-circle';
                            break;
                        case 'sakit': 
                            $status_class = 'bg-info bg-opacity-10 text-info';
                            $status_icon = 'fa-procedures';
                            break;
                        default: 
                            $status_class = 'bg-secondary bg-opacity-10 text-secondary';
                            $status_icon = 'fa-question-circle';
                    }
                ?>
                    <div class="dashboard-entry">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="fw-semibold text-truncate" style="max-width: 70%;">
                                <?=htmlspecialchars($row['siswa_nama'])?>
                            </div>
                            <span class="badge <?=$status_class?> px-2 py-1">
                                <i class="fas <?=$status_icon?> me-1"></i>
                                <?=strtoupper(htmlspecialchars($row['status']))?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-1">
                            <span><i class="fas fa-users me-1"></i> <?=htmlspecialchars($row['nama_kelas'])?></span>
                            <span><i class="far fa-calendar-alt me-1"></i> <?=date('d M Y', strtotime($row['tanggal']))?></span>
                        </div>
                        <?php if (!empty($row['keterangan'])): ?>
                            <div class="small text-muted mt-1 text-truncate" title="<?=htmlspecialchars($row['keterangan'])?>">
                                <i class="fas fa-comment-alt me-1"></i> <?=htmlspecialchars($row['keterangan'])?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
                <div class="text-end mt-2">
                    <a href="absensi_harian.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
            <?php else: ?>
                <div class="dashboard-fallback">
                    <i class="fas fa-inbox"></i><br>
                    Belum ada presensi terbaru
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>
