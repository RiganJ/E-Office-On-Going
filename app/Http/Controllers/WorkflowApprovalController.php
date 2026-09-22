<?php

namespace App\Http\Controllers;

use App\Models\WorkflowApproval;
use App\Http\Requests\WorkflowActionRequest;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class WorkflowApprovalController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->string('tab', 'pending')->toString();
        $query = WorkflowApproval::with(['instance.document.type', 'step'])->where('approver_user_id', $request->user()->id)->latest();
        $query->when($tab === 'pending', fn ($q) => $q->where('status', 'PENDING'))
            ->when($tab === 'processed', fn ($q) => $q->whereIn('status', ['APPROVED', 'REVISION_REQUESTED']))
            ->when($tab === 'rejected', fn ($q) => $q->where('status', 'REJECTED'));
        return view('approvals.index', ['approvals' => $query->paginate(20)->withQueryString(), 'tab' => $tab]);
    }

    public function action(WorkflowActionRequest $request, WorkflowApproval $approval, WorkflowService $workflows)
    {
        $data = $request->validated();
        $workflows->act($approval->instance, $request->user(), $data['action'], $data['notes'] ?? null);
        $instance = $approval->instance->fresh('document');
        $message = match ($data['action']) {
            'REJECT' => 'Dokumen ditolak. Pembuat dokumen telah menerima pemberitahuan.',
            'REVISION_REQUESTED' => 'Revisi telah diminta. Dokumen dikembalikan kepada pembuat untuk diperbaiki.',
            'APPROVE' => $instance->document->status === 'COMPLETED'
                ? 'Dokumen disetujui dan proses telah selesai. Nomor serta dokumen final telah dibuat.'
                : ($instance->current_step > $approval->step->step_order
                    ? 'Dokumen disetujui. Dokumen diteruskan ke tahap persetujuan berikutnya.'
                    : 'Persetujuan Anda telah dicatat. Dokumen masih menunggu keputusan penyetuju lain pada tahap ini.'),
        };

        return back()->with('success', $message);
    }
}
