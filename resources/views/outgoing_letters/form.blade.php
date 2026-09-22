@extends('layouts.app')
@section('content')
    <a class="document-back"
       href="{{ route('outgoing-letters.index') }}">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Surat Keluar</a>
    <section class="internal-form-header">
        <div class="internal-form-icon"><i class="fa-solid fa-paper-plane"></i></div>
        <div>
            <p>PERSURATAN KELUAR</p>
            <h1>Buat Surat Keluar</h1><span>Surat disimpan sebagai rekam dokumen dan tetap dapat
                ditinjau, meskipun telah
                diproses atau didisposisikan.</span>
        </div>
    </section>
    <div class="internal-form-steps">
        <div class="is-active"><b>1</b><span>Isi surat</span></div><i></i>
        <div class="is-active"><b>2</b><span>Lampiran PDF</span></div><i></i>
        <div><b>3</b><span>Simpan / proses</span></div>
    </div>
    <form class="internal-document-form"
          method="post"
          enctype="multipart/form-data"
          action="{{ route('outgoing-letters.store') }}">@csrf
        <section class="internal-form-section">
            <header><span><i class="fa-solid fa-file-lines"></i></span>
                <div>
                    <h2>Informasi surat</h2>
                    <p>Lengkapi data utama sebelum surat disimpan atau dikirim ke alur persetujuan.</p>
                </div>
            </header>
            <div class="internal-fields two-cols"><label>Unit penerbit<select name="unit_id">
                        <option value="">Pilih unit penerbit</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}"
                                    @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </label><label>Tanggal surat<input type="date"
                           name="document_date"
                           value="{{ old('document_date') }}"></label></div><label
                   class="internal-content-field">Perihal<input name="subject"
                       value="{{ old('subject') }}"
                       placeholder="Contoh: Permohonan koordinasi kegiatan"
                       required
                       maxlength="255"></label><label class="internal-content-field">Isi
                surat<small>Tulis isi surat
                    dengan jelas agar mudah dipahami penerima dan pihak yang memprosesnya.</small>
                <textarea name="content"
                          rows="10"
                          placeholder="Tuliskan isi surat di sini..."
                          required>{{ old('content') }}</textarea>
            </label>
        </section>
        <section class="internal-form-section">
            <header><span><i class="fa-solid fa-file-pdf"></i></span>
                <div>
                    <h2>Lampiran PDF</h2>
                    <p>Unggah PDF surat yang akan didisposisikan. Lampiran tersimpan secara privat dan
                        dapat diunduh oleh
                        pengguna berwenang.</p>
                </div>
            </header><label class="internal-upload"><i
                   class="fa-solid fa-cloud-arrow-up"></i><span><b>Pilih file
                        PDF</b><small>Maksimal 10 MB. File ini akan melekat pada rekam Surat
                        Keluar.</small></span><input type="file"
                       name="attachment"
                       accept="application/pdf,.pdf"></label>
            @error('attachment')
                <small class="internal-error">{{ $message }}</small>
            @enderror
        </section>
        <section class="internal-form-section">
            <header><span><i class="fa-solid fa-diagram-project"></i></span>
                <div>
                    <h2>Alur persetujuan</h2>
                    <p>Pilih alur persetujuan. Nomor surat diterbitkan otomatis setelah tahap
                        persetujuan terakhir selesai.
                    </p>
                </div>
            </header>
            <div class="internal-fields two-cols"><label>Alur persetujuan<select name="workflow_id"
                            required>
                        <option value="">Pilih alur persetujuan</option>
                        @foreach ($workflows as $workflow)
                            <option value="{{ $workflow->id }}"
                                    @selected(old('workflow_id') == $workflow->id)>{{ $workflow->name }}
                                ({{ $workflow->steps->count() }} langkah)
                            </option>
                        @endforeach
                    </select>
                    @error('workflow_id')
                        <small class="internal-error">{{ $message }}</small>
                    @enderror
                </label>
                <div class="outgoing-record-note"><i class="fa-solid fa-hashtag"></i>
                    <div><strong>Nomor surat diterbitkan setelah disetujui</strong>
                        <p>Nomor mengikuti Master Nomor Surat dan muncul kembali pada akun pembuat saat
                            proses selesai.</p>
                    </div>
                </div>
            </div>
        </section>
        <footer class="internal-form-actions"><a class="btn btn-secondary"
               href="{{ route('outgoing-letters.index') }}"><i class="fa-solid fa-xmark"></i>
                Batal</a><button class="btn"><i class="fa-solid fa-floppy-disk"></i>
                Simpan Surat Keluar</button></footer>
    </form>
@endsection
