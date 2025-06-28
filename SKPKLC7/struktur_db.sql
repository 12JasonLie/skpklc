-- Struktur Database SMPK Lawang

CREATE TABLE kelas (
    id_kelas INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(10) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin','guru','siswa') NOT NULL,
    id_kelas INT DEFAULT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas)
);

CREATE TABLE materi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    file VARCHAR(255),
    id_guru INT,
    id_kelas INT,
    mapel VARCHAR(50),
    tanggal_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    untuk_semua_kelas BOOLEAN DEFAULT 0,
    FOREIGN KEY (id_guru) REFERENCES users(id),
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas)
);

CREATE TABLE tugas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    file VARCHAR(255),
    tipe_pengumpulan ENUM('fisik','online') NOT NULL,
    id_guru INT,
    id_kelas INT,
    mapel VARCHAR(50),
    deadline DATETIME,
    untuk_semua_kelas BOOLEAN DEFAULT 0,
    tanggal_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_guru) REFERENCES users(id),
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas)
);

CREATE TABLE tugas_pengumpulan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tugas INT,
    id_siswa INT,
    file VARCHAR(255),
    status ENUM('belum','sudah') DEFAULT 'belum',
    nilai INT DEFAULT 0,
    tanggal_kumpul DATETIME,
    FOREIGN KEY (id_tugas) REFERENCES tugas(id),
    FOREIGN KEY (id_siswa) REFERENCES users(id)
);

CREATE TABLE nilai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_guru INT,
    id_kelas INT,
    mapel VARCHAR(50),
    file_pdf VARCHAR(255),
    tanggal_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_guru) REFERENCES users(id),
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas)
);

CREATE TABLE jadwal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_kelas INT,
    file_pdf VARCHAR(255),
    tipe ENUM('pelajaran','SUM','Rem','Peltam'),
    tanggal_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas)
);

CREATE TABLE ekskul (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jadwal_jam VARCHAR(20),
    hari VARCHAR(20),
    tempat VARCHAR(100),
    info TEXT,
    id_guru INT,
    FOREIGN KEY (id_guru) REFERENCES users(id)
);

CREATE TABLE ekskul_siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ekskul INT,
    id_siswa INT,
    FOREIGN KEY (id_ekskul) REFERENCES ekskul(id),
    FOREIGN KEY (id_siswa) REFERENCES users(id)
);

CREATE TABLE absensi_ekskul (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ekskul INT,
    id_siswa INT,
    tanggal DATE,
    status ENUM('hadir','tidak') DEFAULT 'hadir',
    id_guru INT,
    FOREIGN KEY (id_ekskul) REFERENCES ekskul(id),
    FOREIGN KEY (id_siswa) REFERENCES users(id),
    FOREIGN KEY (id_guru) REFERENCES users(id)
);

CREATE TABLE absensi_harian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_siswa INT,
    id_kelas INT,
    tanggal DATE,
    status ENUM('hadir','tidak hadir','izin','sakit') NOT NULL,
    keterangan TEXT,
    id_guru INT,
    waktu_input TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_siswa) REFERENCES users(id),
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas),
    FOREIGN KEY (id_guru) REFERENCES users(id)
);

CREATE TABLE agenda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    tanggal DATETIME,
    id_guru INT,
    id_kelas INT,
    untuk_semua_kelas BOOLEAN DEFAULT 0,
    tanggal_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_guru) REFERENCES users(id),
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas)
);
