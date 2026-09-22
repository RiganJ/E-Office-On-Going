@extends('layouts.app')
@section('content')
    @php
        $statusLabels = [
            'PENDING' => 'Menunggu keputusan',
            'APPROVED' => 'Disetujui',
            'REVISION_REQUESTED' => 'Perlu revisi',
            'REJECTED' => 'Ditolak',
        ];
        $statusIcons = [
            'PENDING' => 'fa-clock',
            'APPROVED' => 'fa-circle-check',
            'REVISION_REQUESTED' => 'fa-arrow-rotate-left',
            'REJECTED' => 'fa-circle-xmark',
        ];
        $tabs = [
            'pending' => ['Menunggu', 'fa-clock'],
            'processed' => ['Selesai diproses', 'fa-circle-check'],
            'rejected' => ['Ditolak', 'fa-circle-xmark'],
        ];
    @endphp
    <section class="approvals-header">
        <div>
            <p class="approvals-kicker"><i class="fa-solid fa-check-double"></i> TINDAKAN ANDA</p>
            <h1>Persetujuan Saya</h1>
            <p>Tinjau dokumen yang menunggu keputusan Anda dan pilih tindakan yang sesuai.</p>
        </div>
        <div class="approvals-total"><i
               class="fa-solid fa-inbox"></i><span><b>{{ number_format($approvals->total()) }}</b>
                dokumen pada kategori ini</span></div>
    </section>
    <nav class="approvals-tabs"
         aria-label="Kategori persetujuan">
        @foreach ($tabs as $key => [$label, $icon])
            <a class="{{ $tab === $key ? 'is-active' : '' }}"
               href="{{ route('approvals.index', ['tab' => $key]) }}"><i
                   class="fa-solid {{ $icon }}"></i> {{ $label }}</a>
        @endforeach
    </nav>
    <section class="approvals-list">
        <div class="approvals-list-heading">
            <div>
                <h2>{{ $tabs[$tab][0] ?? 'Persetujuan' }}</h2>
                <p>
                    @if ($tab === 'pending')
                        Dokumen berikut memerlukan keputusan Anda.
                    @else
                        Riwayat keputusan yang telah Anda buat.
                    @endif
                </p>
            </div><span><i class="fa-solid fa-list-check"></i>
                {{ $approvals->firstItem() ?? 0 }}–{{ $approvals->lastItem() ?? 0 }} dari
                {{ $approvals->total() }}</span>
        </div>
        <div class="approvals-items">
            @forelse($approvals as $approval)
                @php($status = $approval->status)
                <article class="approval-card">
                    <div class="approval-document"><span class="approval-document-icon"><i
                               class="fa-solid fa-file-lines"></i></span>
                        <div><a
                               href="{{ route('documents.show', $approval->instance->document) }}">{{ $approval->instance->document->subject }}</a>
                            <p><i class="fa-solid fa-tag"></i>
                                {{ $approval->instance->document->type?->name ?? 'Dokumen' }}
                                <span>•</span> Langkah
                                {{ $approval->step->step_order }} <span>•</span> Putaran
                                {{ $approval->cycle }}
                            </p>
                        </div><a class="approval-open"
                           href="{{ route('documents.show', $approval->instance->document) }}"
                           aria-label="Buka dokumen"><i
                               class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    </div>
                    <div class="approval-status"><span
                              class="document-status status-{{ strtolower($status) }}"><i
                               class="fa-solid {{ $statusIcons[$status] ?? 'fa-circle-info' }}"></i>
                            {{ $statusLabels[$status] ?? $status }}</span>
                        @if ($approval->acted_at)
                            <small>Diputuskan
                                {{ $approval->acted_at->translatedFormat('d M Y, H:i') }}</small>
                        @endif
                    </div>
                    <div class="approval-note"><small>Catatan</small>
                        <p>{{ $approval->notes ?: ($status === 'PENDING' ? 'Belum ada catatan. Tambahkan catatan bila meminta revisi.' : 'Tidak ada catatan.') }}
                        </p>
                    </div>
                    <div class="approval-action">
                        @if ($status === 'PENDING')
                            <form method="post"
                                  action="{{ route('approvals.action', $approval) }}">
                                @csrf<label>Catatan
                                    keputusan
                                    <textarea name="notes"
                                              rows="2"
                                              placeholder="Wajib diisi saat meminta revisi"></textarea>
                                </label>
                                <div class="approval-buttons"><button class="btn approval-approve"
                                            name="action"
                                            value="APPROVE"><i class="fa-solid fa-check"></i>
                                        Setujui</button><button class="btn approval-revise"
                                            name="action"
                                            value="REVISION_REQUESTED"><i
                                           class="fa-solid fa-arrow-rotate-left"></i> Minta
                                        revisi</button><button class="btn approval-reject"
                                            name="action"
                                            value="REJECT"><i class="fa-solid fa-xmark"></i>
                                        Tolak</button></div>
                        </form>@else<a class="btn btn-secondary"
                               href="{{ route('documents.show', $approval->instance->document) }}"><i
                                   class="fa-solid fa-eye"></i> Lihat dokumen</a>
                        @endif
                    </div>
            </article>@empty<div class="approvals-empty"><i
                       class="fa-solid {{ $tab === 'pending' ? 'fa-circle-check' : 'fa-inbox' }}"></i><strong>{{ $tab === 'pending' ? 'Tidak ada dokumen yang menunggu keputusan' : 'Belum ada data pada kategori ini' }}</strong>
                    <p>{{ $tab === 'pending' ? 'Semua dokumen yang menjadi tanggung jawab Anda sudah ditangani.' : 'Keputusan Anda akan muncul di sini.' }}
                    </p><a class="btn btn-secondary"
                       href="{{ route('documents.index') }}"><i class="fa-solid fa-folder-open"></i>
                        Buka dokumen</a>
                </div>
            @endforelse
        </div>
        @if ($approvals->hasPages())
            <div class="approvals-pagination">{{ $approvals->links() }}</div>
        @endif
    </section>
@endsection
