# Task: Pembuatan Halaman Modul Pengukuran & Penimbangan (Atomic Design)

## 📌 Deskripsi Tugas
Kita membutuhkan fitur halaman untuk **Modul Pengukuran dan Penimbangan Anak/Balita** pada sistem Posyandu. Tugas utamanya adalah merancang dan membuat halaman front-end dengan menggunakan **Prinsip Atomic Components** agar elemen-elemen UI yang dibuat dapat di-reuse (digunakan kembali) di halaman atau modul lain.

Aplikasi ini menggunakan stack **Laravel, Svelte 5, dan Inertia.js**, serta Tailwind CSS untuk styling.

## 🎯 Tujuan Utama
1. Membuat antarmuka (UI) untuk form input data pengukuran (berat badan, tinggi badan, dsb.).
2. Menerapkan Atomic Design (Atoms, Molecules, Organisms, Pages).
3. Memastikan state management form menggunakan fitur bawaan dari Inertia.js (`useForm`).

---

## 📋 Detail Spesifikasi

### 1. Penerapan Atomic Components
Jangan membuat kode UI yang bertumpuk (monolithic) dalam satu file Svelte. Pecah komponen menjadi bagian-bagian kecil:

*   **Atoms (Komponen Dasar):**
    *   Jika belum ada, buat atau gunakan komponen dasar seperti `InputText`, `InputNumber`, `SelectBox`, `InputLabel`, `Button`, dan `InputError`. 
    *   (Catatan: Pastikan untuk mengecek folder `resources/js/Components/UI/`, gunakan komponen yang sudah ada seperti `Card.svelte`, `InputLabel.svelte`, `Button.svelte`).
*   **Molecules (Gabungan Atoms):**
    *   Buat komponen organik kecil seperti `FormFieldGroup.svelte` yang menggabungkan `InputLabel`, `InputText`/`InputNumber`, dan `InputError` menjadi satu kesatuan agar rapi.
*   **Organisms (Komponen Kompleks):**
    *   Buat `PengukuranForm.svelte`: Komponen form khusus yang membungkus seluruh field pengukuran.
    *   Buat `PengukuranTable.svelte` (opsional jika ada list d halaman yang sama): Tabel atau list riwayat hasil ukur.
*   **Pages (Halaman Utama):**
    *   Buat di `resources/js/Pages/Pengukuran/Create.svelte` (atau `Index.svelte`). Halaman ini hanya akan memanggil *Organisms* dan menyambungkannya dengan backend via Inertia.

### 2. Kebutuhan Data (Field Form)
Form minimal harus memiliki input berikut:
*   **Tanggal Pengukuran** (Date picker / Text date)
*   **Berat Badan** dalam kg (Input Number, step: 0.1)
*   **Tinggi/Panjang Badan** dalam cm (Input Number, step: 0.1)
*   **Lingkar Kepala** dalam cm (Input Number, step: 0.1)
*   **Cara Ukur** (Dropdown/Select: "Berdiri" atau "Telentang")
*   **Catatan/Keterangan** (Textarea / Text input panjang)

### 3. Fungsionalitas & State Management
*   Gunakan sintaks **Svelte 5** (`$state`, `$derived`, `$props()`).
*   Gunakan `useForm` dari `@inertiajs/svelte` untuk inisialisasi state form, reset form, dan proses submit (POST data).
*   Tampilkan pesan error validasi (dari backend) di bawah setiap field menggunakan atribut `.errors` pada object `form`.

### 4. Standard Styling & Best Practices
*   Gunakan kelas utility dari **Tailwind CSS**.
*   Pastikan komponen bersifat *fully customizable*. Contoh: Komponen Atom harus menerima prop `class` tambahan dari luar.
*   Terapkan *Two-way binding* untuk input form jika memungkinkan, atau tangkap *event* secara reaktif sesuai best practice Svelte 5.

---

## 📤 Output yang Diharapkan (Deliverables)
1. **File Atoms & Molecules:** File Svelte di folder `Components/UI/` (atau gunakan yang sudah ada).
2. **File Organisms:** File komponen spesifik modul di folder `Components/Pengukuran/` (misal: `FormPengukuran.svelte`).
3. **File Pages:** Halaman utama Inertia di `Pages/Pengukuran/Create.svelte` atau `Index.svelte`.
4. Kode harus bersih, menggunakan komentar untuk bagian logika yang rumit, dan konsisten secara desain.

---
*Silakan ajukan pertanyaan (clarification) jika ada spesifikasi field database atau API backend yang kurang jelas sebelum mulai coding.*
