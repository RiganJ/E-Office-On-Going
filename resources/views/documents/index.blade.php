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
        $filters = collect(
            request()->only([
                'search',
                'unit_id',
                'document_type_id',
                'status',
                'year',
                'creator_id',
            ]),
        )
            ->filter(fn($value) => filled($value))
            ->count();
    @endphp
    <section class="documents-header">
        <div>
            <p class="documents-kicker"><i class="fa-solid fa-folder-open"></i> PUSAT DOKUMEN</p>
            <h1>Dokumen</h1>
            <p>Temukan, pantau, dan kelola seluruh surat serta pengajuan dalam satu tempat.</p>
        </div>
        @can('create', App\Models\Document::class)
            <a class="btn"
               href="{{ route('documents.create') }}"><i class="fa-solid fa-plus"></i> Buat dokumen</a>
        @endcan
    </section>
    <section class="documents-summary">
        <div class="documents-summary-card"><span class="summary-icon blue"><i
                   class="fa-solid fa-layer-group"></i></span>
            <div><small>Total
                    hasil</small><strong>{{ number_format($documents->total()) }}</strong><em>dokumen
                    ditemukan</em></div>
        </div>
        <div class="documents-summary-card"><span class="summary-icon amber"><i
                   class="fa-solid fa-hourglass-half"></i></span>
            <div><small>Perlu
                    perhatian</small><strong>{{ $documents->getCollection()->whereIn('status', ['IN_REVIEW', 'REVISION_REQUIRED'])->count() }}</strong><em>pada
                    halaman ini</em></div>
        </div>
        <div class="documents-summary-card"><span class="summary-icon green"><i
                   class="fa-solid fa-circle-check"></i></span>
            <div><small>Telah
                    selesai</small><strong>{{ $documents->getCollection()->whereIn('status', ['COMPLETED', 'ARCHIVED'])->count() }}</strong><em>pada
                    halaman ini</em></div>
        </div>
    </section>
    <details class="documents-filter"
             @if ($filters) open @endif>
        <summary><span><i class="fa-solid fa-sliders"></i> Filter dokumen @if ($filters)
                    <b>{{ $filters }}</b>
                @endif
            </span>
            <small>Persempit hasil pencarian <i class="fa-solid fa-chevron-down"></i></small>
        </summary>
        <form method="get"
              class="documents-filter-form"><label class="filter-search"><span>Cari dokumen</span>
                <div><i class="fa-solid fa-magnifying-glass"></i><input name="search"
                           value="{{ request('search') }}"
                           placeholder="Nomor surat atau perihal"></div>
            </label><label><span>Unit kerja</span><select name="unit_id">
                    <option value="">Semua unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}"
                                @selected(request('unit_id') == $unit->id)>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </label><label><span>Jenis dokumen</span><select name="document_type_id">
                    <option value="">Semua jenis</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}"
                                @selected(request('document_type_id') == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </label><label><span>Status</span><select name="status">
                    <option value="">Semua status</option>
                    @foreach ($labels as $status => $label)
                        <option value="{{ $status }}"
                                @selected(request('status') === $status)>{{ $label }}</option>
                    @endforeach
                </select>
            </label><label><span>Tahun</span><input type="number"
                       name="year"
                       value="{{ request('year') }}"
                       placeholder="Contoh: 2026"
                       min="2000"
                       max="2100"></label>
            @if (auth()->user()->hasPermission('view all documents'))
                <label><span>Pembuat</span><select name="creator_id">
                        <option value="">Semua pembuat</option>
                        @foreach ($creators as $creator)
                            <option value="{{ $creator->id }}"
                                    @selected(request('creator_id') == $creator->id)>{{ $creator->name }}</option>
                        @endforeach
                    </select>
                </label>
            @endif
            <label>
                <span>Tampilkan</span><select name="per_page">
                    <option value="15">15 baris</option>
                    <option value="25"
                            @selected(request('per_page') == 25)>25 baris</option>
                    <option value="50"
                            @selected(request('per_page') == 50)>50 baris</option>
                </select></label>
            <div class="filter-actions"><a class="btn btn-secondary"
                   href="{{ route('documents.index') }}"><i class="fa-solid fa-rotate-left"></i>
                    Reset</a><button class="btn"><i class="fa-solid fa-magnifying-glass"></i>
                    Terapkan filter</button></div>
        </form>
    </details>
    <section class="documents-list">
        <div class="documents-list-heading">
            <div>
                <h2>Daftar dokumen</h2>
                <p>Menampilkan {{ $documents->firstItem() ?? 0 }}–{{ $documents->lastItem() ?? 0 }}
                    dari
                    {{ number_format($documents->total()) }} dokumen.</p>
            </div><span><i class="fa-solid fa-table-list"></i> Terbaru terlebih dahulu</span>
        </div>
        <div class="overflow-x-auto">
            <table class="documents-table">
                <thead>
                    <tr>
                        <th>Dokumen</th>
                        <th>Jenis & unit</th>
                        <th>Tanggal</th>
                        <th>Status proses</th>
                        <th>Deadline</th>
                        <th><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                        @php($status = $document->status)@php($deadline = $document->deadline_badge)
                        <tr>
                            <td class="document-main"><a
                                   href="{{ route('documents.show', $document) }}">{{ $document->subject }}</a><small><i
                                       class="fa-solid fa-hashtag"></i>
                                    {{ $document->number ?? 'Belum bernomor' }}</small><small><i
                                       class="fa-regular fa-user"></i>
                                    {{ $document->creator->name }}</small></td>
                            <td><b>{{ $document->type->name }}</b><small>{{ $document->unit?->name ?? 'Tanpa unit' }}</small>
                            </td>
                            <td><time>{{ ($document->document_date ?? $document->created_at)->translatedFormat('d M Y') }}</time><small>Dibuat
                                    {{ $document->created_at->diffForHumans() }}</small></td>
                            <td><span class="document-status status-{{ strtolower($status) }}"><i
                                       class="fa-solid {{ $icons[$status] ?? 'fa-file-lines' }}"></i>
                                    {{ $labels[$status] ?? $status }}</span>
                                @if ($document->workflowInstance?->current_step)
                                    <small>Langkah
                                        {{ $document->workflowInstance->current_step }}</small>
                                @endif
                            </td>
                            <td><span class="deadline deadline-{{ strtolower($deadline) }}"><i
                                       class="fa-solid {{ $deadline === 'OVERDUE' ? 'fa-triangle-exclamation' : ($deadline === 'DUE_SOON' ? 'fa-clock' : 'fa-calendar-check') }}"></i>
                                    {{ $deadline === 'DUE_SOON' ? 'Segera' : ($deadline === 'OVERDUE' ? 'Terlambat' : ($deadline === 'COMPLETED' ? 'Selesai' : 'Normal')) }}</span>
                                @if ($document->deadline)
                                    <small>{{ $document->deadline->translatedFormat('d M Y, H:i') }}</small>
                                @endif
                            </td>
                            <td class="document-action"><a class="btn btn-secondary"
                                   href="{{ route('documents.show', $document) }}"
                                   aria-label="Lihat detail {{ $document->subject }}"><i
                                       class="fa-solid fa-arrow-right"></i><span>Detail</span></a></td>
                    </tr>@empty<tr>
                            <td colspan="6">
                                <div class="documents-empty"><i
                                       class="fa-regular fa-folder-open"></i><strong>Belum ada
                                        dokumen yang sesuai</strong>
                                    <p>Coba ubah filter pencarian atau buat dokumen baru.</p>
                                    @can('create', App\Models\Document::class)
                                        <a class="btn"
                                           href="{{ route('documents.create') }}"><i
                                               class="fa-solid fa-plus"></i> Buat dokumen</a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($documents->hasPages())
            <div class="documents-pagination">{{ $documents->links() }}</div>
        @endif
    </section>
@endsection
