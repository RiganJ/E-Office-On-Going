@extends('layouts.app')
@section('content')
    <a class="document-back"
       href="{{ route('incoming-letters.index') }}">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Surat Masuk</a>
    <section class="internal-form-header">
        <div class="internal-form-icon"><i class="fa-solid fa-envelope"></i></div>
        <div>
            <p>PERSURATAN MASUK</p>
            <h1>Catat dan Disposisikan Surat Masuk</h1><span>Rekam surat yang diterima, unggah
                lampirannya, lalu pilih alur
                disposisi yang sudah ditetapkan.</span>
        </div>
    </section>
    <div class="internal-form-steps">
        <div class="is-active"><b>1</b><span>Data surat</span></div><i></i>
        <div class="is-active"><b>2</b><span>Lampiran PDF</span></div><i></i>
        <div><b>3</b><span>Alur disposisi</span></div>
    </div>
    <form class="internal-document-form"
          method="post"
          enctype="multipart/form-data"
          action="{{ route('incoming-letters.store') }}">@csrf
        <section class="internal-form-section">
            <header><span><i class="fa-solid fa-file-lines"></i></span>
                <div>
                    <h2>Informasi surat</h2>
                    <p>Lengkapi data penerimaan dan isi utama surat sebelum diteruskan ke alur
                        disposisi.</p>
                </div>
            </header>
            <div class="internal-fields two-cols"><label>Unit penerima<select name="unit_id">
                        <option value="">Pilih unit penerima</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}"
                                    @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </label><label>Tanggal diterima<input type="date"
                           name="received_date"
                           value="{{ old('received_date', now()->toDateString()) }}"
                           required></label><label>Pengirim<input name="sender"
                           value="{{ old('sender') }}"
                           placeholder="Nama pengirim"
                           required
                           maxlength="255"></label><label>Instansi pengirim<input
                           name="sender_institution"
                           value="{{ old('sender_institution') }}"
                           placeholder="Contoh: Kementerian Pendidikan"></label><label>Nomor surat<input
                           name="letter_number"
                           value="{{ old('letter_number') }}"
                           placeholder="Nomor surat dari pengirim"></label><label>Tanggal
                    surat<input type="date"
                           name="letter_date"
                           value="{{ old('letter_date') }}"></label><label>Sifat
                    surat<select name="nature"
                            required>
                        @foreach (['BIASA' => 'Biasa', 'PENTING' => 'Penting', 'SEGERA' => 'Segera', 'RAHASIA' => 'Rahasia'] as $value => $label)
                            <option value="{{ $value }}"
                                    @selected(old('nature', 'BIASA') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label><label>Klasifikasi<input name="classification"
                           value="{{ old('classification') }}"
                           placeholder="Contoh: Akademik"></label></div><label
                   class="internal-content-field">Perihal<input name="subject"
                       value="{{ old('subject') }}"
                       placeholder="Contoh: Permohonan koordinasi kegiatan"
                       required
                       maxlength="255"></label><label class="internal-content-field">Ringkasan
                isi<small>Opsional,
                    untuk memudahkan pencarian dan membantu pihak yang menindaklanjuti surat.</small>
                <textarea name="content"
                          rows="10"
                          placeholder="Tuliskan ringkasan isi surat di sini...">{{ old('content') }}</textarea>
            </label>
        </section>
        <section class="internal-form-section">
            <header><span><i class="fa-solid fa-file-pdf"></i></span>
                <div>
                    <h2>Lampiran PDF</h2>
                    <p>Unggah PDF surat yang diterima. Lampiran tersimpan secara privat dan dapat
                        diunduh oleh pengguna
                        berwenang.</p>
                </div>
            </header><label class="internal-upload"><i
                   class="fa-solid fa-cloud-arrow-up"></i><span><b>Pilih file
                        PDF</b><small>Maksimal 10 MB. File ini akan melekat pada rekam Surat
                        Masuk.</small></span><input type="file"
                       name="attachment"
                       accept="application/pdf,.pdf"
                       required></label>
            @error('attachment')
                <small class="internal-error">{{ $message }}</small>
            @enderror
        </section>
        <section class="internal-form-section">
            <header><span><i class="fa-solid fa-diagram-project"></i></span>
                <div>
                    <h2>Alur disposisi</h2>
                    <p>Pilih alur yang telah dikonfigurasi. Penerima dan tahapan mengikuti pengaturan
                        alur tersebut.</p>
                </div>
            </header>
            <div class="internal-fields two-cols"><label>Alur disposisi<select name="workflow_id"
                            required>
                        <option value="">Pilih alur disposisi</option>
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
                <div class="outgoing-record-note"><i class="fa-solid fa-inbox"></i>
                    <div><strong>Rekam surat tetap tersimpan</strong>
                        <p>Surat dapat ditinjau kembali dari menu Surat Masuk dan Dokumen pada setiap
                            tahap proses.</p>
                    </div>
                </div>
            </div>
        </section>
        <footer class="internal-form-actions"><a class="btn btn-secondary"
               href="{{ route('incoming-letters.index') }}"><i class="fa-solid fa-xmark"></i>
                Batal</a><button class="btn"><i class="fa-solid fa-share-nodes"></i>
                Simpan dan Mulai Alur Disposisi</button></footer>
    </form>
@endsection
