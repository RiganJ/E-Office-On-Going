@extends('layouts.app')
@section('content')
    <div class="mb-6 flex items-end justify-between">
        <div>
            <h1 class="text-2xl font-bold">Surat Masuk</h1>
            <p class="text-slate-500">Pencatatan, lampiran, dan tindak lanjut surat yang diterima.</p>
        </div>
        @if (auth()->user()->hasPermission('create documents'))
            <a class="btn"
               href="{{ route('incoming-letters.create') }}">+ Catat Surat Masuk</a>
        @endif
    </div>
    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <table>
            <thead>
                <tr>
                    <th>Agenda</th>
                    <th>Pengirim</th>
                    <th>Perihal</th>
                    <th>Sifat</th>
                    <th>Diterima</th>
                </tr>
            </thead>
            <tbody>
                @forelse($letters as $letter)
                    <tr>
                        <td>{{ $letter->agenda_number }}</td>
                        <td>{{ $letter->sender }}@if ($letter->sender_institution)
                                <small
                                       class="block text-slate-500">{{ $letter->sender_institution }}</small>
                            @endif
                        </td>
                        <td><a class="text-cyan-700"
                               href="{{ route('incoming-letters.show', $letter->document) }}">{{ $letter->document->subject }}</a>
                        </td>
                        <td><span class="badge">{{ $letter->nature }}</span></td>
                        <td>{{ $letter->received_date->format('d M Y') }}</td>
                    </tr>@empty<tr>
                            <td colspan="5"
                                class="p-8 text-center text-slate-500">Belum ada surat masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $letters->links() }}</div>
    @endsection
