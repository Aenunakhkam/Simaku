# SIMAKU (Sistem Manajemen Administrasi Keuangan)

SIMAKU adalah aplikasi berbasis *web* yang dirancang secara khusus untuk mempermudah tata kelola keuangan, administrasi pembayaran siswa (seperti SPP), serta pencatatan pengeluaran operasional di lingkungan institusi pendidikan (Sekolah).

Aplikasi ini dikembangkan oleh **Aenunakhkam** dan dibangun di atas arsitektur modern menggunakan kerangka kerja (framework) **Laravel**, **Vue.js**, dan **Inertia.js** dengan antarmuka yang dirancang menarik berbasis **TailwindCSS**.

---

## Fitur Utama

Aplikasi SIMAKU dilengkapi dengan berbagai modul untuk mendukung operasional tata usaha sekolah:

- 📊 **Dashboard Analitik**  
  Menampilkan ringkasan data penting seperti: Saldo Kas Total, Pemasukan Bulan Ini, Pengeluaran Bulan Ini, dan Total Tunggakan Siswa secara *real-time*.

- 💰 **Manajemen Kategori Keuangan**  
  Membuat dan mengatur berbagai kategori penerimaan (contoh: SPP, Uang Pangkal, Seragam) maupun pengeluaran operasional. Modul ini mendukung pemformatan nominal otomatis dalam standar Rupiah.

- 👨‍🎓 **Data Master Siswa & Kelas**  
  Mengelola data siswa lengkap dengan Nomor Induk (NIS/NPSN), pembagian Kelas, dan Jurusan untuk mempermudah proses penagihan (*billing*).

- 💳 **Transaksi Pembayaran & Pengeluaran**  
  Pencatatan langsung untuk semua arus uang masuk (dari siswa) dan uang keluar. Seluruh transaksi terekam aman secara kronologis.

- 📥 **Backup Database Otomatis**  
  Dilengkapi dengan fitur satu-klik untuk mengunduh keseluruhan data sistem demi keamanan informasi (*Disaster Recovery*).

- 🔄 **Cek Pembaruan Terintegrasi**  
  Menampilkan riwayat rilis (*changelog*) secara langsung di dalam aplikasi untuk memantau fitur-fitur baru apa saja yang telah dikembangkan.

---

## Cara Penggunaan (Panduan Singkat)

Aplikasi ini terhubung langsung ke mesin *Database* dan *Web Server*. Berikut adalah alur penggunaan dasar:

1. **Jalankan Server:**  
   Buka file `Start-Simaku.bat` (jika Anda menggunakan versi Portabel) untuk menghidupkan *database* PostgreSQL dan *server* aplikasi secara otomatis.

2. **Login ke Sistem:**  
   Buka *browser* (direkomendasikan Google Chrome atau Firefox) dan ketikkan alamat:  
   `http://localhost:2705` (atau sesuai port yang sedang berjalan). Masuk menggunakan kredensial Admin Anda.

3. **Atur Master Data:**  
   - Mulailah dengan mengatur **Jurusan** dan **Kelas**.
   - Masukkan daftar **Siswa**.
   - Buat **Kategori Pemasukan** (misal: SPP Rp150.000, Seragam Rp200.000).

4. **Proses Transaksi:**  
   - Apabila ada siswa yang membayar, masuk ke menu Transaksi > Pemasukan, lalu catat pembayarannya.
   - Apabila ada keperluan pembelian barang sekolah, masuk ke menu Transaksi > Pengeluaran.
   - Nilai **Total Kas** di Dashboard akan otomatis menyesuaikan dengan sisa uang bersih dari seluruh transaksi tersebut.

5. **Backup Secara Berkala:**  
   Sangat disarankan untuk membuka menu **Pengaturan > Backup Data** setidaknya seminggu sekali untuk mengunduh salinan database ke Flashdisk atau Google Drive Anda.

---

## Pengembang

Aplikasi ini masih terus dikembangkan dan disempurnakan.
- **Author:** Aenunakhkam
- **Framework Utama:** Laravel + Vue 3 + TailwindCSS
- **Database:** PostgreSQL (Portable)
