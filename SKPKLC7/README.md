# Website SMPK Lawang

Website sekolah jenjang SMP berbasis PHP, MySQL, JavaScript, dan CSS.

## Cara Instalasi
1. Ekstrak semua file ke folder `htdocs/SKPKLC` (XAMPP).
2. Import `struktur_db.sql` ke database MySQL, buat database dengan nama `SKPKLC`.
3. Ubah konfigurasi koneksi di `includes/db.php` jika diperlukan.
4. Jalankan di browser: `http://localhost/SKPKLC`

## Struktur Folder
- `admin/` : Tampilan & fungsi admin
- `guru/` : Tampilan & fungsi guru
- `siswa/` : Tampilan & fungsi siswa
- `assets/` : CSS, JS, gambar
- `includes/` : Koneksi, session, fungsi umum
- `uploads/` : File upload

## Akun Default
- Admin: `admin` / `admin123` (setelah insert manual di DB)

## Fitur
- Sistem login multi-user
- Dashboard & sidebar role-based
- Upload materi, tugas, nilai, jadwal, agenda
- Profil user
- Manajemen user (admin)
