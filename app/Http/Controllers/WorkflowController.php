<?php

namespace App\Http\Controllers;

use App\Models\{DocumentType, Permission, Position, Role, Unit, User, Workflow};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkflowController extends Controller
{
    public function index() { $this->guard(); return view('workflows.index', ['workflows' => Workflow::with('documentType', 'unit', 'steps')->latest()->paginate(20)]); }
    public function create() { $this->guard(); return view('workflows.form', $this->formData()); }
    public function store(Request $request) { $this->guard(); $workflow = $this->save($request); return redirect()->route('workflows.edit', $workflow)->with('success', 'Workflow dibuat.'); }
    public function edit(Workflow $workflow) { $this->guard(); return view('workflows.form', $this->formData($workflow)); }
    public function update(Request $request, Workflow $workflow) { $this->guard(); $this->save($request, $workflow); return back()->with('success', 'Workflow diperbarui.'); }

    private function save(Request $request, ?Workflow $workflow = null): Workflow
    {
        $request->merge(['steps' => collect($request->input('steps', []))->filter(fn ($step) => filled($step['approver_reference'] ?? null))->values()->all()]);
        $data = $request->validate(['name'=>['required','string','max:255'],'code'=>['required','alpha_dash','max:100', Rule::unique('workflows','code')->ignore($workflow)],'document_type_id'=>['required','exists:document_types,id'],'unit_id'=>['nullable','exists:units,id'],'is_active'=>['boolean'],'steps'=>['required','array','min:1'],'steps.*.approver_type'=>['required','in:USER,POSITION,UNIT_POSITION,ROLE,PERMISSION'],'steps.*.approver_reference'=>['required','string','max:255'],'steps.*.is_required'=>['nullable','boolean'],'steps.*.allow_reject'=>['nullable','boolean'],'steps.*.allow_revision'=>['nullable','boolean']]);
        return DB::transaction(function () use ($data, $workflow) { $workflow = Workflow::updateOrCreate(['id' => $workflow?->id], collect($data)->except('steps')->all()); $workflow->steps()->delete(); foreach ($data['steps'] as $order => $step) $workflow->steps()->create(['step_order'=>$order + 1, 'action'=>'APPROVE'] + $step); return $workflow; });
    }
    private function guard(): void { abort_unless(auth()->user()->hasPermission('manage workflows'), 403); }
    private function formData(?Workflow $workflow = null): array { return compact('workflow') + ['types'=>DocumentType::orderBy('name')->get(),'units'=>Unit::orderBy('name')->get(),'users'=>User::orderBy('name')->get(),'positions'=>Position::orderBy('name')->get(),'roles'=>Role::orderBy('label')->get(),'permissions'=>Permission::orderBy('label')->get()]; }
}
