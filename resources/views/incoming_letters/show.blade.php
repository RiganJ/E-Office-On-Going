@extends('layouts.app')
@section('content')
    <div class="mb-6 flex items-start justify-between">
        <div><span class="badge">{{ $letter->incomingLetter->nature }}</span>
            <h1 class="mt-2 text-2xl font-bold">{{ $letter->subject }}</h1>
            <p class="text-slate-500">Surat Masuk · Agenda {{ $letter->incomingLetter->agenda_number }}
            </p>
        </div><a class="btn"
           href="{{ route('incoming-letters.index') }}">Kembali</a>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-xl bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="font-semibold">Informasi Surat</h2>
            <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-500">Pengirim</dt>
                    <dd class="font-medium">{{ $letter->incomingLetter->sender }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Instansi</dt>
                    <dd class="font-medium">{{ $letter->incomingLetter->sender_institution ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Nomor surat</dt>
                    <dd class="font-medium">{{ $letter->incomingLetter->letter_number ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Tanggal surat</dt>
                    <dd class="font-medium">
                        {{ $letter->incomingLetter->letter_date?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Tanggal diterima</dt>
                    <dd class="font-medium">
                        {{ $letter->incomingLetter->received_date->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Klasifikasi</dt>
                    <dd class="font-medium">{{ $letter->incomingLetter->classification ?? '—' }}</dd>
                </div>
            </dl>
            @if ($letter->content)
                <h2 class="mt-6 font-semibold">Ringkasan Isi</h2>
                <div class="mt-3 whitespace-pre-line">{{ $letter->content }}</div>
            @endif
        </section>
        <section class="rounded-xl bg-white p-6 shadow-sm">
            <h2 class="font-semibold">Tindakan</h2>
            @if ($letter->files->isNotEmpty())
                <a class="btn mt-4 w-full text-center"
                   href="{{ route('documents.download', $letter) }}"><i
                       class="fa-solid fa-download"></i> Unduh Lampiran</a>
            @endif @can('view', $letter)
            <a class="btn btn-secondary mt-3 w-full text-center"
               href="{{ route('dispositions.create', $letter) }}"><i class="fa-solid fa-share"></i>
                Buat Disposisi</a>
        @endcan
    </section>
</div>
<section class="mt-6 rounded-xl bg-white p-6 shadow-sm">
    <h2 class="font-semibold">Riwayat Disposisi</h2>
    <div class="mt-4 space-y-3">
        @forelse($letter->dispositions as $disposition)
            <div class="border-l-2 border-cyan-500 pl-3 text-sm">
                <b>{{ $disposition->instruction }}</b>
                <p class="text-slate-500">Kepada:
                    {{ $disposition->recipient?->name ?? 'Belum ditentukan' }} ·
                    {{ $disposition->status }}</p>
        </div>@empty<p class="text-sm text-slate-500">Belum ada disposisi.</p>
        @endforelse
    </div>
</section>
<section class="mt-6 rounded-xl bg-white p-6 shadow-sm">
    <h2 class="font-semibold">Timeline</h2>
    <div class="mt-4 space-y-4">
        @foreach ($letter->activityLogs as $log)
            <div class="border-l-2 border-cyan-500 pl-3 text-sm">
                <b>{{ str_replace('_', ' ', $log->action) }}</b>
                <p class="text-slate-500">{{ $log->user?->name ?? 'Sistem' }} ·
                    {{ $log->created_at->format('d M Y H:i') }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
