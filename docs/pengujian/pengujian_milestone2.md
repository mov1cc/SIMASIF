# Hasil Pengujian SIMASIF

Dokumen ini berisi hasil pengujian fungsional SIMASIF (kasus uji TC-01 sampai TC-14).

## Ringkasan

- Total kasus uji: **14**
- Lulus: **13**
- Gagal: **1** (TC-06, BUG-01)

Catatan status tampilan:
- **Final**: tampilan sudah tidak diubah lagi
- **Polish UI**: fungsi sudah lulus, tetapi tampilan masih dipoles

## Yang Sudah Lulus

### Autentikasi dan hak akses
- **TC-01**: Login pegawai dengan email dan sandi benar, dashboard pegawai terbuka. Final.
- **TC-02**: Login owner dengan email dan sandi benar, dashboard owner terbuka. Final.
- **TC-03**: Login dengan sandi salah, pesan gagal tampil dan pengguna tidak bisa masuk. Final.
- **TC-04**: Pegawai membuka halaman owner lewat URL, akses ditolak. Masih Polish UI.

### Pemesanan pelanggan
- **TC-05**: Pelanggan mengisi form pemesanan lalu kirim, pesanan tersimpan dan kode unik tampil. Masih Polish UI.
- **TC-07**: Pelanggan memilih jadwal yang sudah terisi, jadwal ditolak atau tidak dapat dipilih. Masih Polish UI.
- **TC-08**: Pelanggan membuka cek status dengan kode unik, status dan progres pesanan tampil. Masih Polish UI.

### Pembayaran dan workflow
- **TC-09**: Pelanggan mengunggah bukti pembayaran transfer, bukti tersimpan dan status menunggu verifikasi. Masih Polish UI.
- **TC-10**: Pegawai memverifikasi pembayaran, status berubah dan tercatat di workflow. Final.
- **TC-11**: Pegawai memperbarui status workflow pesanan, status berubah dan terlihat di cek status pelanggan. Final.
- **TC-12**: Pegawai mengunggah hasil foto, pelanggan berhasil mengunduh lewat kode unik. Final.

### Dashboard dan audit log
- **TC-13**: Owner membuka dashboard setelah ada pesanan baru, angka booking dan pendapatan sesuai data. Final.
- **TC-14**: Pegawai mengubah status pembayaran lalu owner membuka audit log, aktivitas tercatat. Final.

## Yang Gagal

### TC-06: Draf pemesanan tidak muncul kembali (BUG-01)
- **Skenario**: Pelanggan mengisi form, menutup halaman, lalu membukanya lagi.
- **Hasil diharapkan**: Isian draf muncul kembali.
- **Hasil aktual**: Isian draf tidak muncul kembali, form tampil kosong.
- **Status**: Gagal.

## Tabel Lengkap

| ID | Skenario | Hasil Diharapkan | Hasil Aktual | Status |
|----|----------|------------------|--------------|--------|
| TC-01 | Login pegawai dengan email dan sandi benar | Masuk ke dashboard pegawai | Dashboard pegawai terbuka | Lulus (final) |
| TC-02 | Login owner dengan email dan sandi benar | Masuk ke dashboard owner | Dashboard owner terbuka | Lulus (final) |
| TC-03 | Login dengan sandi salah | Pesan gagal tampil, tidak masuk | Pesan gagal tampil, pengguna tidak masuk | Lulus (final) |
| TC-04 | Pegawai membuka halaman owner lewat URL | Akses ditolak | Akses ditolak | Lulus (Polish UI) |
| TC-05 | Pelanggan mengisi form pemesanan lalu kirim | Pesanan tersimpan, kode unik tampil | Pesanan tersimpan dan kode unik tampil | Lulus (Polish UI) |
| TC-06 | Pelanggan mengisi form, menutup halaman, lalu membuka lagi | Isian draf muncul kembali | Isian draf tidak muncul kembali, form tampil kosong | **Gagal (BUG-01)** |
| TC-07 | Pelanggan memilih jadwal yang sudah terisi | Jadwal ditolak atau tidak bisa dipilih | Jadwal yang sudah terisi ditolak atau tidak dapat dipilih | Lulus (Polish UI) |
| TC-08 | Pelanggan membuka cek status dengan kode unik | Status dan progres pesanan tampil | Status dan progres pesanan tampil | Lulus (Polish UI) |
| TC-09 | Pelanggan mengunggah bukti pembayaran transfer | Bukti tersimpan, status menunggu verifikasi | Bukti tersimpan, status menunggu verifikasi | Lulus (Polish UI) |
| TC-10 | Pegawai memverifikasi pembayaran | Status pembayaran berubah, tercatat di workflow | Status pembayaran berubah dan tercatat di workflow | Lulus (final) |
| TC-11 | Pegawai memperbarui status workflow pesanan | Status berubah dan terlihat di cek status pelanggan | Status berubah dan terlihat di cek status pelanggan | Lulus (final) |
| TC-12 | Pegawai mengunggah hasil foto, pelanggan mengunduh lewat kode unik | File berhasil diunduh | File berhasil diunduh | Lulus (final) |
| TC-13 | Owner membuka dashboard setelah ada pesanan baru | Angka booking dan pendapatan sesuai data | Angka booking dan pendapatan sesuai data | Lulus (final) |
| TC-14 | Pegawai mengubah status pembayaran lalu owner membuka audit log | Aktivitas tercatat | Aktivitas tercatat di audit log | Lulus (final) |