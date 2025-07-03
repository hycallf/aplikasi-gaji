# Aplikasi Penggajian Karyawan (Payroll)

Aplikasi ini adalah sistem manajemen penggajian (payroll) internal berbasis web yang dirancang untuk mengelola seluruh siklus penggajian, mulai dari data karyawan, pencatatan kehadiran, hingga pembuatan laporan dan slip gaji. Versi ini mencakup skema penggajian yang berbeda untuk Karyawan Tetap dan Dosen.

## Lisensi

Aplikasi ini dilindungi oleh lisensi komersial. Anda tidak diizinkan untuk menjual kembali atau mendistribusikan ulang source code tanpa izin tertulis dari pengembang.

Copyright (c) [2025] [@hycallf].

## Fitur Utama

-   **Dashboard Komprehensif:**

    -   Menampilkan ringkasan data penting (Total Karyawan, Total User, Total Gaji Dibayarkan).
    -   Grafik interaktif untuk rekap kehadiran, tren pengeluaran gaji, dan komposisi gaji.
    -   Widget kalender personal untuk memantau absensi per karyawan secara visual dengan FullCalendar.js.
    -   Pesan pengingat otomatis untuk operator jika absensi harian belum lengkap.

-   **Manajemen Karyawan & User (Role-based):**

    -   CRUD (Create, Read, Update, Delete) untuk data master karyawan (termasuk data detail, departemen, riwayat pendidikan, dan foto).
    -   CRUD untuk akun login (user) yang terpisah, dengan role `operator`, `karyawan`, dan `dosen`.
    -   Proses undangan via email yang aman bagi user baru untuk mengatur password mereka sendiri.
    -   Proteksi untuk role Operator agar tidak bisa diedit/dihapus.

-   **Manajemen Kehadiran & Lembur:**

    -   **Untuk Karyawan:** Input absensi harian dengan kalender navigasi visual.
    -   **Untuk Dosen:** Input rekap kehadiran bulanan per mata kuliah.
    -   Fitur input lembur harian dan rekap bulanan.

-   **Manajemen Event & Insentif:**

    -   CRUD untuk "Jenis Event" sebagai template.
    -   Form input insentif yang memungkinkan pemilihan banyak karyawan dan banyak tanggal sekaligus menggunakan Flatpickr.js.

-   **Manajemen Potongan:**

    -   Potongan otomatis dibuat saat karyawan `pulang_awal`.
    -   CRUD untuk potongan manual (kasbon, denda, dll).

-   **Proses Payroll & Laporan:**

    -   Mesin perhitungan gaji otomatis yang menangani dua skema berbeda (gaji tetap untuk karyawan dan honorarium SKS untuk dosen).

    *   Laporan payroll interaktif menggunakan DataTables.
    *   Modal asynchronous untuk melihat rincian setiap komponen gaji langsung dari tabel laporan.
    *   Fitur **Cetak Laporan Keseluruhan** dan **Cetak Slip Gaji Individual** dalam format PDF yang profesional.

-   **Pengaturan Dinamis:**

    -   Fitur "Profil Perusahaan" di mana operator bisa mengubah nama, alamat, dan logo perusahaan yang akan otomatis tampil di semua slip gaji dan laporan.

-   **Teknologi & Tampilan:**
    -   Dibangun dengan **Laravel 12**.
    -   Frontend menggunakan **Laravel Breeze** dengan **Tailwind CSS**.
    -   Ikon dari **Font Awesome**.
    -   Notifikasi dan dialog konfirmasi modern dengan **SweetAlert2**.
    -   Tabel canggih dengan **Yajra DataTables**.
    -   Form multiselect dengan **Select2**.
    -   Kalender interaktif dengan **FullCalendar.js**.
    -   Request AJAX yang efisien dengan **HTMX**.

## Prasyarat

Pastikan perangkat Anda sudah terinstall perangkat lunak berikut:

1.  **XAMPP** dengan PHP 8.2 atau lebih baru.
2.  **Composer** (Manajer dependensi PHP).
3.  **Node.js & NPM** (Manajer paket frontend).
4.  **Git** (Sistem kontrol versi).

## Langkah-langkah Instalasi

1.  **Clone Repository Proyek**
    Buka terminal di dalam direktori `htdocs` XAMPP Anda dan jalankan:

    ```bash
    git clone https://github.com/hycallf/aplikasi-gaji.git
    ```

2.  **Masuk ke Folder Proyek**

    ```bash
    cd nama-proyek
    ```

3.  **Install Dependensi PHP**

    ```bash
    composer install
    ```

4.  **Siapkan File Environment**
    Copy file `.env.example` menjadi `.env`.

    ```bash
    copy .env.example .env
    ```

5.  **Konfigurasi Database**

    1.  Buka **phpMyAdmin** dan buat database baru yang masih kosong (misalnya `db_payroll`).
    2.  Buka file `.env` dan sesuaikan konfigurasi database:
        ```ini
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=db_payroll
        DB_USERNAME=root
        DB_PASSWORD=
        ```

6.  **(PENTING) Aktifkan Ekstensi PHP GD**

    Ekstensi ini wajib aktif agar fitur cetak PDF dengan logo bisa berjalan.

    1.  Di XAMPP Control Panel, pada baris **Apache**, klik tombol `Config`.
    2.  Pilih `PHP (php.ini)`. File akan terbuka di text editor.
    3.  Gunakan `Ctrl + F` dan cari teks: `;extension=gd`
    4.  **Hapus tanda titik koma (`;`)** di depannya sehingga baris tersebut menjadi: `extension=gd`
    5.  Simpan file `php.ini`.
    6.  **Restart Apache** dengan menekan tombol `Stop`, tunggu beberapa saat, lalu tekan `Start` lagi.

7.  **Generate Kunci Aplikasi**

    ```bash
    php artisan key:generate
    ```

8.  **Buat Struktur Database & Isi Data Awal**
    Perintah ini akan membuat semua tabel dan mengisinya dengan data awal (seperti role).

    ```bash
    php artisan migrate --seed
    ```

    _Jika Anda menghadapi error, coba jalankan `php artisan migrate:fresh --seed` untuk menghapus semua tabel lama terlebih dahulu._

9.  **Buat Symbolic Link untuk Storage**
    Penting agar file yang diupload (seperti foto karyawan) bisa diakses.

    ```bash
    php artisan storage:link
    ```

10. **Install Dependensi Frontend**

    ```bash
    npm install
    ```

11. **Compile Aset Frontend (untuk Production)**
    Jika Anda ingin langsung menjalankan versi production, jalankan perintah ini sekali.
    ```bash
    npm run build
    ```

## Menjalankan Aplikasi untuk Development

Untuk pengembangan sehari-hari, Anda perlu menjalankan **dua server** secara bersamaan di **dua terminal terpisah**:

1.  **Terminal 1 (Backend Laravel):**
    ```bash
    php artisan serve
    ```
2.  **Terminal 2 (Frontend Vite):**
    ```bash
    npm run dev
    ```

Buka browser Anda dan kunjungi alamat: **`http://127.0.0.1:8000`**

## Akun Default

Gunakan akun Operator default yang dibuat oleh Seeder untuk login pertama kali:

-   **Email:** `operator@aplikasi.com`
-   **Password:** `password123`

## Perintah Bantuan

Untuk membersihkan semua cache yang mungkin "nyangkut" setelah ada perubahan besar, jalankan perintah ini:

```bash
npm run fresh
```

atau refresh untuk cleaning lebih cepat:

```bash
npm run refresh
```
