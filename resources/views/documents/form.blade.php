@extends('layouts.app') @section('content')
    <h1 class="mb-1 text-2xl font-bold">
        Buat Dokumen</h1>
    <p class="mb-6 text-slate-500">Draf dapat dikirim ke alur persetujuan setelah konfigurasi tersedia.
    </p>
    <form method="post"
          action="{{ route('documents.store') }}"
          class="max-w-3xl rounded-xl bg-white p-6 shadow-sm">@csrf<div class="grid gap-5 md:grid-cols-2">
            <label>Jenis dokumen<select name="document_type_id"
                        required>
                    @foreach ($types as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </label><label>Unit kerja<select name="unit_id">
                    <option value="">Pilih unit</option>
                    @foreach ($units as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select></label>
        </div><label class="mt-5 block">Perihal<input name="subject"
                   value="{{ old('subject') }}"
                   required
                   maxlength="255"></label><label class="mt-5 block">Isi / keterangan
            <textarea name="content"
                      rows="7">{{ old('content') }}</textarea>
        </label><label class="mt-5 block">Batas waktu<input name="deadline"
                   type="datetime-local"></label><button class="btn mt-6"><i
               class="fa-solid fa-floppy-disk"></i> Simpan draf</button></form>
@endsection
