<?php
namespace App\Http\Controllers;
use App\Models\{Document,Workflow}; use App\Services\WorkflowService; use Illuminate\Http\Request;
class DocumentWorkflowController extends Controller {
 public function start(Request $request,Document $document,WorkflowService $workflows){$this->authorize('update',$document);abort_unless($document->status==='DRAFT',422,'Hanya dokumen draft yang dapat dimulai ke workflow.');$data=$request->validate(['workflow_id'=>['required','exists:workflows,id']]);$workflow=Workflow::findOrFail($data['workflow_id']);$workflows->start($document,$workflow,$request->user());return back()->with('success','Workflow dimulai. Approval pertama sudah dibuat.');}
}
