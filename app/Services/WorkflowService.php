<?php

namespace App\Services;

use App\Models\{Document, User, Workflow, WorkflowApproval, WorkflowInstance, WorkflowStep};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkflowService
{
    public function start(Document $document, Workflow $workflow, User $startedBy): WorkflowInstance
    {
        if ($workflow->document_type_id !== $document->document_type_id || !$workflow->is_active) {
            throw ValidationException::withMessages(['workflow_id' => 'Workflow tidak dapat digunakan untuk jenis dokumen ini.']);
        }
        if ($workflow->unit_id && $workflow->unit_id !== $document->unit_id) {
            throw ValidationException::withMessages(['workflow_id' => 'Workflow tidak berlaku untuk unit dokumen ini.']);
        }

        return DB::transaction(function () use ($document, $workflow, $startedBy) {
            $instance = WorkflowInstance::create([
                'workflow_id' => $workflow->id, 'document_id' => $document->id,
                'current_step' => 1, 'status' => 'IN_REVIEW', 'started_by' => $startedBy->id, 'started_at' => now(),
            ]);
            $document->update(['status' => 'IN_REVIEW']);
            $this->createPendingApprovals($instance, $workflow->steps()->where('step_order', 1)->firstOrFail());
            NotificationService::approvers($instance, $document, 'APPROVAL_NEW');
            ActivityLogService::record($document, 'WORKFLOW_STARTED', ['workflow_id' => $workflow->id]);
            return $instance;
        });
    }

    public function act(WorkflowInstance $instance, User $user, string $action, ?string $notes = null): void
    {
        DB::transaction(function () use ($instance, $user, $action, $notes) {
            $instance->refresh();
            if ($instance->status !== 'IN_REVIEW') abort(422, 'Workflow tidak sedang menunggu approval.');
            $step = $instance->workflow->steps()->where('step_order', $instance->current_step)->firstOrFail();
            $approval = $instance->approvals()->where('workflow_step_id', $step->id)->where('approver_user_id', $user->id)->where('status', 'PENDING')->lockForUpdate()->first();
            abort_unless($approval, 403, 'Anda bukan approver pada langkah aktif.');

            if ($action === 'REJECT') {
                abort_unless($step->allow_reject, 422, 'Penolakan tidak diizinkan pada langkah ini.');
                $approval->update(['status' => 'REJECTED', 'notes' => $notes, 'acted_at' => now()]);
                $instance->update(['status' => 'REJECTED', 'completed_at' => now()]);
                $instance->document->update(['status' => 'REJECTED']);
                NotificationService::user($instance->document->creator, $instance->document, 'DOCUMENT_REJECTED', $notes);
            } elseif ($action === 'REVISION_REQUESTED') {
                abort_unless($step->allow_revision, 422, 'Revisi tidak diizinkan pada langkah ini.');
                $approval->update(['status' => 'REVISION_REQUESTED', 'notes' => $notes, 'acted_at' => now()]);
                $instance->update(['status' => 'REVISION_REQUIRED']);
                $instance->document->update(['status' => 'REVISION_REQUIRED']);
                NotificationService::user($instance->document->creator, $instance->document, 'REVISION_REQUESTED', $notes);
            } else {
                $approval->update(['status' => 'APPROVED', 'notes' => $notes, 'acted_at' => now()]);
                $pending = $instance->approvals()->where('workflow_step_id', $step->id)->where('status', 'PENDING')->exists();
                if ($pending && $step->is_required) return;
                $this->advance($instance);
            }
            ActivityLogService::record($instance->document, 'WORKFLOW_'.$action, ['step' => $step->step_order, 'notes' => $notes]);
        });
    }

    public function resubmit(WorkflowInstance $instance, User $user): void
    {
        abort_unless($instance->document->creator_id === $user->id, 403);
        abort_unless($instance->status === 'REVISION_REQUIRED', 422, 'Dokumen tidak sedang memerlukan revisi.');
        DB::transaction(function () use ($instance) {
            $step = $instance->workflow->steps()->where('step_order', $instance->current_step)->firstOrFail();
            $this->createPendingApprovals($instance, $step, $instance->approvals()->max('cycle') + 1);
            $instance->update(['status' => 'IN_REVIEW']);
            $instance->document->update(['status' => 'IN_REVIEW']);
            NotificationService::approvers($instance, $instance->document, 'DOCUMENT_RESUBMITTED');
            ActivityLogService::record($instance->document, 'WORKFLOW_RESUBMITTED', ['step' => $step->step_order]);
        });
    }

    private function advance(WorkflowInstance $instance): void
    {
        $next = $instance->workflow->steps()->where('step_order', '>', $instance->current_step)->orderBy('step_order')->first();
        if (!$next) {
            $document = $instance->document;
            app(NumberingService::class)->assign($document);
            $generated = app(DocumentGenerationService::class)->generate($document->fresh(['type', 'unit', 'metadata']));
            app(SignatureService::class)->sign($generated);
            $instance->update(['status' => 'COMPLETED', 'completed_at' => now()]);
            $document->update(['status' => 'COMPLETED', 'archived_at' => now()]);
            NotificationService::user($document->creator, $document, 'DOCUMENT_COMPLETED', 'Dokumen telah disetujui, diberi nomor, dan diarsipkan.');
            return;
        }
        $instance->update(['current_step' => $next->step_order]);
        $this->createPendingApprovals($instance, $next);
        NotificationService::approvers($instance, $instance->document, 'APPROVAL_NEW');
    }

    private function createPendingApprovals(WorkflowInstance $instance, WorkflowStep $step, int $cycle = 1): void
    {
        $approvers = $this->resolveApprovers($step, $instance->document);
        if ($approvers->isEmpty()) abort(422, "Tidak ada approver untuk langkah {$step->step_order}.");
        foreach ($approvers as $approver) {
            WorkflowApproval::create(['workflow_instance_id' => $instance->id, 'workflow_step_id' => $step->id, 'cycle' => $cycle, 'approver_user_id' => $approver['user_id'], 'approver_position_id' => $approver['position_id'], 'status' => 'PENDING']);
        }
    }

    private function resolveApprovers(WorkflowStep $step, Document $document): Collection
    {
        $reference = $step->approver_reference;
        if ($step->approver_type === 'USER') return User::whereKey($reference)->get()->map(fn ($u) => ['user_id' => $u->id, 'position_id' => null]);
        if ($step->approver_type === 'ROLE') return User::whereHas('roles', fn ($q) => $q->where('id', $reference)->orWhere('name', $reference))->get()->map(fn ($u) => ['user_id' => $u->id, 'position_id' => null]);
        if ($step->approver_type === 'PERMISSION') return User::whereHas('roles.permissions', fn ($q) => $q->where('id', $reference)->orWhere('name', $reference))->get()->map(fn ($u) => ['user_id' => $u->id, 'position_id' => null]);
        $unitId = $step->approver_type === 'UNIT_POSITION' ? $document->unit_id : null;
        return DB::table('employee_positions')->join('employees', 'employees.id', '=', 'employee_positions.employee_id')
            ->where('employee_positions.position_id', $reference)->when($unitId, fn ($q) => $q->where('employee_positions.unit_id', $unitId))
            ->get(['employees.user_id', 'employee_positions.position_id'])->map(fn ($row) => ['user_id' => $row->user_id, 'position_id' => $row->position_id]);
    }
}
