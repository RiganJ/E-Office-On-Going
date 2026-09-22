@extends('layouts.app')
@section('content')
    @php
        $labels = [
            'DRAFT' => 'Draft',
            'DISPOSITIONED' => 'Didisposisikan',
            'IN_REVIEW' => 'Dalam review',
            'REVISION_REQUIRED' => 'Perlu revisi',
            'REJECTED' => 'Ditolak',
            'COMPLETED' => 'Selesai',
            'ARCHIVED' => 'Diarsipkan',
        ];
        $icons = [
            'DRAFT' => 'fa-pen-to-square',
            'DISPOSITIONED' => 'fa-share-nodes',
            'IN_REVIEW' => 'fa-clock',
            'REVISION_REQUIRED' => 'fa-arrow-rotate-left',
            'REJECTED' => 'fa-circle-xmark',
            'COMPLETED' => 'fa-circle-check',
            'ARCHIVED' => 'fa-box-archive',
        ];
        $status = $document->status;
    @endphp
    <a class="document-back"
       href="{{ route('documents.index') }}">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar dokumen</a>
    <section class="document-hero">
        <div class="document-hero-main"><span class="document-type-icon"><i
                   class="fa-solid fa-file-lines"></i></span>
            <div><span class="document-status status-{{ strtolower($status) }}"><i
                       class="fa-solid {{ $icons[$status] ?? 'fa-file-lines' }}"></i>
                    {{ $labels[$status] ?? $status }}</span>
                <h1>{{ $document->subject }}</h1>
                <p><i class="fa-solid fa-tag"></i> {{ $document->type->name }} <span>•</span> <i
                       class="fa-regular fa-user"></i> Dibuat oleh {{ $document->creator->name }}</p>
            </div>
        </div>
        <div class="document-hero-actions">
            @if (in_array($status, ['COMPLETED', 'ARCHIVED']))
                <a class="btn"
                   href="{{ route('documents.final-download', $document) }}"><i
                       class="fa-solid fa-file-pdf"></i> PDF final</a>
            @endif @can('update', $document)
            <a class="btn btn-secondary"
               href="{{ route('documents.edit', $document) }}"><i class="fa-solid fa-pen"></i>
                Ubah</a>
        @endcan <a class="btn btn-secondary"
           href="{{ route('dispositions.create', $document) }}"><i
               class="fa-solid fa-share-nodes"></i> Disposisi</a>
    </div>
</section>
@if (
    $status === 'DRAFT' &&
        $document->workflowInstance === null &&
        $availableWorkflows->isNotEmpty())
    <section class="document-notice notice-info"><span><i class="fa-solid fa-play"></i></span>
        <div><strong>Dokumen masih berupa draf</strong>
            <p>Pilih alur persetujuan agar dokumen dapat diproses oleh pihak yang berwenang.</p>
            @can('update', $document)
                <form method="post"
                      action="{{ route('documents.workflow.start', $document) }}">@csrf<label>Alur
                        persetujuan<select name="workflow_id"
                                required>
                            <option value="">Pilih alur persetujuan</option>
                            @foreach ($availableWorkflows as $workflow)
                                <option value="{{ $workflow->id }}">{{ $workflow->name }}</option>
                            @endforeach
                        </select>
                    </label><button class="btn"
                            type="submit"><i class="fa-solid fa-play"></i> Mulai proses</button>
                </form>
            @endcan
        </div>
    </section>
@endif
@if ($status === 'REVISION_REQUIRED')
    <section class="document-notice notice-warning"><span><i
               class="fa-solid fa-arrow-rotate-left"></i></span>
        <div><strong>Dokumen perlu diperbaiki</strong>
            <p>Lihat catatan pada riwayat approval, perbaiki isi dokumen, lalu kirim kembali untuk
                ditinjau.</p>
            @can('update', $document)
                <div class="notice-actions"><a class="btn btn-secondary"
                       href="{{ route('documents.edit', $document) }}"><i class="fa-solid fa-pen"></i>
                        Perbaiki dokumen</a>
                    <form method="post"
                          action="{{ route('documents.resubmit', $document) }}"
                          data-confirm="Kirim dokumen yang sudah diperbaiki untuk ditinjau kembali?">
                        @csrf<button class="btn"><i class="fa-solid fa-paper-plane"></i> Kirim
                            kembali</button></form>
                </div>
            @endcan
        </div>
    </section>
@endif
<section class="document-quick-info">
    <div><i class="fa-solid fa-hashtag"></i><span>Nomor
            dokumen</span><strong>{{ $document->number ?? 'Belum diterbitkan' }}</strong></div>
    <div><i class="fa-regular fa-calendar"></i><span>Tanggal
            dokumen</span><strong>{{ ($document->document_date ?? $document->created_at)->translatedFormat('d M Y') }}</strong>
    </div>
    <div><i class="fa-solid fa-building"></i><span>Unit
            kerja</span><strong>{{ $document->unit?->name ?? 'Belum ditetapkan' }}</strong></div>
    <div><i
           class="fa-solid fa-calendar-check"></i><span>Deadline</span><strong>{{ $document->deadline?->translatedFormat('d M Y, H:i') ?? 'Tidak ada' }}</strong>
    </div>
</section>
<div class="document-detail-layout">
    <main class="document-detail-main">
        <section class="document-panel">
            <header>
                <div><i class="fa-solid fa-align-left"></i>
                    <h2>Isi dokumen</h2>
                </div><span>{{ $document->content ? 'Tersedia' : 'Belum diisi' }}</span>
            </header>
            <div class="document-content">
                {{ $document->content ?: 'Isi atau keterangan dokumen belum ditambahkan.' }}
            </div>
        </section>
        <section class="document-panel">
            <header>
                <div><i class="fa-solid fa-diagram-project"></i>
                    <h2>Alur approval</h2>
                </div>
                @if ($document->workflowInstance)
                    <span>{{ $document->workflowInstance->workflow?->name ?? 'Workflow aktif' }}</span>
                @endif
            </header>
            <div class="document-timeline">
                @forelse($document->workflowInstance?->approvals ?? [] as $approval)
                    <article
                             class="document-timeline-item timeline-{{ strtolower($approval->status) }}">
                        <span><i
                               class="fa-solid {{ $approval->status === 'APPROVED' ? 'fa-check' : ($approval->status === 'REJECTED' ? 'fa-xmark' : ($approval->status === 'REVISION_REQUESTED' ? 'fa-arrow-rotate-left' : 'fa-clock')) }}"></i></span>
                        <div><strong>Langkah {{ $approval->step->step_order }} ·
                                {{ str_replace('_', ' ', $approval->status) }}</strong>
                            <p>{{ $approval->approver?->name ?? 'Approver' }} · Putaran
                                {{ $approval->cycle }} ·
                                {{ $approval->acted_at?->translatedFormat('d M Y, H:i') ?? 'Menunggu keputusan' }}
                            </p>
                            @if ($approval->notes)
                                <blockquote>{{ $approval->notes }}</blockquote>
                            @endif
                        </div>
                </article>@empty<div class="panel-empty"><i
                           class="fa-solid fa-diagram-project"></i>
                        <p>Dokumen belum masuk ke alur approval.</p>
                    </div>
                @endforelse
            </div>
        </section>
        <section class="document-panel">
            <header>
                <div><i class="fa-solid fa-clock-rotate-left"></i>
                    <h2>Aktivitas terakhir</h2>
                </div>
            </header>
            <div class="document-timeline compact">
                @forelse($document->activityLogs as $log)
                    <article class="document-timeline-item"><span><i
                               class="fa-solid fa-circle"></i></span>
                        <div>
                            <strong>{{ ucwords(strtolower(str_replace('_', ' ', $log->action))) }}</strong>
                            <p>{{ $log->user?->name ?? 'Sistem' }} ·
                                {{ $log->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                </article>@empty<div class="panel-empty"><i class="fa-regular fa-clock"></i>
                        <p>Belum ada aktivitas tercatat.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
    <aside class="document-detail-side">
        <section class="document-panel">
            <header>
                <div><i class="fa-solid fa-paperclip"></i>
                    <h2>Lampiran</h2>
                </div><span>{{ $document->files->count() }} file</span>
            </header>
            <div class="document-files">
                @forelse($document->files as $file)
                    <div><i class="fa-solid fa-file"></i><span>{{ $file->original_filename }}<small>{{ number_format($file->size / 1024, 1) }}
                            KB</small></span></div>@empty<div class="panel-empty"><i
                           class="fa-solid fa-paperclip"></i>
                        <p>Belum ada lampiran.</p>
                    </div>
                @endforelse
            </div>
            @if ($document->files->isNotEmpty())
                <a class="btn document-download"
                   href="{{ route('documents.download', $document) }}"><i
                       class="fa-solid fa-download"></i> Unduh lampiran terbaru</a>
            @endif
        </section>
        <section class="document-panel">
            <header>
                <div><i class="fa-solid fa-share-nodes"></i>
                    <h2>Disposisi</h2>
                </div><span>{{ $document->dispositions->count() }} tercatat</span>
            </header>
            <div class="document-timeline compact">
                @forelse($document->dispositions as $disposition)
                    <article class="document-timeline-item"><span><i
                               class="fa-solid fa-share"></i></span>
                        <div><strong>{{ $disposition->sender->name }} →
                                {{ $disposition->recipient?->name ?? 'Unit tujuan' }}</strong>
                            <p>{{ $disposition->instruction }}</p>
                            <small>{{ $disposition->status }} ·
                                {{ $disposition->created_at->translatedFormat('d M, H:i') }}</small>
                        </div>
                </article>@empty<div class="panel-empty"><i class="fa-solid fa-share-nodes"></i>
                        <p>Belum ada disposisi.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </aside>
</div>
@endsection
