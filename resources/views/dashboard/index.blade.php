@extends('layouts.app')
@section('content')
    <div class="ops-dashboard">
        <div class="ops-header">
            <div>
                <p class="ops-kicker">E-OFFICE / RINGKASAN OPERASIONAL</p>
                <h1>Dashboard Administrasi</h1>
                <p>Monitor seluruh proses dokumen universitas dalam satu tempat.</p>
            </div>
            <div class="ops-actions"><span class="ops-live"><i></i>
                    Operasional</span><span>{{ now()->translatedFormat('d M Y') }}</span><a
                   class="ops-new"
                   href="{{ route('documents.create') }}">+ Dokumen baru</a></div>
        </div>
        <div class="ops-tabs"><b>Overview</b><span>Performa proses</span><span>Dokumen &
                arsip</span><span class="ops-range">7
                hari terakhir</span></div>
        <section class="ops-summary">
            <div class="ops-card accent-blue">
                <div><small>Total
                        dokumen</small><strong>{{ number_format($totalDocuments) }}</strong><em>Semua
                        dokumen
                        terdaftar</em></div><span>▣</span>
            </div>
            <div class="ops-card accent-amber">
                <div><small>Menunggu
                        proses</small><strong>{{ number_format($pending + $dispositions) }}</strong><em>{{ $pending }}
                        approval · {{ $dispositions }} disposisi</em></div><span>◷</span>
            </div>
            <div class="ops-card accent-green">
                <div><small>Selesai /
                        diarsipkan</small><strong>{{ number_format($completed) }}</strong><em>Dokumen
                        telah
                        tuntas</em></div><span>✓</span>
            </div>
            <div class="ops-card accent-violet">
                <div><small>Masuk hari
                        ini</small><strong>{{ number_format($documents) }}</strong><em>Aktivitas
                        harian</em>
                </div><span>↗</span>
            </div>
        </section>
        <section class="ops-panel chart-panel">
            <div class="panel-title">
                <div>
                    <h2>Aktivitas dokumen</h2>
                    <p>Dokumen dibuat dalam tujuh hari terakhir</p>
                </div><b>{{ $daily->sum('count') }} total</b>
            </div>
            <div class="bar-chart">
                @foreach ($daily as $item)
                    <div class="bar-item">
                        <div class="bar-tooltip">{{ $item['count'] }}</div>
                        <div class="bar"
                             style="height: {{ max(5, ($item['count'] / $chartMax) * 100) }}%"></div>
                        <span>{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>
        <div class="ops-grid">
            <section class="ops-panel">
                <div class="panel-title">
                    <div>
                        <h2>Proses berjalan</h2>
                        <p>Status pekerjaan yang membutuhkan perhatian</p>
                    </div><a href="{{ route('dispositions.index') }}">Lihat semua</a>
                </div>
                <div class="process-row"><span class="process-icon blue">✓</span>
                    <div><b>Approval dokumen</b><small>{{ $pending }} menunggu keputusan
                            Anda</small></div>
                    <strong>{{ $pending }}</strong>
                </div>
                <div class="process-row"><span class="process-icon orange">→</span>
                    <div><b>Disposisi aktif</b><small>{{ $dispositions }} perlu ditindaklanjuti</small>
                    </div>
                    <strong>{{ $dispositions }}</strong>
                </div>
                <div class="process-row"><span class="process-icon green">▣</span>
                    <div><b>Dokumen hari ini</b><small>Dokumen baru masuk sistem</small></div>
                    <strong>{{ $documents }}</strong>
                </div>
            </section>
            <section class="ops-panel">
                <div class="panel-title">
                    <div>
                        <h2>Kalender & deadline</h2>
                        <p>Jadwal dokumen mendatang</p>
                    </div><span class="calendar-date">{{ now()->translatedFormat('M Y') }}</span>
                </div>
                <div class="mini-calendar">
                    <span>Sn</span><span>Sl</span><span>Sa</span><span>Ra</span><span>Km</span><span>Jm</span><span>Sb</span>
                    @for ($i = 1; $i <= now()->daysInMonth; $i++)
                        <b class="{{ $i === now()->day ? 'today' : '' }}">{{ $i }}</b>
                    @endfor
                </div>
                <div class="deadline-list">
                    @forelse($deadlines as $d)
                        <a
                       href="{{ route('documents.show', $d) }}"><i></i><span>{{ $d->subject }}<small>{{ $d->deadline->translatedFormat('d M · H:i') }}</small></span></a>@empty
                        <p>Belum ada deadline mendatang.</p>
                    @endforelse
                </div>
            </section>
        </div>
        <section class="ops-panel activity-panel">
            <div class="panel-title">
                <div>
                    <h2>Aktivitas terbaru</h2>
                    <p>Dokumen dan pengajuan terakhir yang tercatat</p>
                </div><a href="{{ route('documents.index') }}">Buka dokumen</a>
            </div>
            <div class="activity-list">
                @forelse($recent as $d)
                    <a href="{{ route('documents.show', $d) }}"><span
                              class="activity-avatar">{{ strtoupper(substr($d->creator->name, 0, 1)) }}</span>
                        <div><b>{{ $d->subject }}</b>
                            <p>{{ $d->creator->name }} · {{ $d->type->name }}</p>
                        </div>
                        <time>{{ $d->created_at->diffForHumans() }}</time><em>{{ $d->status }}</em>
                </a>@empty<p class="empty-activity">Belum ada aktivitas dokumen.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
