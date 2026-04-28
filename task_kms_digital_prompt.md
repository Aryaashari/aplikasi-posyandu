# Task: Pembuatan Modul Grafik Pertumbuhan (KMS Digital)

## 📌 Deskripsi Tugas
Tugas ini bertujuan untuk membuat modul **Grafik Pertumbuhan (KMS Digital)** pada sistem aplikasi Posyandu. Modul ini akan berfungsi sebagai pengganti Kartu Menuju Sehat (KMS) manual yang biasa digunakan di posyandu konvensional. Modul ini diperuntukkan agar petugas kader maupun orang tua dapat melihat tren perkembangan anak secara divisualisasikan.

Aplikasi ini menggunakan stack **Laravel, Svelte 5, Inertia.js**, dan **Tailwind CSS**.

## 🎯 Tujuan Utama
1. **Visualisasi Data**: Membuat rentetan data dari riwayat penimbangan dan pengukuran yang telah diinput diubah menjadi grafik garis (line chart).
2. **KMS Digital**: Menampilkan Grafik Berat Badan menurut Umur (BB/U) dan Tinggi Badan menurut Umur (TB/U).
3. **Peringatan Dini**: Memberikan notifikasi visual (highlight) jika tren perkembangan anak masuk ke dalam kategori peringatan (seperti berat tidak naik).

---

## 📋 Detail Spesifikasi

### 1. Library dan UI/UX
*   **Charting Library**: Gunakan library pembuatan chart yang kompatibel dengan Svelte 5 (misal: `svelte-chartjs` menggunakan **Chart.js**, atau **ApexCharts**). Pastikan library mendukung *multiple lines* dan plotting koordinat (umur dalam bulan pada sumbu X, angka berat/tinggi pada sumbu Y).
*   **Atomic Design**: Lanjutkan implementasi prinsip *Atomic Design*. Jika dibutuhkan pembungkus visual baru, buat *Molecules* atau *Organisms* yang proporsional untuk me-render Chart. Letakkan di folder `resources/js/Components/Pengukuran/` (contoh: `KmsChart.svelte`).
*   **Responsivitas**: Grafik wajib responsif digunakan pada layar HP maupun Desktop.

### 2. Kebutuhan Data (Backend)
*   Buat endpoint atau sesuaikan *controller* yang ada di Laravel untuk memberikan suplai response JSON (melalui Inertia) yang menyajikan:
    *   **Riwayat Pengukuran Anak** spesifik (list berat badan dan tinggi badan yang telah diurutkan berdasarkan umur dalam bulan atau tanggal ukur).
    *   **Data Standar WHO** untuk rentang usia anak tersebut sebagai "garis batas/zona warna" pada grafik. Batas-batas seperti +3 SD, +2 SD, Median, -2 SD, dan -3 SD harus bisa diplot sebagai garis latar belakang.

### 3. Fungsionalitas & Logika Bisnis
*   **Grafik BB & TB per Umur**: 
    1.  Tampilkan grafik riwayat anak berupa garis (misal: warna biru terang) dengan node titik di setiap bulannya.
    2.  Plot garis batas pertumbuhan sesuai WHO (misal area hijau untuk normal, area kuning/oranye untuk "kurang/risiko", area merah untuk "buruk").
*   **Tracking Perkembangan**: User bisa melakukan hover/tap pada dot bulan tertentu untuk melihat tooltip detail angka aktual pada bulan itu.
*   **Highlight & Alerting System**:
    1.  **Berat Tidak Naik (T)**: Jika pada bulan saat ini berat badan anak *lebih rendah* atau *sama dengan* bulan sebelumnya, tampilkan indikator **Peringatan (Merah/Kuning)** (bisa berupa lencana peringatan di atas grafik atau dot grafik yang diwarnai merah).
    2.  **Masuk Zona Risiko**: Jika posisi plot terbaru anak anjlok melewati garis kuning (-2 SD), munculkan notifikasi/alert di layar bahwa "Anak masuk ke zona risiko gizi kurang/stunting, perlukan rujukan".

### 4. Halaman yang Diperlukan (Pages)
*   Mungkin letaknya di halaman **Detail Anak** (`resources/js/Pages/Anak/Show.svelte`) sebagai tab KMS, atau halaman tersendiri (`resources/js/Pages/Pengukuran/KMS.svelte`). Tentukan routing Inertia-nya yang paling intuitif.

---

## 📤 Output yang Diharapkan (Deliverables)
1. **Controller Updates**: Logika query di Controller Laravel untuk mensuplai himpunan data chart.
2. **Komponen Svelte**: Komponen `KmsChart.svelte` yang bisa menampilkan grafik dengan visualisasi garis WHO di background.
3. **Logika Peringatan**: Sistem peringatan (visual & teks) untuk dua kondisi: *berat tidak naik* dan *masuk zona risiko*.
4. Dokumentasi/Komentar singkat pada konfigurasi Chart agar mudah dimodifikasi di masa mendatang.

---
*Silakan jalankan tugas ini langkah demi langkah, mulai dari persiapan data di backend, integrasi library chart di frontend, hingga penyesuaian logika peringatannya.*
