# SKPK LC
SKPK Learning Center:
The newest LMS for Pelita Kasih Lawang Christian Junior High School

## Cara Instalasi
1. Ekstrak semua file ke folder `htdocs/SKPKLC` (XAMPP) atau menyesuaikan di server masing-masing.
2. Buka file `inludes/db.php` dan edit sesuai kebutuhan server masing-masing
3. Import `struktur_db.sql` ke database MySQL, kalau tidak mengubah line 6 dari db.php nama databasenya adalah `SKPKLC`.
4. Jalankan di browser sesuai dengan konfigurasi server. Kalau mengunakan XAMPP: `http://localhost/SKPKLC`

## Struktur Folder
- `admin/` : Tampilan & fungsi admin
- `guru/` : Tampilan & fungsi guru
- `siswa/` : Tampilan & fungsi siswa
- `assets/` : CSS, JS, gambar
- `includes/` : Koneksi, session, fungsi umum dan beberapa tambahan fitur display
- `uploads/` : File upload

## Akun Default
Mohon maaf saat ini tidak ada akun default.
Untuk setup akun pertama (admin pertama) bisa mengikuti langkah-langkah berikut:
1. Buka `admin/manajemen_user.php` di text editor/VS
2. Comment atau delete check_login('admin');
3. Buka `admin/manajemen_user.php` di browser dan buat akun pertama pastikan peran yang dipilih adalah admin
4. Ketika sudah ada akunya uncomment atau masukkan (kalau delete): check_login('admin'); di line 5

Tidak disarankan masukkan akun pertama ini langsung di phpmyadmin semacamnya karena ada algoritma hashing password.

## Fitur
- Sistem login multi-user
- Dashboard & sidebar role-based
- Upload materi, tugas, nilai, jadwal, agenda
- Profil user
- Manajemen user (admin)
- Mobile and desktop friendly

## Ada saran/komentar/kritik?
Masukkan saran/komentar/kritik anda ke:
`https://forms.gle/yAvC5zSZSbVgaaYZ9`
