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
    @endphp
    <section class="dispositions-header">
        <div>
            <p class="dispositions-kicker"><i class="fa-solid fa-share-nodes"></i> TINDAK LANJUT</p>
            <h1>Disposisi Saya</h1>
            <p>Kelola instruksi yang masuk dan selesaikan tindak lanjutnya secara bertahap.</p>
        </div>
        <div class="dispositions-total"><i
               class="fa-solid fa-inbox"></i><span><b>{{ number_format($dispositions->total()) }}</b>
                disposisi
                diterima</span></div>
    </section>
    <section class="dispositions-list">
        <div class="dispositions-list-heading">
            <div>
                <h2>Daftar disposisi</h2>
                <p>Menampilkan
                    {{ $dispositions->firstItem() ?? 0 }}–{{ $dispositions->lastItem() ?? 0 }} dari
                    {{ $dispositions->total() }} disposisi.</p>
            </div><span><i class="fa-solid fa-arrow-down-wide-short"></i> Terbaru terlebih dahulu</span>
        </div>
        <div class="disposition-items">
            @forelse($dispositions as $disposition)
                @php($status = $disposition->status)
                <article class="disposition-card">
                    <div class="disposition-document"><span><i
                               class="fa-solid fa-file-lines"></i></span>
                        <div><a
                               href="{{ route('documents.show', $disposition->document) }}">{{ $disposition->document->subject }}</a>
                            <p><i class="fa-solid fa-user"></i> Dari {{ $disposition->sender->name }}
                                <span>•</span>
                                {{ $disposition->created_at->translatedFormat('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="disposition-instruction"><small>Instruksi</small>
                        <p>{{ str($disposition->instruction)->limit(120) }}</p>
                    </div>
                    <div class="disposition-status"><span
                              class="document-status disposition-{{ strtolower($status) }}"><i
                               class="fa-solid {{ $icons[$status] }}"></i>
                            {{ $labels[$status] }}</span></div>
                    <div class="disposition-actions"><a class="btn btn-secondary"
                           href="{{ route('dispositions.show', $disposition) }}"><i
                               class="fa-solid fa-eye"></i>
                            Detail</a>
                        @if ($status === 'UNREAD')
                            <form method="post"
                                  action="{{ route('dispositions.read', $disposition) }}">@csrf
                                @method('PATCH')<button class="btn"
                                        title="Tandai sudah dibaca"><i
                                       class="fa-solid fa-envelope-open"></i><span>Baca</span></button>
                            </form>
                            @endif @if (!in_array($status, ['IN_PROGRESS', 'COMPLETED']))
                                <form method="post"
                                      action="{{ route('dispositions.process', $disposition) }}">@csrf
                                    @method('PATCH')<button class="btn disposition-process"><i
                                           class="fa-solid fa-spinner"></i><span>Proses</span></button>
                                </form>
                                @endif @if ($status !== 'COMPLETED')
                                    <form method="post"
                                          action="{{ route('dispositions.complete', $disposition) }}"
                                          data-confirm="Tandai disposisi ini selesai?">@csrf
                                        @method('PATCH')<button class="btn disposition-complete"><i
                                               class="fa-solid fa-check"></i><span>Selesai</span></button>
                                    </form>
                                @endif
                    </div>
            </article>@empty<div class="dispositions-empty"><i
                       class="fa-solid fa-inbox"></i><strong>Belum ada disposisi
                        untuk ditindaklanjuti</strong>
                    <p>Disposisi yang ditujukan kepada Anda akan tampil di halaman ini.</p><a
                       class="btn btn-secondary"
                       href="{{ route('documents.index') }}"><i class="fa-solid fa-folder-open"></i>
                        Buka dokumen</a>
                </div>
            @endforelse
        </div>
        @if ($dispositions->hasPages())
            <div class="dispositions-pagination">{{ $dispositions->links() }}</div>
        @endif
    </section>
@endsection
