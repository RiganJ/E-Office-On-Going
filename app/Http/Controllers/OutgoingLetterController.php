<?php
namespace App\Http\Controllers;

use App\Models\{Document,DocumentType,Unit,Workflow,WorkflowInstance};
use App\Services\ActivityLogService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OutgoingLetterController extends Controller
{
    public function index()
    {
        $type = DocumentType::where('code', 'OUTGOING')->firstOrFail();
        return view('outgoing_letters.index', ['letters' => Document::with(['creator','unit','workflowInstance'])->where('document_type_id', $type->id)->latest()->paginate(15)]);
    }
    public function create()
    {
        abort_unless(auth()->user()->hasPermission('create documents'), 403);
        $type = DocumentType::where('code', 'OUTGOING')->firstOrFail();
        return view('outgoing_letters.form', ['units' => Unit::orderBy('name')->get(), 'workflows' => Workflow::with('steps')->where('document_type_id', $type->id)->where('is_active', true)->get()]);
    }
    public function store(Request $request, WorkflowService $workflows)
    {
        abort_unless($request->user()->hasPermission('create documents'), 403);
        $data = $request->validate(['unit_id'=>['nullable','exists:units,id'],'subject'=>['required','string','max:255'],'content'=>['required','string'],'document_date'=>['nullable','date'],'workflow_id'=>['required','exists:workflows,id'],'attachment'=>['nullable','file','mimes:pdf','max:10240']]);
        $type = DocumentType::where('code','OUTGOING')->firstOrFail();
        $letter = DB::transaction(function () use ($data, $request, $type, $workflows) { $letter=Document::create(['document_type_id'=>$type->id,'unit_id'=>$data['unit_id'],'creator_id'=>$request->user()->id,'subject'=>$data['subject'],'content'=>$data['content'],'document_date'=>$data['document_date'],'status'=>'DRAFT','uuid'=>(string) Str::uuid(),'verification_token'=>Str::random(48)]); if ($request->hasFile('attachment')) { $file=$request->file('attachment'); $path=$file->store('eoffice/'.$letter->uuid,'local'); $letter->files()->create(['original_filename'=>$file->getClientOriginalName(),'stored_filename'=>basename($path),'disk'=>'local','path'=>$path,'mime_type'=>$file->getMimeType() ?? 'application/pdf','size'=>$file->getSize(),'checksum'=>hash_file('sha256',$file->getRealPath()),'uploaded_by'=>$request->user()->id]); } ActivityLogService::record($letter,'CREATE_OUTGOING_LETTER',['status'=>'DRAFT','workflow_id'=>$data['workflow_id'] ?? null,'has_attachment'=>$request->hasFile('attachment')]); if (!empty($data['workflow_id'])) $workflows->start($letter, Workflow::findOrFail($data['workflow_id']), $request->user()); return $letter; });
        return redirect()->route('outgoing-letters.show',$letter)->with('success','Surat keluar berhasil disimpan. Rekam surat tetap tersedia untuk ditinjau di menu Surat Keluar dan Dokumen.');
    }
    public function show(Document $outgoingLetter)
    {
        abort_unless($outgoingLetter->type?->code === 'OUTGOING', 404);
        $this->authorize('view', $outgoingLetter);
        return view('outgoing_letters.show',['letter'=>$outgoingLetter->load(['creator','unit','files','workflowInstance.workflow.steps','workflowInstance.approvals.approver','workflowInstance.approvals.step','activityLogs.user'])]);
    }

    public function resubmit(Request $request, Document $outgoingLetter, WorkflowService $workflows)
    {
        abort_unless($outgoingLetter->type?->code === 'OUTGOING', 404);
        $workflows->resubmit($outgoingLetter->workflowInstance()->firstOrFail(), $request->user());
        return back()->with('success', 'Surat telah dikirim kembali untuk direview.');
    }
}
