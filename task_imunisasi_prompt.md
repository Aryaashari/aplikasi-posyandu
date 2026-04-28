# Task: Pembuatan Modul Tracking Imunisasi Anak

## 📌 Deskripsi Tugas
Tugas ini memiliki tujuan untuk membuat modul **Imunisasi** di dalam sistem Posyandu. Fitur ini krusial untuk melacak riwayat vaksinasi setiap balita guna memastikan mereka mendapatkan Imunisasi Dasar Lengkap (IDL) tepat waktu sesuai standar Kemenkes/WHO.

Sistem ini bersifat berkelanjutan dengan modul sebelumnya (KMS Digital), sehingga akan ditambahkan ke tabulasi pelacakan profil anak.

Tech Stack: **Laravel, Svelte 5, Inertia.js, Tailwind CSS**.

## 🎯 Tujuan Utama
1. **Pencatatan Checklist**: Memungkinkan kader untuk "menceklis" atau menandai jenis imunisasi yang sudah diberikan kepada anak beserta tanggal pemberiannya.
2. **Pengingat Jadwal**: Sistem dapat mengkalkulasi jadwal imunisasi lanjutan berdasarkan umur anak.
3. **Indikator Kelengkapan**: Indikator persentase atau status mutlak apakah anak sudah mendapatkan Imunisasi Lengkap atau Belum.

---

## 📋 Detail Spesifikasi

### 1. Struktur Database (Backend)
Dibutuhkan penyesuaian pada tabel bawaan atau penambahan Master Enum/Reference:
*   **Katalog Vaksin (Master / Array Config)**: Daftarkan standar imunisasi wajib seperti:
    *   Hepatitis B0 (Usia 0 bulan)
    *   BCG & Polio 1 (Usia 1 bulan)
    *   DPT-HB-Hib 1 & Polio 2 (Usia 2 bulan)
    *   Dst hingga Campak/Rubella (Usia 9 bulan).
*   **Tabel `trx_imunisasi`**: 
    *   Harus memiliki `id_anak`, `jenis_vaksin` (string/FK), `tanggal_pemberian` (date), dan `keterangan` (text).

### 2. Antarmuka UI (Svelte 5 Frontend)
*   **Integrasi KMS Digital (`Anak/Show.svelte`)**:
    *   Tambahkan Tab **"Imunisasi"** di menu KMS anak (bersandingan dengan Tab Riwayat Pengukuran & Tab Riwayat Kehadiran).
*   **Checklist Vaksin**: 
    *   Tampilkan daftar lengkap vaksin wajib yang diurutkan berdasar Usia Anjuran. 
    *   Gunakan desain UI **Timeline** atau **Card Checklist**. Jika sudah diberikan, tampilkan ✅ Ikon Centang Hijau beserta tanggalnya. Jika belum, tampilkan tombol "Beri Vaksin" (mengirim request `router.post(...)`).
*   **Jadwal Berikutnya (Next Schedule Alert)**:
    *   Tambahkan peringatan (Alert Card) berwarna kuning atau biru di atas *checklist* yang menampilkan *"Vaksin yang harus diberikan selanjutnya: [Nama Vaksin] (Keterlambatan: 2 Bulan)"* — kalkulasikan dari usia anak hari ini dikurangi batas usia vaksin.
*   **Status Imunisasi Dasar Lengkap (IDL)**:
    *   Progress bar (0% hingga 100%) berdasarkan jumlah vaksin dasar yang sudah dicentang. Tampilkan badge besar: "BELUM LENGKAP" atau "LENGKAP".

### 3. Logika Controller (`ImunisasiController`)
*   **Method `store`**: Untuk menerima *submit checklist* dari kader secara *hybrid* (jangan *reload page* utuh, simpan *state* Ineria secara asinkron atau responsif).
*   **Method perhitungan `getSchedules`**: Menyusun array data *view model* untuk di-*consume* oleh Svelte *frontend*, mengecek *intersect* mana yang sudah divaksin mana yang *pending*.

---

## 📤 Output yang Diharapkan (Deliverables)
1. **Migration & Model**: Validasi tabel `trx_imunisasi` di database.
2. **Tab Imunisasi Svelte**: Integrasi UI yang responsif dan sangat *user-friendly* berbentuk *checklist* yang *satisfying* saat di-klik oleh Kader.
3. **Jadwal & Indikator Lengkap**: Perhitungan *Next Schedule* dan *Progress Kelengkapan* wajib berjalan tanpa masalah di profil masing-masing anak.

---
*Silakan jalankan tugas ini dengan pendekatan Atomic Design menggunakan komponen Svelte yang ada secara interaktif!*
