<?php

namespace App\Http\Controllers;

use App\Models\{Document, DocumentType, IncomingLetter, Unit, Workflow};
use App\Services\{ActivityLogService, WorkflowService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IncomingLetterController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->hasPermission('view documents'), 403);

        $query = IncomingLetter::with(['document.creator', 'document.unit'])->latest('received_date');
        if (! $request->user()->hasPermission('view all documents')) {
            $query->whereHas('document', fn ($document) => $document->where('creator_id', $request->user()->id));
        }

        return view('incoming_letters.index', ['letters' => $query->paginate(15)]);
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->hasPermission('create documents'), 403);

        $type = DocumentType::where('code', 'INCOMING')->firstOrFail();

        return view('incoming_letters.form', [
            'units' => Unit::orderBy('name')->get(),
            'workflows' => Workflow::with('steps')->where('document_type_id', $type->id)->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, WorkflowService $workflows)
    {
        abort_unless($request->user()->hasPermission('create documents'), 403);

        $data = $request->validate([
            'unit_id' => ['nullable', 'exists:units,id'],
            'sender' => ['required', 'string', 'max:255'],
            'sender_institution' => ['nullable', 'string', 'max:255'],
            'letter_number' => ['nullable', 'string', 'max:255'],
            'letter_date' => ['nullable', 'date'],
            'received_date' => ['required', 'date'],
            'nature' => ['required', 'in:BIASA,PENTING,SEGERA,RAHASIA'],
            'classification' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'attachment' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'workflow_id' => ['required', 'exists:workflows,id'],
        ]);

        $incoming = DB::transaction(function () use ($data, $request, $workflows) {
            $type = DocumentType::where('code', 'INCOMING')->firstOrFail();
            $document = Document::create([
                'document_type_id' => $type->id,
                'unit_id' => $data['unit_id'],
                'creator_id' => $request->user()->id,
                'subject' => $data['subject'],
                'content' => $data['content'],
                'document_date' => $data['letter_date'],
                'status' => 'RECEIVED',
                'uuid' => (string) Str::uuid(),
                'verification_token' => Str::random(48),
            ]);
            $agenda = 'SM-'.now()->format('Ymd').'-'.str_pad((string) $document->id, 6, '0', STR_PAD_LEFT);
            $incoming = IncomingLetter::create([...collect($data)->only(['sender', 'sender_institution', 'letter_number', 'letter_date', 'received_date', 'nature', 'classification'])->all(), 'document_id' => $document->id, 'agenda_number' => $agenda]);
            $file = $request->file('attachment');
            $path = $file->store('eoffice/'.$document->uuid, 'local');
            $document->files()->create(['original_filename' => $file->getClientOriginalName(), 'stored_filename' => basename($path), 'disk' => 'local', 'path' => $path, 'mime_type' => $file->getMimeType() ?? 'application/pdf', 'size' => $file->getSize(), 'checksum' => hash_file('sha256', $file->getRealPath()), 'uploaded_by' => $request->user()->id]);
            ActivityLogService::record($document, 'CREATE_INCOMING_LETTER', ['agenda_number' => $agenda, 'sender' => $incoming->sender, 'nature' => $incoming->nature]);

            $workflows->start($document, Workflow::findOrFail($data['workflow_id']), $request->user());

            return $incoming;
        });

        return redirect()->route('incoming-letters.show', $incoming->document)->with('success', 'Surat masuk berhasil dicatat dan dikirim ke alur disposisi.');
    }

    public function show(Request $request, Document $incomingLetter)
    {
        abort_unless($incomingLetter->type?->code === 'INCOMING', 404);
        $this->authorize('view', $incomingLetter);

        return view('incoming_letters.show', ['letter' => $incomingLetter->load(['incomingLetter', 'creator', 'unit', 'files', 'activityLogs.user', 'dispositions.sender', 'dispositions.recipient'])]);
    }
}
