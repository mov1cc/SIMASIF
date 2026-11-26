WBS -- Work Breakdown Stucture

## 1. Identifikasi

- 1.1. Inisiasi Proyek & Pembentukan Kelompok
    - 1.1.1 Pembentukan kelompok & Menentukan peran
    - 1.1.2 Membuat ruang kerja tim (Workspace Notion) untuk mencatat tugas.
- 1.2  Identifikasi Masalah dan Analisis Kebutuhan
    - 1.2.1 Wawancara mitra
    - 1.2.2 Membuat google from untuk pelanggan studio
    - 1.2.3 Mengindentifikasi permasalahan
- 1.3 Penentuan Batasan & Fitur Aplikasi
    - 1.3.1 Menentukan tujuan utama
    - 1.3.2 Menentukan batasan sistem
    - 1.3.3 Menentukan fitur utama

## 2. Perencanaan Proyek

- 2.1 Perencanaan Sistem
    - 2.1.1 Menentukan peran pengguna dan hak aksesnya
    - 2.1.2 Menentukan kebutuhan fungsional
    - 2.1.3 Menentukan kebutuhan non - fungsional
- 2.2 Perencanaan Proyek
    - 2.2.1 Menyusun WBS
    - 2.2.2 Menentukan target batas waktu di setiap milestone/checkpoint.
- 2.3 Penyusunan Proposal
    - 2.3.1 Menyusun proposal
    - 2.3.2 Melakukan review dan revisi proposal
    - 2.3.4 Finalisasi proposal

## 3. Analisis dan Perancangan Sistem

- 3.1 Analisis dan Perancangan Sistem
    - 3.1.1 Menganalisis alur layanan pelanggan (pemesanan, pembayaran, hasil foto)
    - 3.1.2 Menganalisis alur operasional pegawai (pemesanan, workflow, verifikasi pembayaran)
    - 3.1.3 Menganalisis alur monitoring owner
    - 3.1.4 Membuat DFD Level 0, 1, dan 2
- 3.2 Perancangan Sistem
    - 3.2.1 Use Case Diagram
    - 3.2.2 Activity Diagram
    - 3.2.3 Class Diagram
- 3.3 Perancangan Database
    - 3.3.1 Identifikasi 8 entitas
    - 3.3.2 Penyusunan ERD
    - 3.3.3 Menentukan relasi dan kardinalitas
    - 3.3.4 Normalisasi hingga 3NF
    - 3.3.5 Menyusun kamus data
    - 3.3.6 Validasi rancangan database
- 3.4 Perancangan UI/UX
    - 3.4.1 Membuat user flow
    - 3.4.2 Wireframe
    - 3.4.3 Mockup Figma
    - 3.4.4 Prototype
    - 3.4.5 Review dan revisi desain

## 4. Pengembangan Sistem

- 4.1 Pengembangan Environment
    - 4.1.1 Instalasi PHP, PostgreSQL, Composer
    - 4.1.2 Repository Git dan struktur folder proyek
    - 4.1.3 Konfigurasi .env dan virtual host
- 4.2 Implementasi Databas
    - 4.2.1 Migration 8 tabel, ENUM, foreign key, constraint
    - 4.2.2 Trigger updated_at
    - 4.2.3 Seeder user dan layanan
- 4.3 Mini-Framework (Core)
    - 4.3.1 Database, Request, Response, Session
    - 4.3.2 Router, Controller, Model, Middleware
    - 4.3.3 Config, exception handler, entry point
- 4.4 Autentikasi dan Hak Akses
    - 4.4.1 Halaman login dan logout
    - 4.4.2 AuthService, AuthMiddleware, RoleMiddleware
    - 4.4.3 Layout guest, pegawai, owner
- 4.5 Data Master
    - 4.5.1 CRUD layanan
    - 4.5.2 CRUD pelanggan (validasi no HP unik)
- 4.6 Halaman Publik: Informasi Layanan
    - 4.6.1 Daftar paket
    - 4.6.2 Detail layanan
    - 4.6.3 Tombol WhatsApp
- 4.7 Pemesanan dan Kode Unik
    - 4.7.1 Form pemesanan dan permintaan khusus
    - 4.7.2 Generate kode unik dan halaman konfirmasi
- 4.8 Pengelolaan Pemesanan dan Jadwal
    - 4.8.1 Kelola pemesanan
    - 4.8.2 Kelola jadwal
- 4.9 Workflow Layanan dan Tracking
    - 4.9.1 Update status layanan
    - 4.9.2 Validasi perubahan status
    - 4.9.3 Halaman cek status dan tracking pelanggan
- 4.10 Transaksi Pembayaran
    - 4.10.1 Pilihan metode dan upload bukti transfer
    - 4.10.2 Verifikasi dan update status pembayaran
- 4.11 File Fotografi
    - 4.11.1 Upload file pelanggan dan hasil foto
    - 4.11.2 Download aman (validasi kode unik, kepemilikan, status)
- 4.12 Dashboard Owner dan Pegawai
    - 4.12.1 Dashboard pegawai
    - 4.12.2 Dashboard owner (statistik, grafik, donut chart, filter periode)
- 4.13 Laporan, Audit Log, dan Manajemen Akun
    - 4.13.1 Laporan pemesanan dan pendapatan
    - 4.13.2 Audit log
    - 4.13.3 Manajemen akun pegawai, profil, reset password
- 4.14 Pencarian, Filter, dan Polish UI
    - 4.14.1 Search dan filter data
    - 4.14.2 Penyempurnaan tampilan dan responsif
    - 4.14.3 Optimasi query dan cleanup kode

## 5. Pengujian dan Penyempurnaan

- 5.1 Keamanan dan Pengujian Sistem
    - 5.1.1 Menyusun skenario pengujian
    - 5.1.2 Audit keamanan (query, route, form, upload, storage)
    - 5.1.3 Uji SQL Injection dan XSS
    - 5.1.4 Regression test alur utama
    - 5.1.5 Uji performa data skala lebih besar
    - 5.1.6 Mencatat hasil pengujian dan bug
- 5.2 Perbaikan Sistem
    - 5.2.1 Mengidentifikasi dan memprioritaskan bug
    - 5.2.2 Memperbaiki bug
    - 5.2.3 Menguji ulang fitur yang diperbaiki
- 5.3 UAT dan Validasi Produk
    - 5.3.1 Menyiapkan skenario UAT
    - 5.3.2 Uji bersama mitra
    - 5.3.3 Revisi berdasarkan masukan mitra
    - 5.3.4 Finalisasi sistem

## 6. Dokumentasi dan Diseminasi

- 6.1 Deployment dan Rilis
    - 6.1.1 Setup server dan migrasi database
    - 6.1.2 Konfigurasi .env production dan HTTPS
    - 6.1.3 Smoke test, backup database, tag versi v1.0
    - 6.1.4 Menyiapkan akun demo
- 6.2 Dokumentasi Proyek
    - 6.2.1 Dokumentasi teknis (database dan sistem)
    - 6.2.2 User manual
    - 6.2.3 Melengkapi logbook mingguan
- 6.3 Laporan Akhir
    - 6.3.1 Laporan analisis
    - 6.3.2 Laporan perancangan
    - 6.3.3 Laporan implementasi
    - 6.3.4 Laporan pengujian
    - 6.3.5 Finalisasi laporan akhir
- 6.4 Presentasi dan Demo
    - 6.4.1 Menyusun slide presentasi
    - 6.4.2 Menyiapkan demo dan pembagian materi
    - 6.4.3 Latihan presentasi
    - 6.4.4 Presentasi dan Expo PBL