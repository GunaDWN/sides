# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## Sistem Informasi Desa/Kelurahan — SIDES

**Versi:** 1.0  
**Status:** Draft Pengembangan  
**Platform:** Web Application  
**Nama aplikasi:** SIDES  
**Nama database:** `sides`  
**Target implementasi awal:** Satu instalasi dapat menampung satu atau lebih desa/kelurahan  

---

## Daftar Isi

1. Ringkasan Produk
2. Latar Belakang
3. Tujuan Produk
4. Ruang Lingkup
5. Target Pengguna dan Role
6. Teknologi
7. Konfigurasi Awal Sistem
8. Struktur Halaman
9. Matriks Hak Akses
10. Modul Data Desa
11. Modul Registrasi Warga
12. Modul Approval Pembuatan Akun
13. Modul Pengelolaan User
14. Modul Pengelolaan Jabatan
15. Modul Pengelolaan Warga
16. Modul Hak Akses Jabatan
17. Modul Jenis Surat
18. Modul Pengajuan Surat
19. Modul Approval Surat
20. Manajemen Versi Dokumen
21. Status dan State Transition
22. Log dan Audit Trail
23. Notifikasi
24. Dashboard
25. Struktur Data dan Tabel
26. Relasi Data
27. Aturan Bisnis
28. Validasi
29. Keamanan
30. Kebutuhan Nonfungsional
31. Komponen UI/UX
32. Kriteria Penerimaan
33. Skenario Pengujian Utama
34. Prioritas Pengembangan MVP
35. Risiko dan Mitigasi
36. Asumsi Produk
37. Di Luar Ruang Lingkup MVP
38. Indikator Keberhasilan
39. Definition of Done

---

# 1. Ringkasan Produk

SIDES adalah sistem informasi berbasis web untuk membantu pemerintah desa atau kelurahan mengelola identitas desa, data warga, akun pengguna, jabatan, jenis surat, pengajuan surat, serta proses persetujuan surat secara bertahap dan terdokumentasi.

Sistem memiliki dua role autentikasi utama:

1. **Admin**
2. **Warga**

Warga yang memiliki jabatan tetap menggunakan role **Warga**, tetapi memperoleh hak tambahan berdasarkan jabatan aktif dan permission yang diberikan Admin.

Contoh hak tambahan warga yang memiliki jabatan:

- Mengakses halaman pengajuan pembuatan akun.
- Menyetujui atau menolak pembuatan akun warga.
- Mengelola jenis surat tertentu.
- Melihat permintaan approval surat.
- Menyetujui, menolak, atau meminta perbaikan dokumen surat.
- Mengunggah dokumen yang sudah ditandatangani atau diberi stempel.

SIDES mendukung approval surat secara berurutan. Dokumen yang diterima pejabat berikutnya harus merupakan dokumen terakhir yang diunggah pada tahapan sebelumnya.

---

# 2. Latar Belakang

Proses administrasi desa dan kelurahan sering dilakukan secara manual. Warga harus datang ke kantor desa, mengisi dokumen, menyerahkan fotokopi identitas, menunggu pemeriksaan petugas, dan memantau proses melalui komunikasi langsung.

Permasalahan utama yang ingin diselesaikan:

- Data warga belum tersimpan secara terpusat.
- Proses pembuatan akun warga belum memiliki mekanisme verifikasi.
- Pengajuan surat sulit dilacak.
- Urutan persetujuan pejabat belum terdokumentasi.
- Dokumen dapat tertimpa ketika diedit beberapa pihak.
- Warga tidak mengetahui posisi pengajuan.
- Riwayat perubahan status tidak tercatat dengan baik.
- Hak akses pejabat belum dapat dikonfigurasi secara fleksibel.
- Dokumen KTP, tanda tangan, stempel, dan surat membutuhkan perlindungan akses.

SIDES menyediakan proses administrasi yang lebih terstruktur, transparan, aman, dan mudah diaudit.

---

# 3. Tujuan Produk

SIDES bertujuan untuk:

- Memusatkan pengelolaan data desa dan warga.
- Menyediakan proses registrasi warga yang diverifikasi Admin atau pejabat berwenang.
- Mempermudah warga mengajukan surat secara daring.
- Mengatur hak akses berdasarkan role dan jabatan aktif.
- Mendukung approval surat berurutan oleh beberapa pejabat.
- Menyimpan setiap versi dokumen.
- Menyediakan timeline status yang dapat dipantau warga.
- Menyimpan log setiap perubahan status.
- Mengurangi ketergantungan pada proses administrasi manual.
- Menjaga agar dokumen sensitif hanya diakses oleh pengguna yang berwenang.

---

# 4. Ruang Lingkup

## 4.1 Ruang Lingkup MVP

- Landing page publik.
- Login page.
- Registrasi warga.
- Pencarian desa berdasarkan Provinsi, Kabupaten/Kota, dan Kecamatan.
- Upload KTP.
- Approval pembuatan akun.
- Dashboard Admin.
- Dashboard Warga.
- Menu tambahan untuk warga yang memiliki jabatan.
- Pengelolaan data desa.
- Pengelolaan user.
- Pengelolaan jabatan.
- Pengelolaan warga.
- Pengelolaan hak akses jabatan.
- Pengelolaan jenis surat.
- Upload template surat.
- Penentuan jabatan pengelola surat.
- Penentuan pejabat approval dan urutannya.
- Pengajuan surat oleh warga.
- Download template surat.
- Upload dokumen yang sudah diisi.
- Approval surat berurutan.
- Status Terima, Tolak, dan Ulangi.
- Upload ulang dokumen oleh warga.
- Riwayat pengajuan.
- Riwayat approval.
- Penyimpanan versi dokumen.
- Log perubahan status.
- Notifikasi dalam aplikasi.

## 4.2 Pengembangan Lanjutan

- Notifikasi email.
- Notifikasi WhatsApp.
- Tanda tangan digital tersertifikasi.
- Integrasi Dukcapil.
- Integrasi data wilayah resmi melalui API.
- Editor dokumen langsung di browser.
- Generator surat berbasis form dinamis.
- Aplikasi mobile.
- Multi-bahasa.
- Pembayaran administrasi surat.

---

# 5. Target Pengguna dan Role

## 5.1 Admin

Admin bertanggung jawab mengelola sistem.

Admin dapat:

- Mengelola data desa.
- Mengelola user.
- Mengelola jabatan.
- Mengelola data warga.
- Menentukan permission jabatan.
- Melihat dan memproses pengajuan pembuatan akun.
- Menentukan jabatan yang dapat memproses pengajuan akun.
- Mengelola seluruh jenis surat.
- Menentukan jabatan yang boleh mengelola jenis surat.
- Menentukan urutan approval surat.
- Melihat seluruh pengajuan surat.
- Melihat seluruh log.

## 5.2 Warga Belum Disetujui

Warga yang baru melakukan registrasi memiliki status pengajuan akun dan belum memiliki akses login.

Warga belum disetujui dapat:

- Mengisi registrasi.
- Mendapatkan kode registrasi.
- Melihat status registrasi.
- Melihat alasan penolakan apabila pengajuan ditolak.

## 5.3 Warga Aktif

Warga aktif adalah warga yang pengajuan akunnya telah disetujui.

Warga aktif dapat:

- Login.
- Melihat profil.
- Melihat jenis surat yang tersedia.
- Mengunduh template surat.
- Mengunggah dokumen.
- Mengajukan surat.
- Melihat riwayat pengajuan.
- Melihat status dan timeline approval.
- Mengunggah ulang dokumen jika diminta.
- Mengunduh dokumen final.

## 5.4 Warga dengan Jabatan

Warga dengan jabatan merupakan warga aktif yang memiliki satu atau lebih jabatan dalam periode aktif.

Selain hak warga biasa, pengguna ini dapat memperoleh hak untuk:

- Mengakses pengajuan pembuatan akun.
- Menyetujui atau menolak pembuatan akun.
- Mengelola jenis surat tertentu.
- Melihat permintaan approval surat.
- Mengunduh dokumen untuk ditandatangani.
- Mengunggah hasil tanda tangan atau stempel.
- Memberikan keputusan terhadap pengajuan surat.

---

# 6. Teknologi

## 6.1 Backend

- Laravel
- PHP
- Laravel Breeze
- Livewire v4
- MySQL

## 6.2 Frontend

- Livewire v4
- `wire:navigate`
- Livewire loading state
- Tailwind CSS
- Komponen UI bergaya shadcn
- Alpine.js apabila diperlukan

## 6.3 Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sides
DB_USERNAME=guna
DB_PASSWORD=gunapwd
```

Ketentuan:

- File `.env` tidak boleh masuk repository.
- Password database tidak boleh ditulis pada dokumentasi publik.
- Konfigurasi production harus menggunakan kredensial berbeda.

---

# 7. Konfigurasi Awal Sistem

## 7.1 Kondisi Aplikasi Baru

Saat aplikasi pertama kali dipasang:

1. Sistem membuat akun Admin awal melalui seeder atau instalasi.
2. Admin login.
3. Admin menambahkan data desa atau kelurahan.
4. Registrasi warga baru aktif setelah minimal satu desa aktif tersedia.

## 7.2 Kondisi Belum Ada Desa

Jika belum ada desa aktif:

- Halaman registrasi tetap dapat dibuka.
- Form registrasi dinonaktifkan.
- Sistem menampilkan pesan bahwa registrasi belum tersedia.

Pesan yang direkomendasikan:

> Registrasi belum tersedia karena data desa belum dikonfigurasi oleh administrator.

---

# 8. Struktur Halaman

## 8.1 Landing Page

Landing page dapat diakses tanpa login.

Konten minimal:

- Logo dan nama desa.
- Navbar.
- Hero section.
- Deskripsi layanan desa.
- Daftar layanan surat.
- Alur registrasi dan pengajuan surat.
- Informasi kontak.
- Alamat kantor.
- Jam pelayanan.
- Tombol Login.
- Tombol Registrasi.
- Footer.

## 8.2 Login Page

Field:

- Email.
- Password.
- Ingat saya, opsional.
- Tombol Masuk.
- Link Registrasi.
- Link Cek Status Registrasi.
- Lupa password, opsional.

Redirect:

- Admin ke Dashboard Admin.
- Warga ke Dashboard Warga.
- Warga dengan jabatan tetap ke Dashboard Warga dengan menu tambahan.

## 8.3 Registration Page

Field:

- Nama lengkap.
- NIK.
- Email.
- Provinsi.
- Kabupaten/Kota.
- Kecamatan.
- Desa/Kelurahan.
- Upload KTP.
- Password.
- Konfirmasi password.
- Persetujuan kebenaran data.
- Tombol Registrasi.

## 8.4 Admin Page

Menu utama:

- Dashboard.
- Data Desa.
- Pengajuan Pembuatan Akun.
- User.
- Jabatan.
- Data Warga.
- Hak Akses Jabatan.
- Jenis Surat.
- Seluruh Pengajuan Surat.
- Log Aktivitas.
- Profil.
- Logout.

## 8.5 Warga Page

Menu utama:

- Dashboard.
- Ajukan Surat.
- Riwayat Pengajuan.
- Profil.
- Notifikasi.
- Logout.

Menu tambahan berdasarkan jabatan dan permission:

- Pengajuan Pembuatan Akun.
- Permintaan Approval Surat.
- Riwayat Approval.
- Kelola Jenis Surat.

---

# 9. Matriks Hak Akses

| Fitur | Admin | Warga Pending | Warga Aktif | Pejabat Berizin |
|---|---:|---:|---:|---:|
| Landing page | Ya | Ya | Ya | Ya |
| Registrasi | Tidak relevan | Ya | Tidak diperlukan | Tidak diperlukan |
| Cek status registrasi | Tidak relevan | Ya | Tidak relevan | Tidak relevan |
| Login | Ya | Tidak | Ya | Ya |
| Kelola data desa | Ya | Tidak | Tidak | Tidak |
| Kelola user | Ya | Tidak | Tidak | Tidak |
| Kelola jabatan | Ya | Tidak | Tidak | Tidak |
| Kelola data warga | Ya | Tidak | Tidak | Opsional sesuai permission |
| Lihat pengajuan akun | Ya | Tidak | Tidak | Sesuai permission |
| Approve akun | Ya | Tidak | Tidak | Sesuai permission |
| Reject akun | Ya | Tidak | Tidak | Sesuai permission |
| Kelola semua jenis surat | Ya | Tidak | Tidak | Tidak |
| Kelola jenis surat tertentu | Ya | Tidak | Tidak | Sesuai permission |
| Ajukan surat | Opsional | Tidak | Ya | Ya |
| Lihat pengajuan sendiri | Tidak relevan | Tidak | Ya | Ya |
| Lihat seluruh pengajuan | Ya | Tidak | Tidak | Tidak |
| Approval surat | Sesuai kebijakan | Tidak | Tidak | Berdasarkan tahapan |
| Lihat log | Ya | Tidak | Pengajuan sendiri | Sesuai cakupan |

---

# 10. Modul Data Desa

## 10.1 Data yang Dikelola

- Nama desa/kelurahan.
- Kode desa, opsional.
- Provinsi.
- Kode provinsi.
- Kabupaten/Kota.
- Kode kabupaten/kota.
- Kecamatan.
- Kode kecamatan.
- Alamat kantor.
- Kode pos.
- Nomor telepon.
- Email.
- Website, opsional.
- Kop desa.
- Stempel desa.
- Logo desa.
- Nama kepala desa.
- Status aktif.

## 10.2 Fitur

- Tambah data desa.
- Lihat daftar desa.
- Lihat detail desa.
- Ubah data desa.
- Aktifkan/nonaktifkan desa.
- Upload logo.
- Upload kop surat.
- Upload stempel.
- Preview file.
- Hapus atau ganti file.

## 10.3 Aturan Bisnis

- Registrasi hanya dapat memilih desa aktif.
- Nama desa boleh sama jika wilayah berbeda.
- Kombinasi Provinsi, Kabupaten/Kota, Kecamatan, dan nama desa harus unik.
- Data desa tidak boleh dihapus permanen jika sudah digunakan.
- File lama dapat disimpan sebagai riwayat atau dihapus setelah file baru berhasil disimpan.

---

# 11. Modul Registrasi Warga

## 11.1 Alur Registrasi

1. Warga membuka halaman registrasi.
2. Warga mengisi nama lengkap.
3. Warga mengisi NIK.
4. Warga mengisi email.
5. Warga memilih Provinsi.
6. Sistem menampilkan Kabupaten/Kota berdasarkan Provinsi.
7. Warga memilih Kabupaten/Kota.
8. Sistem menampilkan Kecamatan berdasarkan Kabupaten/Kota.
9. Warga memilih Kecamatan.
10. Sistem mencari desa yang sudah tersedia di SIDES berdasarkan tiga data wilayah tersebut.
11. Warga memilih desa.
12. Jika desa ditemukan, field KTP dan password diaktifkan.
13. Warga mengunggah KTP.
14. Warga mengisi password dan konfirmasi password.
15. Warga menyetujui pernyataan kebenaran data.
16. Warga menekan tombol Registrasi.
17. Sistem melakukan validasi.
18. Sistem membuat pengajuan registrasi.
19. Status menjadi `menunggu_approval`.
20. Sistem membuat kode registrasi unik.
21. Warga belum dapat login.

## 11.2 Kondisi Desa Tidak Ditemukan

Jika desa tidak tersedia:

- Warga tidak dapat melanjutkan registrasi.
- Upload KTP dinonaktifkan.
- Password dinonaktifkan.
- Tombol Registrasi dinonaktifkan.

Pesan:

> Desa atau kelurahan Anda belum tersedia dalam sistem. Silakan hubungi pemerintah desa atau administrator terkait.

## 11.3 Status Registrasi

- `menunggu_approval`
- `disetujui`
- `ditolak`
- `dibatalkan`

## 11.4 Halaman Berhasil Registrasi

Menampilkan:

- Kode registrasi.
- Nama pemohon.
- Desa yang dipilih.
- Tanggal registrasi.
- Status Menunggu Approval.
- Informasi bahwa user belum dapat login.

## 11.5 Cek Status Registrasi

Pencarian menggunakan kombinasi:

- Kode registrasi.
- NIK atau email.

Informasi yang ditampilkan:

- Kode registrasi.
- Nama lengkap.
- Desa tujuan.
- Tanggal registrasi.
- Status.
- Tanggal keputusan.
- Catatan petugas.
- Alasan penolakan jika ada.

KTP tidak boleh ditampilkan pada halaman publik.

---

# 12. Modul Approval Pembuatan Akun

## 12.1 Pengguna yang Dapat Mengakses

- Admin.
- Warga dengan jabatan aktif yang memiliki permission.

## 12.2 Permission yang Direkomendasikan

- `registrasi-akun.view`
- `registrasi-akun.detail`
- `registrasi-akun.approve`
- `registrasi-akun.reject`
- `registrasi-akun.download-ktp`

## 12.3 Daftar Pengajuan Akun

Kolom:

- Kode registrasi.
- Nama lengkap.
- NIK tersamarkan.
- Email tersamarkan.
- Desa.
- Wilayah.
- Tanggal registrasi.
- Status.
- Petugas pemeriksa.
- Tanggal keputusan.
- Aksi.

Filter:

- Status.
- Desa.
- Provinsi.
- Kabupaten/Kota.
- Kecamatan.
- Rentang tanggal.
- Petugas pemeriksa.

Pencarian:

- Kode registrasi.
- Nama.
- NIK.
- Email.

## 12.4 Detail Pengajuan

Menampilkan:

- Identitas pemohon.
- Wilayah.
- Desa pilihan.
- KTP.
- Informasi file.
- Riwayat pengajuan.
- Catatan petugas.
- Tombol Setujui.
- Tombol Tolak.

## 12.5 Proses Persetujuan

Ketika pengajuan disetujui:

1. Sistem memastikan status masih `menunggu_approval`.
2. Sistem mengunci row pengajuan.
3. Sistem memeriksa NIK belum digunakan warga aktif.
4. Sistem memeriksa email belum digunakan user aktif.
5. Sistem membuat data warga.
6. Sistem membuat user dengan role `warga`.
7. Sistem menghubungkan user ke warga dan desa.
8. Sistem menetapkan akun aktif.
9. Status registrasi menjadi `disetujui`.
10. Sistem mencatat petugas dan waktu persetujuan.
11. Sistem membuat log.
12. User dapat login.

Semua langkah harus berada dalam satu database transaction.

## 12.6 Proses Penolakan

Ketika ditolak:

- Alasan penolakan wajib diisi.
- Status menjadi `ditolak`.
- User tidak dibuat atau tidak diaktifkan.
- Data warga aktif tidak dibuat.
- Log penolakan dibuat.

Contoh alasan:

- KTP tidak terbaca.
- NIK tidak sesuai.
- Pemohon bukan warga desa tersebut.
- Data sudah terdaftar.
- Dokumen tidak valid.

## 12.7 Pembatasan Akses Pejabat

Pejabat hanya dapat memproses pengajuan apabila:

- Akun aktif.
- Memiliki data warga.
- Memiliki jabatan aktif.
- Periode jabatan masih berlaku.
- Memiliki permission.
- Berada pada desa yang sama dengan pemohon.

---

# 13. Modul Pengelolaan User

## 13.1 Data User

- Desa.
- Warga terkait.
- Nama lengkap.
- Email.
- Password.
- Role.
- Status akun.
- Waktu approval.
- Disetujui oleh.

## 13.2 Fitur

- Tambah user.
- Lihat daftar.
- Lihat detail.
- Ubah nama dan email.
- Ubah password.
- Aktifkan/nonaktifkan akun.
- Suspend akun.
- Soft delete.
- Hubungkan user dengan warga.

## 13.3 Aturan Bisnis

- Email harus unik.
- Password wajib di-hash.
- Role utama hanya `admin` dan `warga`.
- User warga maksimal terhubung dengan satu data warga aktif.
- User yang memiliki riwayat transaksi tidak boleh dihapus permanen.
- User nonaktif tidak dapat login.

---

# 14. Modul Pengelolaan Jabatan

## 14.1 Data Jabatan

- Desa.
- Nama jabatan.
- Kode jabatan.
- Deskripsi.
- Urutan tampilan.
- Status aktif.

Contoh:

- Kepala Desa.
- Sekretaris Desa.
- Kepala Seksi Pelayanan.
- Kepala Dusun.
- Ketua RW.
- Ketua RT.
- Operator Desa.

## 14.2 Fitur

- CRUD jabatan.
- Aktifkan/nonaktifkan.
- Urutkan jabatan.
- Cari dan filter.
- Atur permission.

## 14.3 Aturan Bisnis

- Nama jabatan unik dalam satu desa.
- Jabatan yang digunakan tidak boleh dihapus permanen.
- Jabatan bukan role autentikasi.
- Hak akses jabatan bergantung periode aktif.

---

# 15. Modul Pengelolaan Warga

## 15.1 Data Warga

- Desa.
- Jenis warga.
- NIK.
- Nomor KK, opsional.
- Nama lengkap.
- Tempat lahir.
- Tanggal lahir.
- Jenis kelamin.
- Alamat.
- RT.
- RW.
- Telepon.
- Email.
- Status warga.
- User terkait.

## 15.2 Jenis Warga

- Warga biasa.
- Warga dengan jabatan.

## 15.3 Data Jabatan Warga

- Jabatan.
- Tanggal mulai.
- Tanggal selesai.
- Tanda tangan.
- Stempel.
- Nomor SK, opsional.
- File SK, opsional.
- Status.
- Catatan.

## 15.4 Fitur

- CRUD warga.
- Tambah jabatan warga.
- Lihat riwayat jabatan.
- Upload tanda tangan.
- Upload stempel.
- Hubungkan akun user.
- Filter berdasarkan wilayah, status, jenis warga, dan jabatan.

## 15.5 Aturan Bisnis

- NIK harus unik.
- NIK terdiri dari 16 digit.
- Warga dengan jabatan wajib memiliki periode.
- Tanggal selesai tidak boleh sebelum tanggal mulai.
- Hak pejabat hanya aktif dalam periode jabatan.
- Satu warga dapat memiliki beberapa riwayat jabatan.
- Riwayat jabatan tidak boleh dihapus jika sudah digunakan dalam approval.

---

# 16. Modul Hak Akses Jabatan

## 16.1 Tujuan

Admin dapat menentukan fitur apa saja yang dapat diakses oleh jabatan tertentu.

## 16.2 Contoh Permission

- `registrasi-akun.view`
- `registrasi-akun.detail`
- `registrasi-akun.approve`
- `registrasi-akun.reject`
- `registrasi-akun.download-ktp`
- `jenis-surat.view`
- `jenis-surat.create`
- `jenis-surat.update`
- `jenis-surat.delete`
- `pengajuan-surat.approve`
- `pengajuan-surat.download`
- `warga.view`

## 16.3 Aturan

- Permission diberikan kepada jabatan.
- User mendapatkan permission dari jabatan aktifnya.
- Jika user memiliki beberapa jabatan, permission digabungkan.
- Permission berakhir ketika jabatan berakhir.
- Admin tidak bergantung permission jabatan.
- Semua pengecekan harus dilakukan melalui Policy/Gate dan tidak hanya menyembunyikan menu.

---

# 17. Modul Jenis Surat

## 17.1 Data Jenis Surat

- Desa.
- Nama surat.
- Kode surat.
- Kategori.
- Deskripsi.
- Template surat.
- Status aktif.
- Membutuhkan approval atau tidak.
- Jabatan pengelola.
- Daftar jabatan approval.
- Urutan approval.

## 17.2 Fitur

- CRUD jenis surat.
- Upload dan ganti template.
- Download template.
- Aktifkan/nonaktifkan.
- Tentukan jabatan pengelola.
- Tentukan apakah perlu approval.
- Pilih satu atau lebih jabatan approval.
- Atur urutan approval.

## 17.3 Aturan Bisnis

- Template wajib tersedia sebelum surat diaktifkan.
- Template dapat berupa `.doc`, `.docx`, atau `.pdf`.
- Jika approval diperlukan, minimal satu jabatan harus dipilih.
- Urutan approval harus unik.
- Pejabat urutan berikutnya tidak dapat memproses sebelum tahap sebelumnya diterima.
- Konfigurasi approval disalin sebagai snapshot ketika pengajuan dibuat.
- Perubahan konfigurasi tidak mengubah pengajuan lama.

---

# 18. Modul Pengajuan Surat

## 18.1 Alur Pengajuan

1. Warga login.
2. Warga membuka Ajukan Surat.
3. Warga memilih jenis surat.
4. Sistem menampilkan informasi surat dan urutan approval.
5. Warga mengunduh template.
6. Warga mengedit dokumen di perangkatnya.
7. Warga mengunggah dokumen.
8. Warga menambahkan catatan jika diperlukan.
9. Warga menekan Ajukan.
10. Sistem membuat nomor pengajuan.
11. Sistem menyimpan dokumen sebagai versi pertama.
12. Sistem membuat snapshot tahapan approval.
13. Sistem mengaktifkan tahapan pertama.
14. Warga dapat melihat status.

## 18.2 Data Pengajuan

- Nomor pengajuan.
- Desa.
- Jenis surat.
- Warga pemohon.
- Tanggal pengajuan.
- Status.
- Tahapan aktif.
- Catatan pemohon.
- Dokumen terbaru.
- Tanggal selesai.
- Tanggal ditolak.
- Tanggal dibatalkan.

## 18.3 Status Pengajuan

- `draft`
- `diajukan`
- `menunggu_approval`
- `perlu_perbaikan`
- `ditolak`
- `selesai`
- `dibatalkan`

## 18.4 Aturan Bisnis

- Hanya warga aktif yang dapat mengajukan.
- Pengajuan wajib memiliki dokumen.
- Draft dapat diedit.
- Dokumen yang sudah diajukan tidak boleh ditimpa.
- Setiap upload membuat versi baru.
- Pengajuan selesai setelah seluruh approval diterima.
- Tolak menghentikan proses.
- Ulangi mengembalikan tindakan kepada warga.

---

# 19. Modul Approval Surat

## 19.1 Daftar Permintaan Approval

Pejabat melihat pengajuan yang:

- Berasal dari desa yang sama.
- Ditujukan kepada jabatannya.
- Sedang berada pada tahapan aktif.
- Belum diproses.

Informasi:

- Nomor pengajuan.
- Nama warga.
- Jenis surat.
- Tanggal pengajuan.
- Urutan approval.
- Status.
- Dokumen terbaru.
- Riwayat keputusan sebelumnya.

## 19.2 Proses Approval

1. Pejabat membuka detail.
2. Pejabat mengunduh dokumen terbaru.
3. Pejabat menandatangani atau memberi stempel.
4. Pejabat mengunggah dokumen hasil perubahan.
5. Pejabat memilih keputusan.
6. Pejabat mengisi komentar.
7. Sistem menyimpan keputusan, dokumen, dan log.

## 19.3 Status Approval

- `menunggu`
- `aktif`
- `diterima`
- `ditolak`
- `ulangi`
- `dilewati`
- `dibatalkan`

## 19.4 Keputusan Terima

- Dokumen disimpan sebagai versi baru.
- Tahapan aktif menjadi diterima.
- Tahapan berikutnya diaktifkan.
- Dokumen terbaru diteruskan ke pejabat berikutnya.
- Jika tidak ada tahapan berikutnya, pengajuan selesai.

## 19.5 Keputusan Tolak

- Komentar wajib.
- Pengajuan menjadi ditolak.
- Tahapan berikutnya tidak diaktifkan.
- Warga dapat melihat alasan.
- Pengajuan tidak dapat dilanjutkan.

## 19.6 Keputusan Ulangi

- Komentar wajib.
- Pengajuan menjadi perlu perbaikan.
- Tahapan pejabat yang meminta ulang tetap menjadi tahapan aktif.
- Warga mengunduh dokumen terbaru.
- Warga mengunggah versi perbaikan.
- Pengajuan kembali ke pejabat yang meminta ulang.
- Approval sebelumnya tidak diulang.

## 19.7 Contoh Tiga Pejabat

Urutan:

1. Kepala Seksi Pelayanan.
2. Sekretaris Desa.
3. Kepala Desa.

Skenario:

- Pejabat 1 menerima dan mengunggah versi 2.
- Pejabat 2 menerima dan mengunggah versi 3.
- Pejabat 3 memilih Ulangi.
- Warga mengunduh versi 3.
- Warga mengunggah versi 4.
- Pengajuan kembali ke Pejabat 3.
- Pejabat 1 dan 2 tetap berstatus diterima.

---

# 20. Manajemen Versi Dokumen

## 20.1 Jenis Dokumen

- Template.
- Dokumen awal warga.
- Dokumen hasil pejabat.
- Dokumen upload ulang warga.
- Dokumen final.

## 20.2 Data Versi

- Pengajuan.
- Nomor versi.
- Nama file asli.
- Path file.
- Ekstensi.
- MIME type.
- Ukuran file.
- Sumber upload.
- User pengunggah.
- Tahapan approval.
- Keterangan.
- Waktu upload.
- Penanda dokumen terbaru.
- Hash file, opsional.

## 20.3 Aturan

- File lama tidak boleh ditimpa.
- Hanya satu dokumen yang ditandai sebagai terbaru.
- Dokumen terbaru digunakan pada proses berikutnya.
- File disimpan pada private storage.
- Download harus melalui controller dan authorization.

---

# 21. Status dan State Transition

## 21.1 Registrasi Akun

```text
menunggu_approval -> disetujui
menunggu_approval -> ditolak
menunggu_approval -> dibatalkan
```

## 21.2 Pengajuan Surat

```text
draft -> diajukan
diajukan -> menunggu_approval
menunggu_approval -> menunggu_approval
menunggu_approval -> perlu_perbaikan
menunggu_approval -> ditolak
menunggu_approval -> selesai
perlu_perbaikan -> menunggu_approval
draft -> dibatalkan
menunggu_approval -> dibatalkan
```

## 21.3 Aturan Transition

- Transition harus divalidasi server-side.
- Transition tidak valid harus ditolak.
- Setiap transition membuat log.
- Perubahan status dan log dilakukan dalam satu transaction.

---

# 22. Log dan Audit Trail

## 22.1 Aktivitas Registrasi yang Dicatat

- Registrasi dikirim.
- KTP diunggah.
- Pengajuan dilihat petugas.
- Pengajuan disetujui.
- Pengajuan ditolak.
- User dibuat.
- Data warga dibuat.
- Akun diaktifkan.

## 22.2 Aktivitas Pengajuan Surat yang Dicatat

- Draft dibuat.
- Dokumen diunggah.
- Pengajuan dikirim.
- Approval diaktifkan.
- Approval diterima.
- Approval ditolak.
- Approval meminta perbaikan.
- Warga mengunggah ulang.
- Pengajuan selesai.
- Pengajuan dibatalkan.
- Status diubah Admin.

## 22.3 Data Log

- Modul.
- ID referensi.
- User pelaku.
- Aksi.
- Status sebelum.
- Status sesudah.
- Komentar.
- Metadata.
- IP address.
- User agent.
- Timestamp.

## 22.4 Aturan

- Log tidak dapat diubah pengguna biasa.
- Log tidak dapat dihapus dari UI.
- Log dibuat pada transaksi yang sama dengan aksi utama.
- Perubahan manual Admin harus menyimpan alasan.

---

# 23. Notifikasi

## 23.1 Notifikasi Admin dan Pejabat

- Registrasi akun baru.
- Pengajuan surat baru.
- Tahapan approval aktif.
- Warga mengunggah ulang.
- Pengajuan dibatalkan.

## 23.2 Notifikasi Warga

- Registrasi diterima sistem.
- Registrasi disetujui.
- Registrasi ditolak.
- Surat berhasil diajukan.
- Surat diterima pejabat.
- Surat diteruskan ke pejabat berikutnya.
- Surat perlu diperbaiki.
- Surat ditolak.
- Surat selesai.

## 23.3 Data Notifikasi

- User penerima.
- Judul.
- Pesan.
- Tipe.
- URL tujuan.
- Status dibaca.
- Waktu dibaca.
- Waktu dibuat.

---

# 24. Dashboard

## 24.1 Dashboard Admin

- Total desa.
- Total warga.
- Total warga dengan jabatan.
- Total user aktif.
- Registrasi menunggu approval.
- Total jenis surat.
- Pengajuan surat hari ini.
- Pengajuan menunggu approval.
- Pengajuan perlu perbaikan.
- Pengajuan selesai.
- Pengajuan ditolak.
- Grafik pengajuan bulanan.
- Aktivitas terbaru.

## 24.2 Dashboard Warga

- Total pengajuan.
- Pengajuan aktif.
- Perlu perbaikan.
- Selesai.
- Ditolak.
- Jenis surat tersedia.
- Pengajuan terbaru.
- Notifikasi tindakan.

## 24.3 Dashboard Pejabat

- Registrasi akun menunggu pemeriksaan.
- Permintaan approval surat aktif.
- Approval diterima.
- Approval ditolak.
- Approval ulangi.
- Daftar tindakan terbaru.

---

# 25. Struktur Data dan Tabel

## 25.1 `desas`

- `id`
- `nama`
- `kode`
- `provinsi`
- `kode_provinsi`
- `kabupaten`
- `kode_kabupaten`
- `kecamatan`
- `kode_kecamatan`
- `alamat`
- `kode_pos`
- `telepon`
- `email`
- `website`
- `logo_path`
- `kop_path`
- `stempel_path`
- `nama_kepala_desa`
- `is_active`
- `created_at`
- `updated_at`
- `deleted_at`

## 25.2 `registrasi_akuns`

- `id`
- `kode_registrasi`
- `desa_id`
- `nama_lengkap`
- `nik`
- `email`
- `provinsi`
- `kode_provinsi`
- `kabupaten`
- `kode_kabupaten`
- `kecamatan`
- `kode_kecamatan`
- `password`
- `ktp_path`
- `ktp_nama_asli`
- `ktp_mime_type`
- `ktp_size`
- `status`
- `catatan_petugas`
- `diproses_oleh`
- `diproses_pada`
- `user_id`
- `warga_id`
- `ip_address`
- `user_agent`
- `created_at`
- `updated_at`
- `deleted_at`

## 25.3 `registrasi_akun_logs`

- `id`
- `registrasi_akun_id`
- `user_id`
- `action`
- `status_sebelum`
- `status_sesudah`
- `catatan`
- `metadata`
- `ip_address`
- `user_agent`
- `created_at`

## 25.4 `users`

- `id`
- `desa_id`
- `warga_id`
- `name`
- `email`
- `password`
- `role`
- `status`
- `is_active`
- `approved_at`
- `approved_by`
- `email_verified_at`
- `remember_token`
- `created_at`
- `updated_at`
- `deleted_at`

## 25.5 `jabatans`

- `id`
- `desa_id`
- `nama`
- `kode`
- `deskripsi`
- `urutan`
- `is_active`
- `created_at`
- `updated_at`
- `deleted_at`

## 25.6 `permissions`

- `id`
- `name`
- `label`
- `group`
- `created_at`
- `updated_at`

## 25.7 `jabatan_permissions`

- `id`
- `jabatan_id`
- `permission_id`
- `created_by`
- `created_at`
- `updated_at`

## 25.8 `wargas`

- `id`
- `desa_id`
- `nik`
- `no_kk`
- `nama`
- `tempat_lahir`
- `tanggal_lahir`
- `jenis_kelamin`
- `alamat`
- `rt`
- `rw`
- `telepon`
- `email`
- `jenis_warga`
- `status`
- `created_at`
- `updated_at`
- `deleted_at`

## 25.9 `warga_jabatans`

- `id`
- `warga_id`
- `jabatan_id`
- `tanggal_mulai`
- `tanggal_selesai`
- `tanda_tangan_path`
- `stempel_path`
- `nomor_sk`
- `file_sk_path`
- `status`
- `catatan`
- `created_at`
- `updated_at`
- `deleted_at`

## 25.10 `jenis_surats`

- `id`
- `desa_id`
- `nama`
- `kode`
- `kategori`
- `deskripsi`
- `template_path`
- `butuh_approval`
- `is_active`
- `created_by`
- `updated_by`
- `created_at`
- `updated_at`
- `deleted_at`

## 25.11 `jenis_surat_pengelolas`

- `id`
- `jenis_surat_id`
- `jabatan_id`
- `created_at`
- `updated_at`

## 25.12 `jenis_surat_approvals`

- `id`
- `jenis_surat_id`
- `jabatan_id`
- `urutan`
- `wajib`
- `created_at`
- `updated_at`

## 25.13 `pengajuan_surats`

- `id`
- `nomor_pengajuan`
- `desa_id`
- `jenis_surat_id`
- `warga_id`
- `status`
- `tahapan_aktif`
- `catatan_pemohon`
- `submitted_at`
- `completed_at`
- `rejected_at`
- `cancelled_at`
- `created_by`
- `created_at`
- `updated_at`
- `deleted_at`

## 25.14 `pengajuan_approvals`

- `id`
- `pengajuan_surat_id`
- `jabatan_id`
- `warga_jabatan_id`
- `nama_jabatan_snapshot`
- `nama_pejabat_snapshot`
- `urutan`
- `status`
- `komentar`
- `dokumen_versi_id`
- `activated_at`
- `processed_at`
- `processed_by`
- `created_at`
- `updated_at`

## 25.15 `pengajuan_dokumens`

- `id`
- `pengajuan_surat_id`
- `pengajuan_approval_id`
- `versi`
- `nama_file_asli`
- `file_path`
- `file_extension`
- `mime_type`
- `file_size`
- `sumber`
- `uploaded_by`
- `keterangan`
- `is_latest`
- `created_at`
- `updated_at`

## 25.16 `pengajuan_logs`

- `id`
- `pengajuan_surat_id`
- `pengajuan_approval_id`
- `user_id`
- `action`
- `status_sebelum`
- `status_sesudah`
- `komentar`
- `metadata`
- `ip_address`
- `user_agent`
- `created_at`

## 25.17 `notifications`

Menggunakan tabel notifikasi Laravel atau tabel khusus.

---

# 26. Relasi Data

- Satu desa memiliki banyak user.
- Satu desa memiliki banyak warga.
- Satu desa memiliki banyak jabatan.
- Satu desa memiliki banyak jenis surat.
- Satu registrasi akun terkait satu desa.
- Satu registrasi akun menghasilkan maksimal satu user dan satu warga.
- Satu warga memiliki maksimal satu akun aktif.
- Satu warga dapat memiliki banyak riwayat jabatan.
- Satu jabatan memiliki banyak permission.
- Satu jenis surat dapat dikelola banyak jabatan.
- Satu jenis surat memiliki banyak tahapan approval.
- Satu warga dapat memiliki banyak pengajuan surat.
- Satu pengajuan memiliki banyak approval.
- Satu pengajuan memiliki banyak versi dokumen.
- Satu pengajuan memiliki banyak log.

---

# 27. Aturan Bisnis

1. Registrasi tidak tersedia sebelum ada desa aktif.
2. Desa dipilih berdasarkan Provinsi, Kabupaten/Kota, dan Kecamatan.
3. Hanya desa yang tersimpan di SIDES yang dapat dipilih.
4. Akun tidak langsung aktif setelah registrasi.
5. User baru dapat login setelah disetujui.
6. NIK dan email tidak boleh memiliki pengajuan aktif ganda.
7. Admin dapat menentukan jabatan yang dapat memproses registrasi.
8. Pejabat hanya dapat memproses data dari desa yang sama.
9. Jabatan harus aktif dan periodenya berlaku.
10. Penolakan registrasi wajib memiliki alasan.
11. Password tidak pernah dapat dilihat Admin atau pejabat.
12. Data KTP disimpan secara privat.
13. Jenis surat dapat dikelola Admin dan jabatan yang diberikan izin.
14. Approval surat berjalan sesuai urutan.
15. Pejabat berikutnya menerima dokumen terakhir dari pejabat sebelumnya.
16. Setiap upload menghasilkan versi baru.
17. Keputusan Ulangi hanya mengulang dari pejabat yang meminta perbaikan.
18. Approval pejabat sebelumnya tetap sah.
19. Tolak menghentikan proses.
20. Semua perubahan status harus membuat log.
21. Proses kritis harus menggunakan database transaction.
22. Aksi approval harus menggunakan row locking untuk mencegah proses ganda.
23. Perubahan konfigurasi approval tidak mengubah pengajuan lama.
24. Pengalihan pejabat oleh Admin wajib tercatat dalam log.

---

# 28. Validasi

## 28.1 Registrasi

### Nama Lengkap

- Wajib.
- Minimal 3 karakter.
- Maksimal 150 karakter.

### NIK

- Wajib.
- Tepat 16 digit.
- Hanya angka.
- Unik pada warga aktif.
- Tidak boleh memiliki pengajuan pending lain.

### Email

- Wajib.
- Format valid.
- Maksimal 255 karakter.
- Unik pada user aktif.
- Tidak boleh memiliki pengajuan pending lain.

### Password

- Wajib.
- Minimal 8 karakter.
- Harus sama dengan konfirmasi.
- Disimpan dengan hash Laravel.

### KTP

Format:

- `.jpg`
- `.jpeg`
- `.png`
- `.webp`
- `.pdf`

Maksimal 5 MB.

## 28.2 Template dan Dokumen Surat

Format:

- `.doc`
- `.docx`
- `.pdf`

Maksimal 10 MB.

## 28.3 Logo, Tanda Tangan, dan Stempel

Format:

- `.png`
- `.jpg`
- `.jpeg`
- `.webp`

Maksimal 2 MB.

## 28.4 Aturan Umum File

- Validasi MIME type.
- Nama file menggunakan UUID.
- Nama asli disimpan sebagai metadata.
- File disimpan pada private disk.
- SVG tidak diperbolehkan pada MVP.

---

# 29. Keamanan

Sistem harus menerapkan:

- Laravel authentication.
- Laravel Breeze.
- Password hashing.
- CSRF protection.
- Session regeneration setelah login.
- Rate limiting login dan registrasi.
- Policy dan Gate.
- Middleware role.
- Authorization berdasarkan desa.
- Authorization berdasarkan jabatan aktif.
- Authorization berdasarkan kepemilikan data.
- Private file storage.
- Download melalui controller.
- Validasi MIME type.
- Pencegahan IDOR.
- Escape output.
- Soft delete data master.
- Audit log.
- Database transaction.
- Row locking untuk approval.
- Penyembunyian sebagian NIK dan email pada tabel daftar.
- Pembatasan percobaan cek status registrasi.

---

# 30. Kebutuhan Nonfungsional

## 30.1 Performa

- Halaman utama dimuat kurang dari 3 detik pada koneksi normal.
- Daftar menggunakan pagination.
- Query menggunakan eager loading.
- Kolom pencarian diberi index.
- Dashboard menggunakan query agregasi efisien.

Index penting:

- `nik`
- `email`
- `kode_registrasi`
- `nomor_pengajuan`
- `desa_id`
- `warga_id`
- `jabatan_id`
- `jenis_surat_id`
- `status`
- `created_at`

## 30.2 Responsif

- Desktop.
- Tablet.
- Smartphone.

## 30.3 Reliabilitas

- Perubahan status bersifat atomik.
- Kegagalan upload tidak mengubah status.
- Kegagalan log membatalkan transaksi utama.
- Backup database dan storage dilakukan berkala.

## 30.4 Kompatibilitas Browser

- Google Chrome.
- Microsoft Edge.
- Mozilla Firefox.
- Safari.

## 30.5 Maintainability

- Gunakan service class untuk proses approval.
- Gunakan enum untuk status.
- Gunakan policy untuk authorization.
- Gunakan form request atau validasi Livewire.
- Gunakan event/listener untuk notifikasi.
- Gunakan activity log terpisah.
- Gunakan test untuk alur kritis.

---

# 31. Komponen UI/UX

- Sidebar responsif.
- Top navbar.
- Breadcrumb.
- Data table.
- Pagination.
- Search input.
- Filter dropdown.
- Date range picker.
- Select searchable.
- Cascading select wilayah.
- File uploader.
- File preview.
- Modal konfirmasi.
- Alert dialog.
- Toast notification.
- Status badge.
- Timeline approval.
- Stepper approval.
- Dashboard cards.
- Empty state.
- Loading skeleton.
- `wire:loading` indicator.
- Disable submit saat proses berjalan.
- Activity log panel.
- Tabs detail.
- Mobile drawer.

Status badge harus konsisten:

- Menunggu: netral.
- Diproses: informasi.
- Disetujui/Selesai: sukses.
- Perlu Perbaikan/Ulangi: peringatan.
- Ditolak: bahaya.

---

# 32. Kriteria Penerimaan

## 32.1 Data Desa

- Admin dapat menambahkan desa.
- Registrasi aktif setelah ada desa aktif.
- Desa nonaktif tidak muncul pada registrasi.
- Logo, kop, dan stempel dapat diunggah.

## 32.2 Registrasi

- Dropdown wilayah berjalan berurutan.
- Desa dicari berdasarkan wilayah.
- Registrasi tidak dapat dilanjutkan jika desa tidak ada.
- NIK 16 digit wajib.
- Email valid wajib.
- KTP wajib.
- Password dan konfirmasi harus sama.
- Registrasi menghasilkan kode unik.
- Status awal Menunggu Approval.
- User belum dapat login.

## 32.3 Approval Akun

- Pengajuan muncul di console Admin.
- Pengajuan muncul pada pejabat yang memiliki permission.
- Pejabat tanpa permission ditolak.
- Approve membuat warga dan user.
- User aktif setelah approve.
- Reject wajib memiliki alasan.
- Seluruh aksi tercatat dalam log.
- Approval ganda tidak membuat data duplikat.

## 32.4 User dan Warga

- Email user unik.
- NIK warga unik.
- User nonaktif tidak dapat login.
- Jabatan memiliki periode valid.
- Hak jabatan berakhir setelah periode selesai.

## 32.5 Jenis Surat

- Template dapat diunggah.
- Approval dapat diaktifkan atau dinonaktifkan.
- Urutan pejabat dapat diatur.
- Perubahan urutan tidak mengubah pengajuan lama.

## 32.6 Pengajuan Surat

- Warga dapat download template.
- Warga dapat upload dokumen.
- Pengajuan tidak dapat dikirim tanpa dokumen.
- Nomor pengajuan unik.
- Warga hanya melihat pengajuan sendiri.
- Snapshot approval dibuat ketika submit.

## 32.7 Approval Surat

- Hanya pejabat aktif yang dapat memproses.
- Hanya tahapan aktif yang dapat diproses.
- Terima mengaktifkan pejabat berikutnya.
- Tolak menghentikan proses.
- Ulangi mengembalikan ke warga.
- Approval sebelumnya tidak diulang.
- Dokumen terbaru diteruskan ke tahapan berikutnya.

## 32.8 Log

- Setiap status berubah menghasilkan log.
- Log mencatat user, waktu, status lama, status baru, dan komentar.
- Log tidak dapat diubah pengguna biasa.

---

# 33. Skenario Pengujian Utama

## 33.1 Belum Ada Desa

1. Warga membuka registrasi.
2. Sistem tidak menemukan desa aktif.
3. Form dinonaktifkan.
4. Pesan informasi muncul.

## 33.2 Registrasi Berhasil

1. Warga mengisi semua data.
2. Desa ditemukan.
3. KTP valid.
4. Registrasi disimpan.
5. Status Menunggu Approval.
6. Kode registrasi ditampilkan.
7. Warga belum dapat login.

## 33.3 Registrasi Disetujui Admin

1. Admin membuka detail.
2. Admin memeriksa KTP.
3. Admin menyetujui.
4. Warga dan user dibuat.
5. Akun aktif.
6. User dapat login.

## 33.4 Registrasi Disetujui Pejabat

1. Admin memberikan permission kepada Sekretaris Desa.
2. Sekretaris Desa memiliki jabatan aktif.
3. Pengajuan dapat dibuka.
4. Pengajuan disetujui.
5. Log mencatat petugas.

## 33.5 Pejabat Tidak Berizin

1. User memiliki jabatan tetapi tanpa permission.
2. User membuka URL pengajuan akun.
3. Sistem menolak akses.
4. Data KTP tidak ditampilkan.

## 33.6 Pengajuan Akun Ditolak

1. Petugas menemukan KTP tidak valid.
2. Petugas memilih Tolak.
3. Alasan wajib diisi.
4. Status menjadi Ditolak.
5. User tidak dapat login.

## 33.7 Approval Akun Bersamaan

1. Dua petugas membuka pengajuan sama.
2. Petugas pertama menyetujui.
3. Petugas kedua mencoba menyetujui.
4. Sistem menolak karena status sudah berubah.
5. Tidak ada duplikasi warga atau user.

## 33.8 Approval Surat Tiga Pejabat

1. Warga mengajukan dokumen versi 1.
2. Pejabat 1 menerima dan upload versi 2.
3. Pejabat 2 menerima dan upload versi 3.
4. Pejabat 3 meminta ulang.
5. Warga upload versi 4.
6. Pengajuan kembali ke Pejabat 3.
7. Pejabat 1 dan 2 tetap diterima.
8. Pejabat 3 menerima.
9. Pengajuan selesai.

---

# 34. Prioritas Pengembangan MVP

## Tahap 1 — Fondasi

- Instalasi Laravel.
- Konfigurasi MySQL.
- Laravel Breeze.
- Livewire v4.
- Tailwind CSS.
- Layout Admin dan Warga.
- Seeder Admin.
- Middleware role.
- Policy dasar.

## Tahap 2 — Data Desa dan Wilayah

- CRUD desa.
- Data Provinsi.
- Data Kabupaten/Kota.
- Data Kecamatan.
- Pencarian desa berdasarkan wilayah.

## Tahap 3 — Registrasi Publik

- Halaman registrasi.
- Cascading dropdown.
- Upload KTP.
- Validasi NIK dan email.
- Kode registrasi.
- Halaman cek status.

## Tahap 4 — Approval Akun

- Daftar pengajuan akun.
- Detail dan preview KTP.
- Approve.
- Reject.
- Pembuatan warga dan user.
- Aktivasi akun.
- Log.

## Tahap 5 — Jabatan dan Permission

- CRUD jabatan.
- Riwayat jabatan warga.
- Permission berdasarkan jabatan.
- Pembatasan periode.

## Tahap 6 — Jenis Surat

- CRUD jenis surat.
- Upload template.
- Jabatan pengelola.
- Approval berurutan.

## Tahap 7 — Pengajuan Surat

- Download template.
- Draft.
- Upload dokumen.
- Submit.
- Riwayat pengajuan.

## Tahap 8 — Approval Surat

- Inbox approval.
- Download/upload dokumen.
- Terima.
- Tolak.
- Ulangi.
- Approval berurutan.

## Tahap 9 — Versi, Log, dan Notifikasi

- Versi dokumen.
- Timeline.
- Log aktivitas.
- Notifikasi dalam aplikasi.

## Tahap 10 — Dashboard dan Hardening

- Dashboard Admin.
- Dashboard Warga.
- Dashboard Pejabat.
- Pengujian authorization.
- Optimasi mobile.
- Optimasi query.
- Backup dan deployment.

---

# 35. Risiko dan Mitigasi

## 35.1 Tidak Ada Pejabat Aktif

**Risiko:** Pengajuan tidak dapat diproses.  
**Mitigasi:** Validasi sebelum submit dan notifikasi Admin.

## 35.2 File Rusak atau Tidak Valid

**Risiko:** Dokumen tidak dapat dibuka.  
**Mitigasi:** Validasi MIME, ukuran, ekstensi, dan simpan seluruh versi.

## 35.3 Approval Diproses Bersamaan

**Risiko:** Duplikasi atau status tidak konsisten.  
**Mitigasi:** Database transaction, row lock, dan pengecekan status terbaru.

## 35.4 Jabatan Berubah Saat Proses Berjalan

**Risiko:** Pejabat tujuan tidak lagi aktif.  
**Mitigasi:** Snapshot pejabat, fitur pengalihan Admin, dan audit log.

## 35.5 Dokumen Lama Tertimpa

**Risiko:** Riwayat hilang.  
**Mitigasi:** Versioning, UUID file, dan larangan overwrite.

## 35.6 Kebocoran KTP

**Risiko:** Data pribadi dapat diakses publik.  
**Mitigasi:** Private storage, authorization download, dan masking identitas.

## 35.7 Duplikasi NIK atau Email

**Risiko:** Satu warga memiliki beberapa akun.  
**Mitigasi:** Unique constraint dan validasi ulang saat approval.

---

# 36. Asumsi Produk

- Role autentikasi hanya Admin dan Warga.
- Jabatan bukan role.
- Satu warga memiliki maksimal satu akun aktif.
- Warga mengedit dokumen di aplikasi eksternal.
- Pejabat menandatangani dokumen di aplikasi eksternal.
- Sistem tidak menempelkan tanda tangan otomatis pada MVP.
- Pengajuan yang ditolak harus dibuat ulang.
- Ulangi hanya mengulang dari tahapan yang meminta perbaikan.
- Approval sebelumnya tetap sah.
- Sistem dapat dikembangkan menjadi multi-desa.

---

# 37. Di Luar Ruang Lingkup MVP

- Integrasi Dukcapil.
- OCR otomatis KTP.
- Verifikasi biometrik.
- Tanda tangan elektronik tersertifikasi.
- Editor Word di browser.
- Pembayaran.
- WhatsApp Gateway.
- SMS Gateway.
- Aplikasi mobile native.
- Integrasi SSO pemerintah.

---

# 38. Indikator Keberhasilan

Produk dianggap berhasil apabila:

- Admin dapat menambahkan desa.
- Warga dapat registrasi setelah desa tersedia.
- Pengajuan akun dapat disetujui Admin atau pejabat berizin.
- User hanya dapat login setelah disetujui.
- Warga dapat mengajukan surat.
- Pejabat dapat memproses approval berurutan.
- Warga dapat melihat posisi pengajuan.
- Tidak ada dokumen yang tertimpa.
- Setiap perubahan status memiliki log.
- Dokumen sensitif tidak dapat diakses publik.
- Sistem berjalan baik pada desktop dan mobile.

---

# 39. Definition of Done

Sebuah fitur dianggap selesai apabila:

- Sesuai kebutuhan fungsional.
- Memiliki validasi server-side.
- Memiliki authorization.
- Responsif.
- Memiliki loading state.
- Memiliki empty state dan error state.
- Aktivitas penting tercatat dalam log.
- Memiliki feature test untuk alur kritis.
- Tidak memiliki error kritis.
- Sudah diuji sebagai Admin, Warga, dan Pejabat.
- Sudah diuji untuk Terima, Tolak, dan Ulangi.
- Sudah diuji untuk approval satu, dua, dan tiga pejabat.
- Dokumentasi teknis diperbarui.

---

## Catatan Implementasi Utama

Strategi yang direkomendasikan untuk registrasi adalah menyimpan data pemohon terlebih dahulu pada tabel `registrasi_akuns`. Record `users` dan `wargas` baru dibuat ketika pengajuan disetujui. Dengan cara ini, akun yang belum diverifikasi tidak memenuhi tabel user aktif dan seluruh proses registrasi tetap dapat diaudit.
