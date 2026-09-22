@extends('layouts.app')
@section('content')
    @php $isMemo = $type->code === 'MEMO'; @endphp
    <a class="document-back"
       href="{{ route('documents.index') }}">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar dokumen</a>
    <section class="internal-form-header">
        <div class="internal-form-icon"><i
               class="fa-solid {{ $isMemo ? 'fa-envelope-open-text' : 'fa-briefcase' }}"></i>
        </div>
        <div>
            <p>{{ $isMemo ? 'KORESPONDENSI INTERNAL' : 'PENUGASAN PEGAWAI' }}</p>
            <h1>Buat {{ $type->name }}</h1>
            <span>{{ $isMemo ? 'Isi informasi pengirim, penerima, dan isi nota secara ringkas agar mudah ditindaklanjuti.' : 'Lengkapi data tugas dan pilih alur persetujuan sebelum menyimpan.' }}</span>
        </div>
    </section>
    <div class="internal-form-steps">
        <div class="is-active"><b>1</b><span>Informasi dokumen</span></div><i></i>
        <div class="is-active"><b>2</b><span>{{ $isMemo ? 'Tujuan nota' : 'Data tugas' }}</span></div>
        <i></i>
        <div><b>3</b><span>Simpan</span></div>
    </div>
    <form class="internal-document-form"
          method="post"
          enctype="multipart/form-data"
          action="{{ route('internal-documents.store', $type->code) }}">@csrf
        <section class="internal-form-section">
            <header><span><i class="fa-solid fa-file-lines"></i></span>
                <div>
                    <h2>Informasi dokumen</h2>
                    <p>Data ini menjadi identitas utama {{ strtolower($type->name) }}.</p>
                </div>
            </header>
            <div class="internal-fields two-cols"><label>Unit kerja <select name="unit_id"
                            required>
                        <option value="">Pilih unit kerja</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}"
                                    @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </label><label>Perihal <input name="subject"
                           value="{{ old('subject') }}"
                           placeholder="Contoh: Koordinasi kegiatan akademik"
                           required></label></div><label class="internal-content-field">Isi dokumen
                <small>Tuliskan informasi utama secara ringkas, jelas, dan dapat
                    ditindaklanjuti.</small>
                <textarea name="content"
                          rows="8"
                          placeholder="Tuliskan isi {{ strtolower($type->name) }} di sini..."
                          required>{{ old('content') }}</textarea>
            </label>
        </section>
        @if ($isMemo)
            <section class="internal-form-section">
                <header><span><i class="fa-solid fa-people-arrows"></i></span>
                    <div>
                        <h2>Tujuan Nota Dinas</h2>
                        <p>Tentukan pihak pengirim dan penerima nota dinas internal.</p>
                    </div>
                </header>
                <div class="internal-fields two-cols"><label>Dari <input name="metadata[dari]"
                               value="{{ old('metadata.dari') }}"
                               placeholder="Contoh: Wakil Rektor II"
                               required></label><label>Kepada <input name="metadata[kepada]"
                               value="{{ old('metadata.kepada') }}"
                               placeholder="Contoh: Bagian Keuangan"
                               required></label>
                </div>
            </section>
        @endif
        @if (!$isMemo)
            <section class="internal-form-section">
                <header><span><i class="fa-solid fa-person-walking-luggage"></i></span>
                    <div>
                        <h2>Data pelaksanaan tugas</h2>
                        <p>Informasi ini akan ditampilkan pada Surat Tugas final.</p>
                    </div>
                </header>
                <div class="internal-fields two-cols"><label>Nama pegawai<input
                               name="metadata[nama_pegawai]"
                               value="{{ old('metadata.nama_pegawai') }}"
                               required></label><label>Jabatan<input name="metadata[jabatan]"
                               value="{{ old('metadata.jabatan') }}"
                               placeholder="Contoh: Dosen"
                               required></label><label>Nama kegiatan<input name="metadata[kegiatan]"
                               value="{{ old('metadata.kegiatan') }}"
                               required></label><label>Lokasi<input name="metadata[lokasi]"
                               value="{{ old('metadata.lokasi') }}"
                               required></label><label>Tanggal
                        mulai<input type="date"
                               name="metadata[tanggal_mulai]"
                               value="{{ old('metadata.tanggal_mulai') }}"
                               required></label><label>Tanggal selesai<input type="date"
                               name="metadata[tanggal_selesai]"
                               value="{{ old('metadata.tanggal_selesai') }}"
                               required></label></div><label class="internal-content-field">Tujuan /
                    keterangan
                    <textarea name="metadata[keterangan]"
                              rows="3">{{ old('metadata.keterangan') }}</textarea>
                </label>
            </section>
        @endif
        <section class="internal-form-section">
            <header><span><i class="fa-solid fa-paperclip"></i></span>
                <div>
                    <h2>Lampiran dan alur persetujuan</h2>
                    <p>Lampiran bersifat opsional.
                        {{ $isMemo ? 'Nota dapat disimpan sebagai draf atau langsung masuk ke alur persetujuan.' : 'Alur persetujuan wajib dipilih untuk Surat Tugas.' }}
                    </p>
                </div>
            </header>
            <div class="internal-fields two-cols"><label class="internal-upload"><i
                       class="fa-solid fa-cloud-arrow-up"></i><span><b>Tambahkan lampiran</b><small>PDF,
                            Word, atau gambar;
                            maksimal 10 MB.</small></span><input type="file"
                           name="attachment"
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"></label><label>Alur
                    persetujuan<select name="workflow_id"
                            @if (!$isMemo) required @endif>
                        <option value="">
                            {{ $isMemo ? 'Simpan sebagai draf' : 'Pilih alur persetujuan' }}</option>
                        @foreach ($workflows as $workflow)
                            <option value="{{ $workflow->id }}"
                                    @selected(old('workflow_id') == $workflow->id)>{{ $workflow->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('workflow_id')
                        <small class="internal-error">{{ $message }}</small>
                    @enderror
                </label></div>
        </section>
        <footer class="internal-form-actions"><a class="btn btn-secondary"
               href="{{ route('documents.index') }}"><i class="fa-solid fa-xmark"></i> Batal</a><button
                    class="btn"><i class="fa-solid fa-floppy-disk"></i>
                Simpan {{ $type->name }}</button></footer>
    </form>
@endsection
