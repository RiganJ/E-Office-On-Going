<!doctype html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport"
              content="width=device-width,initial-scale=1">
        <title>Verifikasi Dokumen</title>
    </head>

    <body>
        <main>
            <h1>DOKUMEN VALID</h1>
            <p>Nomor Surat: {{ $document->number }}</p>
            <p>Jenis Dokumen: {{ $document->type->name }}</p>
            <p>Tanggal: {{ $document->document_date?->format('d-m-Y') }}</p>
            <p>Penandatangan:
                {{ data_get($document->generatedDocument?->data_snapshot, 'penandatangan', $document->creator->name) }}
            </p>
            <p>Status: {{ $document->status }}</p><small>Validasi dokumen melalui approval dan QR.
                Bukan Tanda Tangan
                Elektronik Tersertifikasi.</small>
        </main>
    </body>

</html>
