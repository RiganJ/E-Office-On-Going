@extends('layouts.app')
@section('content')
    <div class="mb-6 flex items-start justify-between">
        <div><span class="badge">{{ $letter->status }}</span>
            <h1 class="mt-2 text-2xl font-bold">{{ $letter->subject }}</h1>
            <p class="text-slate-500">Surat Keluar · {{ $letter->creator->name }}</p>
        </div><a class="btn"
           href="{{ route('outgoing-letters.index') }}">Kembali</a>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-xl bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="font-semibold">Draft Surat</h2>
            <div class="mt-5 whitespace-pre-line">{{ $letter->content }}</div>
            @if ($letter->status === 'REVISION_REQUIRED' && $letter->creator_id === auth()->id())
                <form method="post"
                      action="{{ route('outgoing-letters.resubmit', $letter) }}"
                      class="mt-6">@csrf<button class="btn">Kirim Kembali Setelah Revisi</button>
                </form>
            @endif
        </section>
        <section class="rounded-xl bg-white p-6 shadow-sm">
            <h2 class="font-semibold">Workflow</h2>
            @if ($letter->workflowInstance)
                <p class="mt-2 text-sm">Status: <span
                          class="badge">{{ $letter->workflowInstance->status }}</span></p>
                <div class="mt-4 space-y-3">
                    @foreach ($letter->workflowInstance->workflow->steps as $step)
                        <div class="border-l-2 border-cyan-500 pl-3 text-sm"><b>Langkah
                                {{ $step->step_order }}</b>
                            <p>{{ $step->approver_type }}: {{ $step->approver_reference }}</p>
                        </div>
                    @endforeach
                </div>
            @else<p class="mt-4 text-sm text-slate-500">Belum disubmit ke workflow.</p>
            @endif
        </section>
    </div>
    @if ($letter->workflowInstance)
        <section class="mt-6 rounded-xl bg-white p-6 shadow-sm">
            <h2 class="font-semibold">Riwayat Approval</h2>
            <table class="mt-4">
                <thead>
                    <tr>
                        <th>Langkah</th>
                        <th>Approver</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($letter->workflowInstance->approvals as $approval)
                        <tr>
                            <td>{{ $approval->step->step_order }} / putaran {{ $approval->cycle }}
                            </td>
                            <td>{{ $approval->approver?->name ?? 'Tidak tersedia' }}</td>
                            <td>{{ $approval->status }}</td>
                            <td>{{ $approval->notes ?? '—' }}</td>
                            <td>{{ ($approval->acted_at ?? $approval->created_at)->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif
    <section class="mt-6 rounded-xl bg-white p-6 shadow-sm">
        <h2 class="font-semibold">Timeline</h2>
        <div class="mt-4 space-y-4">
            @forelse($letter->activityLogs as $log)
                <div class="border-l-2 border-cyan-500 pl-3 text-sm">
                    <b>{{ str_replace('_', ' ', $log->action) }}</b>
                    <p class="text-slate-500">{{ $log->user?->name ?? 'Sistem' }} ·
                        {{ $log->created_at->format('d M Y H:i') }}</p>
            </div>@empty<p class="text-sm text-slate-500">Belum ada aktivitas.</p>
            @endforelse
        </div>
    </section>
@endsection
