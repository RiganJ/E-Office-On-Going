@extends('layouts.app')
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Arsip Digital</h1>
        <p class="text-slate-500">Indeks arsip bersumber dari database, bukan struktur folder.</p>
    </div>
    <form class="mb-5 grid gap-2 md:grid-cols-4"><input name="search"
               value="{{ request('search') }}"
               placeholder="Perihal"><input name="number"
               value="{{ request('number') }}"
               placeholder="Nomor surat"><input type="number"
               name="year"
               value="{{ request('year') }}"
               placeholder="Tahun"><select name="unit_id">
            <option value="">Semua unit</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}"
                        @selected(request('unit_id') == $unit->id)>{{ $unit->name }}</option>
            @endforeach
        </select>
        <select name="document_type_id">
            <option value="">Semua jenis</option>
            @foreach ($types as $type)
                <option value="{{ $type->id }}"
                        @selected(request('document_type_id') == $type->id)>{{ $type->name }}</option>
            @endforeach
        </select>
        <select name="creator_id">
            <option value="">Semua pembuat</option>
            @foreach ($creators as $creator)
                <option value="{{ $creator->id }}"
                        @selected(request('creator_id') == $creator->id)>{{ $creator->name }}</option>
            @endforeach
        </select>
        <button class="btn">Cari Arsip</button>
    </form>
    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table>
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Perihal</th>
                    <th>Jenis</th>
                    <th>Unit</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $document)
                    <tr>
                        <td>{{ $document->number }}</td>
                        <td><a class="text-cyan-700"
                               href="{{ route('documents.show', $document) }}">{{ $document->subject }}</a>
                        </td>
                        <td>{{ $document->type->name }}</td>
                        <td>{{ $document->unit?->name ?? '—' }}</td>
                        <td>{{ $document->document_date?->format('d M Y') }}</td>
                </tr>@empty<tr>
                        <td colspan="5"
                            class="p-8 text-center text-slate-500">Arsip tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $documents->links() }}</div>
@endsection
