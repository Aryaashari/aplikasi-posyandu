# Task: Pembuatan Modul Kehadiran (Attendance) Posyandu

## 📌 Deskripsi Tugas
Tugas ini bertujuan untuk membuat modul **Kehadiran** guna mencatat (tracking) kedatangan anak/balita di kegiatan posyandu. Fitur ini akan berfungsi sebagai buku tamu digital yang terintegrasi, yang nantinya sangat penting untuk validasi data laporan bulanan, memastikan anak mana saja yang aktif datang.

Aplikasi ini beroperasi menggunakan stack **Laravel, Svelte 5, Inertia.js**, dan **Tailwind CSS**.

## 🎯 Tujuan Utama
1. **Fitur Check-In**: Memungkinkan kader untuk melakukan presensi (check-in) anak dengan cepat pada setiap sesi kegiatan posyandu.
2. **Rekap Bulanan**: Menyediakan antarmuka tabel rekapitulasi kehadiran berdasarkan bulan dan tahun, memudahkan evaluasi keaktifan balita.

---

## 📋 Detail Spesifikasi

### 1. Struktur Database (Backend)
Pastikan model dan migrasi yang digunakan mendukung pencatatan kehadiran.
*   **Tabel `trx_kehadiran`**: Minimal harus memiliki:
    *   `id_anak` (Foreign Key ke tabel `md_anak`)
    *   `id_jadwal` atau `tanggal_kegiatan` (Untuk melacak kegiatan mana anak tersebut hadir)
    *   `status` (Enum/String: 'Hadir', 'Izin', 'Sakit', 'Tanpa Keterangan')
    *   `waktu_checkin` (Timestamp atau Time)

### 2. Fitur Check-In Harian
*   **Antarmuka (UI)**: Buat halaman `resources/js/Pages/Kehadiran/CheckIn.svelte`.
*   **Search & Select**: Tambahkan fitur pencarian (search bar) berdasarkan Nama Anak atau NIK agar kader bisa cepat menemukan nama anak.
*   **Aksi Cepat (Quick Action)**: Setelah nama anak ditemukan, sediakan tombol cepat "Hadir" (warna hijau) yang ketika diklik akan langsung mengirim request AJAX (melalui Inertia `router.post`) tanpa harus membuka form yang rumit.
*   **Indikator Visual**: Jika anak sudah di-check-in pada hari tersebut, tandai namanya dengan *badge* "Sudah Hadir" agar tidak diklik dua kali (mencegah duplikasi data).

### 3. Fitur Rekapitulasi Bulanan
*   **Filter Waktu**: Pada halaman `resources/js/Pages/Kehadiran/Index.svelte` atau `Rekap.svelte`, sediakan dropdown / filter untuk memilih **Bulan** dan **Tahun**.
*   **Grid/Table View**: Tampilkan tabel rekap di mana baris (row) adalah nama-nama anak, dan kolom (column) adalah status kehadirannya di bulan tersebut.
*   *Bonus (Opsional)*: Tambahkan ringkasan metrik di atas tabel (contoh: "Total Hadir: 45 Anak", "Tingkat Partisipasi: 80%").

### 4. Standar UI/UX (Atomic Design)
*   Gunakan komponen standar yang sudah ada (seperti `Card.svelte`, `Button.svelte`, `Input.svelte`) di folder `resources/js/Components/UI/`.
*   Pastikan tabel rekapitulasi *responsive* (bisa digeser ke samping/scroll) karena tabel bulanan berpotensi lebar jika diakses dari layar handphone kader.
*   Gunakan sintaks Svelte 5 (`$state`, `$derived`, fungsi event interaktif).

---

## 📤 Output yang Diharapkan (Deliverables)
1. **Controller & Routes (`KehadiranController`)**: Mengatur proses API check-in dan fetch data rekap bulanan.
2. **Halaman Check-In Svelte**: Tampilan presensi yang cepat dan ringan.
3. **Pembaruan Sidebar**: Menambahkan menu "Kehadiran" pada komponen sidebar yang mengarah ke modul ini.
4. Kode harus mematuhi standar Inertia.js untuk pengiriman state management.

---
*Silakan jalankan tugas ini bertahap: mulai dari validasi migrasi kehadiran, pembuatan fungsi check-in yang responsif, lalu laporan rekapnya.*
