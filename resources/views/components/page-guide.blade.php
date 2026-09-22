@php
    $route = request()->route()?->getName() ?? '';
    $guides = [
        'dashboard' => [
            'Ringkasan kerja hari ini',
            'Mulai dari kartu Menunggu proses, lalu buka Persetujuan atau Disposisi yang membutuhkan tindakan Anda.',
        ],
        'documents.index' => [
            'Cari dan pantau dokumen',
            'Gunakan filter untuk mempersempit data. Klik Detail untuk melihat file, workflow, riwayat, serta tindakan yang diizinkan.',
        ],
        'documents.create' => [
            'Buat dokumen umum',
            'Simpan sebagai draft terlebih dahulu. Gunakan Surat Tugas atau Surat Keluar bila membutuhkan form khusus dan workflow.',
        ],
        'documents.show' => [
            'Pusat informasi dokumen',
            'Periksa status, workflow, catatan revisi, file, dan timeline. Tombol tindakan hanya muncul bila Anda berhak.',
        ],
        'documents.edit' => [
            'Perbaiki dokumen',
            'Ubah bagian yang diminta, simpan, lalu kembali ke detail untuk submit ulang ke workflow.',
        ],
        'internal-documents.create' => [
            'Isi pengajuan dengan lengkap',
            'Untuk Surat Tugas, pilih workflow wajib agar dokumen masuk ke Approval Saya setelah disimpan.',
        ],
        'outgoing-letters.index' => [
            'Kelola surat keluar',
            'Buat surat baru, pantau statusnya, dan buka detail untuk melihat workflow serta riwayat approval.',
        ],
        'outgoing-letters.create' => [
            'Buat surat keluar',
            'Isi perihal dan isi surat. Pilih workflow bila surat perlu diproses hingga menjadi dokumen resmi.',
        ],
        'approvals.index' => [
            'Proses persetujuan Anda',
            'Setujui melanjutkan alur, Revisi mengembalikan ke pembuat, dan Tolak menghentikan alur.',
        ],
        'dispositions.index' => [
            'Tindak lanjuti disposisi',
            'Proses status secara berurutan: Baca, Proses, lalu Selesai. Gunakan detail dokumen untuk konteks lengkap.',
        ],
        'dispositions.create' => [
            'Kirim disposisi',
            'Pilih minimal satu tujuan: pengguna, unit, atau jabatan. Tulis instruksi yang dapat ditindaklanjuti.',
        ],
        'archives.index' => [
            'Temukan dokumen final',
            'Cari berdasarkan nomor, perihal, tahun, unit, jenis, atau pembuat.',
        ],
        'workflows.index' => [
            'Konfigurasi alur persetujuan',
            'Alur persetujuan menentukan siapa yang memproses dokumen. Gunakan contoh alur sebagai referensi.',
        ],
        'workflows.create' => [
            'Buat alur persetujuan',
            'Pilih jenis dokumen dan isi langkah berurutan. Referensi penyetuju harus sesuai tipe.',
        ],
        'workflows.edit' => [
            'Ubah alur persetujuan',
            'Perubahan berlaku untuk dokumen baru; riwayat persetujuan yang berjalan tetap tersimpan.',
        ],
    ];
    [$title, $message] = $guides[$route] ?? [
        'Petunjuk halaman',
        'Lengkapi informasi yang tersedia, lalu gunakan tombol tindakan untuk melanjutkan proses.',
    ];
@endphp
<aside class="page-guide"
       role="note"><span class="page-guide-icon">i</span>
    <div><strong>{{ $title }}</strong>
        <p>{{ $message }}</p>
    </div>
</aside>
