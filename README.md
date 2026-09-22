# E-Office

E-Office adalah aplikasi pengelolaan dokumen dan persuratan berbasis web. Sistem ini membantu proses pembuatan, pengajuan, pemeriksaan, persetujuan, pengarsipan, serta verifikasi dokumen secara terstruktur.

> **Status proyek: masih dalam tahap pengembangan**
>
> Fitur, struktur data, dan integrasi dapat berubah sewaktu-waktu. Aplikasi belum dianggap sebagai rilis produksi final sehingga pengujian dan validasi tambahan tetap diperlukan sebelum digunakan pada lingkungan operasional.

## Gambaran Umum

Sistem dirancang untuk mendukung alur administrasi dokumen secara terpusat, antara lain:

- pembuatan dokumen internal dan persuratan;
- pencatatan dokumen masuk dan dokumen keluar;
- pengunggahan serta pengelolaan berkas dokumen;
- disposisi dan tindak lanjut dokumen;
- workflow persetujuan berdasarkan tahapan dan kewenangan;
- penomoran dokumen;
- pembuatan dokumen final dalam format PDF;
- validasi dokumen menggunakan QR code;
- notifikasi dan pencatatan aktivitas;
- penyimpanan dokumen yang telah selesai ke arsip.

## Workflow Sistem

Secara umum, proses dokumen berjalan dengan alur berikut:

1. **Dokumen dibuat**
   - Pengguna mengisi data dokumen sesuai jenisnya.
   - Dokumen dapat dilengkapi metadata dan berkas pendukung.

2. **Dokumen diajukan**
   - Pengguna memilih alur kerja yang sesuai.
   - Sistem memeriksa kesesuaian jenis dokumen dan unit kerja.

3. **Pemeriksaan dan persetujuan**
   - Sistem membuat tugas persetujuan untuk pihak yang berwenang pada tahap aktif.
   - Approver dapat menyetujui, menolak, atau meminta revisi sesuai aturan tahap tersebut.
   - Setiap tindakan dicatat sebagai aktivitas dan dapat memicu notifikasi.

4. **Revisi atau pengajuan ulang**
   - Jika diperlukan revisi, dokumen dikembalikan kepada pembuatnya.
   - Setelah diperbaiki, dokumen dapat diajukan kembali untuk melanjutkan proses.

5. **Penyelesaian dokumen**
   - Setelah seluruh tahap disetujui, sistem memberikan nomor dokumen.
   - Sistem membuat salinan PDF final berdasarkan template dan data dokumen.
   - PDF dilengkapi QR code untuk membantu proses validasi.

6. **Pengarsipan**
   - Dokumen yang selesai ditandai sebagai dokumen final dan disimpan dalam arsip.
   - Jejak aktivitas dan status proses tetap tersedia untuk kebutuhan penelusuran.

## Stack Teknologi

### Backend

- **PHP 8.2 atau lebih baru**
- **Laravel 12**
- **Laravel Eloquent ORM**
- **Laravel Blade** untuk server-side rendering
- **SQLite** sebagai konfigurasi database bawaan pengembangan
- **Laravel Queue** dengan database driver untuk pekerjaan antrean

### Frontend dan asset

- **Vite 7**
- **Tailwind CSS 4**
- **Axios**
- JavaScript dan CSS yang dikelola melalui Laravel Vite Plugin

### Pemrosesan dokumen

- **Dompdf** untuk menghasilkan PDF
- **Endroid QR Code** untuk membuat QR code validasi dokumen
- Layanan penandatanganan saat ini menjadi abstraction boundary dan dapat diintegrasikan dengan provider tersertifikasi pada tahap berikutnya.

### Pengujian dan tooling

- **PHPUnit 11**
- **Laravel Pint**
- **Laravel Sail** (opsional)
- **Concurrently** untuk menjalankan beberapa proses development secara bersamaan

## Struktur Direktori Utama

```text
app/
├── Http/          Controller dan Form Request
├── Models/        Model Eloquent
├── Notifications/ Notifikasi aplikasi
├── Policies/      Aturan otorisasi
└── Services/      Logika workflow, penomoran, PDF, notifikasi, dan aktivitas

database/
├── migrations/    Struktur tabel dan perubahan skema
└── seeders/       Data awal (jika tersedia)

resources/
├── css/           Style aplikasi
├── js/            Entry point JavaScript
└── views/         Template Blade

routes/             Definisi navigasi dan proses aplikasi
storage/            File runtime dan hasil generate aplikasi
tests/              Pengujian otomatis
```
