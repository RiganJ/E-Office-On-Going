<?php

namespace App\Http\Controllers;

use App\Models\{DocumentType, NumberingRule, Unit};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NumberingRuleController extends Controller
{
    private const SUPPORTED_TYPES = ['ASSIGNMENT', 'OUTGOING'];

    public function index()
    {
        $this->guard();
        $types = DocumentType::whereIn('code', self::SUPPORTED_TYPES)->get()->keyBy('code');
        $rules = NumberingRule::with(['documentType', 'unit'])->whereIn('document_type_id', $types->pluck('id'))->get()->groupBy(fn (NumberingRule $rule) => $rule->documentType->code);

        return view('numbering_rules.index', ['assignmentRules' => $rules->get('ASSIGNMENT', collect()), 'outgoingRules' => $rules->get('OUTGOING', collect()), 'types' => $types, 'units' => Unit::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $this->guard();
        NumberingRule::create($this->validated($request));
        return back()->with('success', 'Aturan nomor surat berhasil ditambahkan.');
    }

    public function update(Request $request, NumberingRule $numberingRule)
    {
        $this->guard();
        $numberingRule->update($this->validated($request, $numberingRule));
        return back()->with('success', 'Aturan nomor surat berhasil diperbarui.');
    }

    public function destroy(NumberingRule $numberingRule)
    {
        $this->guard();
        $numberingRule->delete();
        return back()->with('success', 'Aturan nomor surat berhasil dihapus.');
    }

    private function validated(Request $request, ?NumberingRule $rule = null): array
    {
        $data = $request->validate(['document_type_id' => ['required', Rule::in(DocumentType::whereIn('code', self::SUPPORTED_TYPES)->pluck('id'))], 'unit_id' => ['nullable', 'exists:units,id'], 'format' => ['required', 'string', 'max:255'], 'reset_period' => ['required', 'in:YEARLY,MONTHLY'], 'is_active' => ['nullable', 'boolean']]);
        $exists = NumberingRule::where('document_type_id', $data['document_type_id'])->where('unit_id', $data['unit_id'] ?? null)->when($rule, fn ($query) => $query->whereKeyNot($rule))->exists();
        abort_if($exists, 422, 'Aturan untuk jenis surat dan unit ini sudah tersedia.');
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }

    private function guard(): void
    {
        abort_unless(auth()->user()->hasPermission('manage master data'), 403);
    }
}
