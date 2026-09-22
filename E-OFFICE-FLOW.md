# Panduan Alur E-Office

Dokumen ini merangkum cara kerja E-Office. Salin bagian **Prompt untuk GPT** bila ingin meminta GPT membuat flowchart, BPMN, sequence diagram, atau ERD.

## Peran utama

| Peran | Tanggung jawab |
|---|---|
| Pembuat dokumen | Membuat draft, memperbaiki revisi, dan submit ulang. |
| Approver | Approve, reject, atau request revision pada step yang ditugaskan. |
| Penerima disposisi | Membaca, memproses, dan menyelesaikan disposisi. |
| Administrator | Mengelola master data, workflow, template, numbering rule, dan permission. |
| Publik | Memverifikasi dokumen final melalui QR/link verifikasi. |

## Peta sistem

~~~mermaid
flowchart TD
    Login[Login + CAPTCHA] --> Dashboard[Dashboard]
    Dashboard --> Inbox[Inbox: Dokumen, Disposisi, Nota Dinas]
    Dashboard --> Outbox[Outbox: Draft dan Surat Keluar]
    Dashboard --> Requests[Pengajuan: Surat Tugas dan lainnya]
    Dashboard --> Approval[Approval Saya]
    Dashboard --> Archive[Arsip Digital]
    Dashboard --> Master[Master Data dan Workflow]
    Inbox --> Disposition[Tracking Disposisi]
    Outbox --> Document[Dokumen]
    Requests --> Document
    Document --> Workflow[Workflow Configurable]
    Workflow --> Final[Finalisasi]
    Final --> Archive
~~~

## Alur dokumen dengan workflow

~~~mermaid
stateDiagram-v2
    [*] --> DRAFT
    DRAFT --> IN_REVIEW: Submit / mulai workflow
    IN_REVIEW --> IN_REVIEW: Approve dan lanjut step
    IN_REVIEW --> REVISION_REQUIRED: Request Revision
    REVISION_REQUIRED --> IN_REVIEW: Perbaiki + submit ulang
    IN_REVIEW --> REJECTED: Reject
    IN_REVIEW --> COMPLETED: Approval terakhir
    COMPLETED --> ARCHIVED: Finalisasi otomatis
~~~

~~~mermaid
flowchart TD
    A[Pembuat membuat draft] --> B[Pilih workflow]
    B --> C[Approval step 1]
    C --> D{Keputusan approver}
    D -->|APPROVE| E{Ada step berikutnya?}
    E -->|Ya| F[Buat approval step berikutnya]
    F --> D
    E -->|Tidak| G[Generate nomor surat]
    G --> H[Generate PDF snapshot]
    H --> I[QR verification]
    I --> J[Completed dan Archived]
    D -->|REQUEST REVISION| K[Revision Required + catatan]
    K --> L[Pembuat memperbaiki]
    L --> M[Submit kembali]
    M --> C
    D -->|REJECT| N[Rejected]
~~~

## Surat Tugas

~~~mermaid
flowchart TD
    A[Isi Surat Tugas] --> B[Pegawai, unit, jabatan]
    B --> C[Kegiatan, lokasi, tanggal]
    C --> D[Lampiran dan workflow]
    D --> E[Kaprodi approval]
    E --> F[Dekan approval]
    F --> G[Nomor, PDF, QR]
    G --> H[Arsip]
~~~

Workflow Surat Tugas hanya contoh konfigurasi. Administrator dapat mengganti atau menambah step tanpa membuat aplikasi baru.

## Nota Dinas dan disposisi

~~~mermaid
flowchart LR
    A[Nota Dinas: Dari, Kepada, Perihal, Isi] --> B[Kirim]
    B --> C[Disposisi baru]
    C --> D[READ]
    D --> E[IN PROGRESS]
    E --> F[COMPLETED]
~~~

## Finalisasi dokumen resmi

~~~mermaid
flowchart TD
    A[Approval terakhir] --> B[Transaction dan locking]
    B --> C[Numbering Rule]
    C --> D[Render template placeholder]
    D --> E[Simpan HTML/data snapshot]
    E --> F[PDF ke private storage]
    F --> G[Simpan SHA-256 checksum]
    G --> H[QR ke token verifikasi]
    H --> I[Approval + QR validation]
    I --> J[COMPLETED dan ARCHIVED]
~~~

Contoh numbering:

    {SEQ}/UFDK/{UNIT}/ST/{ROMAN_MONTH}/{YEAR}
    027/UFDK/FIK/ST/VIII/2026

## Verifikasi publik

~~~mermaid
sequenceDiagram
    participant User as Penerima
    participant QR as QR Code
    participant Public as Verifikasi Publik
    participant DB as Database
    User->>QR: Scan
    QR->>Public: /verifikasi/{token}
    Public->>DB: Cari token acak
    DB-->>Public: Dokumen completed/archived
    Public-->>User: Nomor, jenis, tanggal, penandatangan, status
~~~

Halaman publik tidak menampilkan isi dokumen, disposisi, lampiran, audit, atau metadata internal.

## Deadline dan notifikasi

| Kondisi | Badge |
|---|---|
| Tidak ada deadline / masih jauh | NORMAL |
| Maksimal 3 hari lagi | DUE_SOON |
| Lewat deadline, belum selesai | OVERDUE |
| Selesai / arsip | COMPLETED |

Notifikasi database dibuat untuk disposisi baru, approval baru, request revision, penolakan, submit ulang, dan dokumen selesai.

## Keamanan

1. URL memakai UUID publik; policy tetap wajib.
2. Backend mengotorisasi view, edit, submit, approve, download, dan archive.
3. Lampiran/PDF berada di private storage; download lewat controller.
4. Sequence nomor memakai transaction + locking, bukan MAX(id).
5. Riwayat approval immutable; resubmit membuat cycle baru.
6. Token verifikasi acak, bukan ID database.

## Prompt untuk GPT

~~~text
Buatkan diagram untuk sistem E-Office berikut:

1. Flowchart dokumen dari DRAFT sampai ARCHIVED.
2. BPMN sederhana untuk approve, reject, request revision, dan resubmit.
3. Sequence diagram QR verification publik.
4. Diagram komponen: UI, controller, policy, workflow service, numbering service, PDF generator, storage, notification, audit log.
5. ERD sederhana: documents, document_types, workflows, workflow_steps, workflow_instances, workflow_approvals, dispositions, document_files, numbering_rules, numbering_sequences, document_templates, generated_documents, activity_logs, users, roles, permissions, units, positions.

Aturan:
- Document adalah parent entity.
- Workflow configurable, tidak hardcoded.
- Approver hanya bertindak pada step aktif miliknya.
- Request revision mengembalikan dokumen ke pembuat dan history tetap tersimpan.
- Approval terakhir memicu numbering, PDF snapshot, checksum SHA-256, QR, completed, archive.
- File berada di private storage dan download wajib berotorisasi.
- Verifikasi publik hanya menampilkan nomor, jenis, tanggal, penandatangan, status.

Gunakan bahasa Indonesia, format Mermaid, dan jelaskan tiap diagram singkat.
~~~
