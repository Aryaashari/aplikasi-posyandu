# Prompt: Pembuatan CRUD Master Data Anak (Inertia.js + Svelte 5)

**Deskripsi Tugas:**
Buatlah fitur CRUD (Create, Read, Update, Delete) lengkap untuk **Master Data Anak** pada Sistem Posyandu. Fitur ini mencakup halaman daftar (tabel dengan pencarian), form tambah data, dan form edit data.

## Spesifikasi Teknis:

### 1. Backend (Laravel 12):
*   **Controller:** Buat `AnakController`.
    *   `index()`: Mengambil data dari tabel `md_anak` dengan dukungan pencarian (`search`) dan pagination. Gunakan `Inertia::render`.
    *   `store()`: Validasi dan simpan data anak baru.
    *   `update()`: Validasi dan perbarui data anak yang ada.
    *   `destroy()`: Hapus data anak.
*   **Routing:** Daftarkan rute resource di `web.php` di bawah middleware `auth`.
*   **Model:** Gunakan model `MdAnak` yang sudah ada. Pastikan `fillable` mencakup semua field.
*   **Pencarian:** Implementasikan pencarian berdasarkan `nama` atau `nik`.

### 2. Frontend (Svelte 5 + Inertia.js v3):
*   **Halaman (Pages):**
    *   `Anak/Index.svelte`: Menampilkan tabel data anak, input pencarian, dan tombol "Tambah Anak".
    *   `Anak/Form.svelte`: Komponen form yang digunakan bersama untuk tambah dan edit (gunakan props untuk membedakan mode).
*   **Komponen UI (WAJIB DIGUNAKAN):**
    *   Gunakan komponen yang sudah ada di `resources/js/Components/UI/` (`Card`, `Input`, `InputLabel`, `InputError`, `Button`).
    *   Pastikan tabel bersifat responsif dan mudah dibaca oleh kader posyandu (usia 30-50 tahun).
*   **State Management:**
    *   Gunakan `useForm` dari `@inertiajs/svelte` v3.
    *   **PENTING:** Karena menggunakan Svelte 5, akses properti form secara langsung (misal: `form.nama`, bukan `$form.nama`) sesuai dengan standar terbaru Inertia Svelte 3.0.
*   **UX/Aesthetics:**
    *   Tampilkan pesan sukses menggunakan sistem Flash Message Inertia.
    *   Berikan konfirmasi sebelum menghapus data.
    *   Gunakan skema warna `posyandu-primary` dan `posyandu-secondary`.

## Struktur Data (md_anak):
- `nama` (string)
- `nik` (string, unik)
- `no_kk` (string)
- `tanggal_lahir` (date)
- `jenis_kelamin` (enum: L/P)
- `nama_ayah` (string)
- `nama_ibu` (string)
- `no_telp_ortu` (string)
- `alamat` (text)

## Harapan Hasil:
Berikan kode yang lengkap dan rapi untuk Controller, Routing, dan Pages Svelte. Pastikan logika pencarian di backend efisien dan integrasi frontend dengan UI atomic components berjalan lancar.
