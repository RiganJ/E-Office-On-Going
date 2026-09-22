@extends('layouts.app')
@section('content')
    <div class="mb-6 flex items-end justify-between">
        <div>
            <h1 class="text-2xl font-bold">Workflow</h1>
            <p class="text-slate-500">Konfigurasi alur approval.</p>
        </div><a class="btn"
           href="{{ route('workflows.create') }}">+ Workflow</a>
    </div>
    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th>Unit</th>
                    <th>Langkah</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($workflows as $workflow)
                    <tr>
                        <td>{{ $workflow->name }}<small
                                   class="block text-slate-500">{{ $workflow->code }}</small></td>
                        <td>{{ $workflow->documentType->name }}</td>
                        <td>{{ $workflow->unit?->name ?? 'Semua unit' }}</td>
                        <td>{{ $workflow->steps->count() }}</td>
                        <td>{{ $workflow->is_active ? 'AKTIF' : 'NONAKTIF' }}</td>
                        <td><a class="text-cyan-700"
                               href="{{ route('workflows.edit', $workflow) }}">Ubah</a></td>
                </tr>@empty<tr>
                        <td colspan="6">Belum ada workflow.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $workflows->links() }}</div>
@endsection
