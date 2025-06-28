# Aplikasi Penggajian Karyawan (Payroll)

Aplikasi ini adalah sistem manajemen penggajian (payroll) internal berbasis web yang dirancang untuk mengelola seluruh siklus penggajian, mulai dari data karyawan, pencatatan kehadiran, hingga pembuatan laporan dan slip gaji.

## Fitur Utama

-   **Dashboard Komprehensif:**

    -   Menampilkan ringkasan data penting (Total Karyawan, Total User, Total Gaji Dibayarkan).
    -   Grafik interaktif untuk rekap kehadiran, tren pengeluaran gaji (6 bulan), dan komposisi gaji.
    -   Widget kalender personal untuk memantau absensi per karyawan secara visual.
    -   Pesan pengingat otomatis untuk operator jika absensi harian belum lengkap.

-   **Manajemen Karyawan & User (Role-based):**

    -   CRUD (Create, Read, Update, Delete) untuk data master karyawan (termasuk data detail dan foto).
    -   CRUD untuk akun login (user) yang terpisah, dengan role `operator`, `karyawan`, dan `dosen`.
    -   Sistem role manual sederhana yang aman, di mana Operator tidak bisa diedit/dihapus.
    -   Modal detail karyawan yang asynchronous untuk melihat informasi lengkap tanpa meninggalkan halaman.

-   **Manajemen Kehadiran Harian:**

    -   Tampilan input dengan kalender navigasi visual untuk memilih tanggal.
    -   Pencarian nama karyawan secara asynchronous (tanpa reload halaman) menggunakan HTMX.
    -   Status kehadiran: Hadir, Sakit, Izin, Telat, Pulang Awal.
    -   Input keterangan dinamis untuk status tertentu.
    -   Constraint untuk mencegah input absensi di masa depan atau pada hari libur.

-   **Manajemen Lembur, Insentif & Potongan:**

    -   Fitur input lembur harian dengan deskripsi dan nominal upah.
    -   Fitur "Event" untuk mengelola insentif atau bonus non-rutin dengan kemampuan memilih banyak karyawan sekaligus.
    -   Fitur "Potongan" yang menangani potongan manual (seperti kasbon) dan potongan otomatis (misal: dari status `pulang_awal` di absensi).

-   **Proses Payroll & Laporan:**

    -   Mesin perhitungan gaji otomatis per periode (bulan/tahun).
    -   Laporan payroll interaktif menggunakan DataTables (search, sort, pagination).

    *   Modal asynchronous untuk melihat rincian setiap komponen gaji (transport, lembur, dll) langsung dari tabel laporan.
    *   Fitur **Cetak Laporan Keseluruhan** dan **Cetak Slip Gaji Individual** dalam format PDF.

-   **Teknologi & Tampilan:**
    -   Dibangun dengan **Laravel 12**.
    -   Frontend menggunakan **Laravel Breeze** dengan **Tailwind CSS**.
    -   Ikon dari **Font Awesome**.
    -   Notifikasi dan dialog konfirmasi modern dengan **SweetAlert2**.
    -   Tabel canggih dengan **Yajra DataTables**.
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
    git clone https://URL-GITHUB-ANDA/nama-proyek.git
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

6.  **Generate Kunci Aplikasi**

    ```bash
    php artisan key:generate
    ```

7.  **Buat Struktur Database & Isi Data Awal**
    Perintah ini akan membuat semua tabel dan mengisinya dengan data awal (seperti role).

    ```bash
    php artisan migrate --seed
    ```

    _Jika Anda menghadapi error, coba jalankan `php artisan migrate:fresh --seed` untuk menghapus semua tabel lama terlebih dahulu._

8.  **Buat Symbolic Link untuk Storage**
    Penting agar file yang diupload (seperti foto karyawan) bisa diakses.

    ```bash
    php artisan storage:link
    ```

9.  **Install Dependensi Frontend**

    ```bash
    npm install
    ```

10. **Compile Aset Frontend (untuk Production)**
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
