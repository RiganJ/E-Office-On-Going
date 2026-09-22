@extends('layouts.app')
@section('content')
    <div class="mb-6 flex items-end justify-between">
        <div>
            <h1 class="text-2xl font-bold">Surat Keluar</h1>
            <p class="text-slate-500">Draft, proses workflow, dan surat keluar resmi.</p>
        </div><a class="btn"
           href="{{ route('outgoing-letters.create') }}">+ Buat Surat Keluar</a>
    </div>
    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table>
            <thead>
                <tr>
                    <th>Perihal</th>
                    <th>Unit</th>
                    <th>Pembuat</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($letters as $letter)
                    <tr>
                        <td><a class="text-cyan-700"
                               href="{{ route('outgoing-letters.show', $letter) }}">{{ $letter->subject }}</a>
                        </td>
                        <td>{{ $letter->unit?->name ?? '—' }}</td>
                        <td>{{ $letter->creator->name }}</td>
                        <td><span class="badge">{{ $letter->status }}</span></td>
                        <td>{{ $letter->created_at->format('d M Y') }}</td>
                </tr>@empty<tr>
                        <td colspan="5"
                            class="p-8 text-center text-slate-500">Belum ada surat keluar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $letters->links() }}</div>
@endsection
