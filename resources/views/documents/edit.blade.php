@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="flex gap-3">
            <span class="page-header-icon">
                <i class="fa-solid fa-pen-to-square"></i>
            </span>

            <div>
                <h1>Perbaiki Dokumen</h1>
                <p>Pastikan perbaikan menjawab catatan revisi sebelum dikirim ulang.</p>
            </div>
        </div>

        <a class="btn btn-secondary"
           href="{{ route('documents.show', $document) }}">
            <i class="fa-solid fa-arrow-left"></i>
            Detail
        </a>
    </div>

    <form class="form-shell"
          method="post"
          action="{{ route('documents.update', $document) }}">
        @csrf
        @method('PUT')

        <section class="form-section">
            <h2>Konten revisi</h2>
            <p>Riwayat approval sebelumnya tetap tersimpan dan tidak akan berubah.</p>

            <label>
                Perihal
                <input name="subject"
                       value="{{ old('subject', $document->subject) }}"
                       required>
            </label>

            <label class="mt-5">
                Isi dokumen
                <textarea name="content"
                          rows="12">{{ old('content', $document->content) }}</textarea>
            </label>
        </section>

        <footer class="form-actions">
            <a class="btn btn-secondary"
               href="{{ route('documents.show', $document) }}">
                Batal
            </a>

            <button class="btn">
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Perbaikan
            </button>
        </footer>
    </form>
@endsection
