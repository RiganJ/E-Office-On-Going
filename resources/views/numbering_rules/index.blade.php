@extends('layouts.app')
@section('content')
    <section class="mb-6">
        <p class="eyebrow">MASTER DATA</p>
        <h1 class="text-2xl font-bold">Master Nomor Surat</h1>
        <p class="text-slate-500">Atur format nomor Surat Tugas dan Surat Keluar. Nomor diterbitkan
            otomatis saat approval
            tahap terakhir selesai.</p>
    </section>
    @foreach (['ASSIGNMENT' => ['Surat Tugas', $assignmentRules], 'OUTGOING' => ['Surat Keluar', $outgoingRules]] as $code => [$title, $rules])
        <section class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm">
            <header class="border-b border-slate-200 px-6 py-4">
                <h2 class="font-semibold">{{ $title }}</h2>
                <p class="text-sm text-slate-500">Aturan per unit diprioritaskan; aturan Semua unit
                    digunakan sebagai
                    cadangan.</p>
            </header>
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Format nomor</th>
                            <th>Reset</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rules as $rule)
                            <tr>
                                <td>{{ $rule->unit?->name ?? 'Semua unit' }}</td>
                                <td><code>{{ $rule->format }}</code></td>
                                <td>{{ $rule->reset_period === 'MONTHLY' ? 'Bulanan' : 'Tahunan' }}</td>
                                <td>{{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                <td>
                                    <form method="post"
                                          action="{{ route('numbering-rules.destroy', $rule) }}"
                                          data-confirm="Hapus aturan nomor ini? Nomor pada dokumen yang sudah diterbitkan tidak akan berubah.">
                                        @csrf @method('DELETE')<button type="submit"
                                                class="text-rose-600">Hapus</button>
                                    </form>
                                </td>
                        </tr>@empty<tr>
                                <td colspan="5"
                                    class="p-6 text-center text-slate-500">Belum ada aturan nomor
                                    {{ strtolower($title) }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <form class="grid gap-4 border-t border-slate-200 p-6 md:grid-cols-4"
                  method="post"
                  action="{{ route('numbering-rules.store') }}">@csrf<input type="hidden"
                       name="document_type_id"
                       value="{{ $types[$code]->id }}"><label>Unit<select name="unit_id">
                        <option value="">Semua unit</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select></label><label>Format nomor<input name="format"
                           value="{{ $code === 'ASSIGNMENT' ? '{SEQ}/UFDK/{UNIT}/ST/{ROMAN_MONTH}/{YEAR}' : '{SEQ}/UFDK/{UNIT}/SK/{ROMAN_MONTH}/{YEAR}' }}"
                           required></label><label>Periode reset<select name="reset_period">
                        <option value="YEARLY">Tahunan</option>
                        <option value="MONTHLY">Bulanan</option>
                    </select></label><label class="flex items-center gap-2 pt-6"><input type="checkbox"
                           name="is_active"
                           value="1"
                           checked> Aktif</label>
                <div class="md:col-span-4"><small class="text-slate-500">Placeholder: {SEQ}, {UNIT},
                        {DOCUMENT_TYPE},
                        {MONTH}, {ROMAN_MONTH}, {YEAR}.</small><button class="btn ml-4"
                            type="submit">+ Tambah aturan
                        {{ $title }}</button></div>
            </form>
        </section>
    @endforeach
@endsection
