@extends('layouts.app')
@section('content')
    @php
        $labels = [
            'UNREAD' => 'Belum dibaca',
            'READ' => 'Sudah dibaca',
            'IN_PROGRESS' => 'Sedang diproses',
            'COMPLETED' => 'Selesai',
        ];
        $icons = [
            'UNREAD' => 'fa-envelope',
            'READ' => 'fa-envelope-open',
            'IN_PROGRESS' => 'fa-spinner',
            'COMPLETED' => 'fa-circle-check',
        ];
        $status = $disposition->status;
    @endphp
    <a class="document-back"
       href="{{ route('dispositions.index') }}">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar disposisi</a>
    <section class="disposition-hero">
        <div><span class="document-status disposition-{{ strtolower($status) }}"><i
                   class="fa-solid {{ $icons[$status] }}"></i> {{ $labels[$status] }}</span>
            <h1>Disposisi dokumen</h1>
            <p><i class="fa-solid fa-file-lines"></i> {{ $disposition->document->subject }}</p>
        </div><a class="btn btn-secondary"
           href="{{ route('documents.show', $disposition->document) }}"><i
               class="fa-solid fa-arrow-up-right-from-square"></i> Buka dokumen</a>
    </section>
    <section class="disposition-quick-info">
        <div><i
               class="fa-solid fa-user"></i><span>Pengirim</span><strong>{{ $disposition->sender->name }}</strong>
        </div>
        <div><i
               class="fa-solid fa-user-check"></i><span>Penerima</span><strong>{{ $disposition->recipient?->name ?? 'Unit atau jabatan tujuan' }}</strong>
        </div>
        <div><i
               class="fa-solid fa-flag"></i><span>Prioritas</span><strong>{{ ['LOW' => 'Rendah', 'NORMAL' => 'Normal', 'HIGH' => 'Tinggi', 'URGENT' => 'Mendesak'][$disposition->priority] ?? $disposition->priority }}</strong>
        </div>
        <div><i class="fa-solid fa-calendar-check"></i><span>Batas
                waktu</span><strong>{{ $disposition->deadline?->translatedFormat('d M Y, H:i') ?? 'Tidak ada' }}</strong>
        </div>
    </section>
    <div class="disposition-detail-layout">
        <section class="document-panel">
            <header>
                <div><i class="fa-solid fa-clipboard-list"></i>
                    <h2>Instruksi tindak lanjut</h2>
                </div>
            </header>
            <div class="document-content">{{ $disposition->instruction }}</div>
            @if ($disposition->notes)
                <div class="disposition-sender-note"><i class="fa-solid fa-note-sticky"></i>
                    <div><strong>Catatan pengirim</strong>
                        <p>{{ $disposition->notes }}</p>
                    </div>
                </div>
            @endif
        </section>
        <aside class="document-panel disposition-action-panel">
            <header>
                <div><i class="fa-solid fa-list-check"></i>
                    <h2>Tindakan</h2>
                </div>
            </header>
            <div>
                <p>Pilih tindakan sesuai progres disposisi saat ini.</p>
                @if ($status === 'UNREAD')
                    <form method="post"
                          action="{{ route('dispositions.read', $disposition) }}">@csrf
                        @method('PATCH')<button class="btn"><i
                               class="fa-solid fa-envelope-open"></i> Tandai sudah
                            dibaca</button></form>
                    @endif @if (!in_array($status, ['IN_PROGRESS', 'COMPLETED']))
                        <form method="post"
                              action="{{ route('dispositions.process', $disposition) }}">@csrf
                            @method('PATCH')<button class="btn disposition-process"><i
                                   class="fa-solid fa-spinner"></i>
                                Mulai proses</button></form>
                        @endif @if ($status !== 'COMPLETED')
                            <form method="post"
                                  action="{{ route('dispositions.complete', $disposition) }}"
                                  data-confirm="Tandai disposisi ini selesai?">@csrf
                                @method('PATCH')<button class="btn disposition-complete"><i
                                       class="fa-solid fa-check"></i> Tandai
                                selesai</button></form>@else<div class="disposition-done"><i
                                   class="fa-solid fa-circle-check"></i> Disposisi telah selesai
                                ditindaklanjuti.</div>
                        @endif
            </div>
        </aside>
    </div>
@endsection
