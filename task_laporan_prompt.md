# Task: Pembuatan Modul Laporan & Rekap Terpadu

## 📌 Deskripsi Tugas
Tugas ini difokuskan pada pembuatan modul **Laporan & Rekapitulasi** untuk sistem aplikasi Posyandu. Modul ini merupakan fitur krusial (end-goal) yang digunakan oleh kader dan pihak Puskesmas untuk mengevaluasi kesehatan balita di wilayah tersebut setiap bulannya secara otomatis tanpa perlu menghitung manual.

Sistem akan melakukan agregasi data dari modul-modul sebelumnya (Data Anak, Pengukuran, Kehadiran) dan menyajikannya dalam format yang mudah dibaca serta siap untuk didownload.

Tech Stack: **Laravel, Svelte 5, Inertia.js, Tailwind CSS**.

## 🎯 Tujuan Utama
1. **Automatisasi Laporan**: Menggantikan rekapitulasi data buku KIA/Posyandu manual dengan *dashboard summary* sekali klik.
2. **Kompilasi Data Kesehatan**: Menampilkan jumlah total balita, memetakan demografi status gizi dan stunting, serta merekap tingkat partisipasi (kehadiran).
3. **Fitur Ekspor**: Memberikan kemampuan untuk mengunduh laporan dalam format **PDF** dan **Excel**.

---

## 📋 Detail Spesifikasi

### 1. Kebutuhan Data & Query (Backend Controller)
Buat `LaporanController` yang bertugas melakukan perhitungan statistik (*aggregation*). Minimal data yang harus disiapkan dan dikirimkan ke Svelte Frontend:
*   **Jumlah Balita**: Total anak aktif yang terdaftar di database untuk Posyandu terkait.
*   **Distribusi Status Gizi (BB/U)**: 
    *   Berapa jumlah anak berstatus Gizi Buruk, Gizi Kurang, Gizi Baik, Gizi Lebih di bulan tertentu berdasarkan tabel `trx_pengukuran` terakhir.
*   **Distribusi Status Stunting (TB/U)**:
    *   Berapa jumlah anak berstatus Sangat Pendek, Pendek, Normal, Tinggi.
*   **Statistik Kehadiran**: 
    *   Persentase atau jumlah riil anak yang hadir pada kegiatan bulan ini dibandingkan total anak terdaftar (berdasarkan `trx_kehadiran`).

### 2. Antarmuka Dashboard Laporan (Frontend)
*   **Halaman**: Buat `resources/js/Pages/Laporan/Index.svelte`.
*   **Filter Waktu**: Harus ada *dropdown* filter **Bulan** dan **Tahun** agar laporan yang muncul sesuai periode yang diinginkan.
*   **Visualisasi Metrik**: Tampilkan output data di atas menggunakan komponen `Card` yang rapi. Sangat disarankan jika agregasi (Gizi/Stunting) direpresentasikan sekilas dalam bentuk *Donut Chart* atau *Bar Chart* (bisa menggunakan Chart.js yang sudah terpasang).

### 3. Fitur Ekspor (Download)
Pastikan laporan bisa dibawa ke perancangan luring:
*   **Export Excel (.xlsx)**:
    *   Lebih diutamakan untuk menggunakan *package* PHP populer seperti `maatwebsite/excel` (Laravel Excel) di sisi backend.
    *   Isi file Excel: Daftar rinci nama anak, NIK, Berat Terakhir, Tinggi Terakhir, Status Gizi, Status Stunting, dan Kehadiran pada bulan yang di-filter.
*   **Export PDF (.pdf)**:
    *   Gunakan backend package seperti `barryvdh/laravel-dompdf` atau sejenisnya.
    *   Isi file PDF: Halaman ringkas yang elegan berisi kop surat (atas nama posyandu), tabel rekap (summary Gizi/Stunting), dan ditutup dengan form tanda tangan ketua posyandu di bagian bawah.
*   Tambahkan dua tombol besar di halaman `Laporan/Index.svelte`: **[ UNDUH PDF ]** dan **[ UNDUH EXCEL ]**.

---

## 📤 Output yang Diharapkan (Deliverables)
1. **LaporanController & Routes**: Mengatur API data statistik dan method `exportPdf` / `exportExcel`.
2. **Library Ekspor**: Integrasi package PHP minimal untuk PDF dan Excel.
3. **Halaman UI Laporan**: Integrasi Svelte, Inertia, dengan desain yang *clean*, profesional, dan fungsional.
4. **Pembaruan Navigasi**: Pastikan sidebar/menu utama sudah memiliki akses ke "Laporan".

---
*Catatan Tambahan untuk Programmer: Gunakan Eloquent dengan metode `count()`, `groupBy()`, dan fungsi datetime/Carbon sebaik mungkin agar query efisien dan tidak memberatkan server saat generate laporan.*
