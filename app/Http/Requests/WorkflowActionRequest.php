<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class WorkflowActionRequest extends FormRequest { public function authorize():bool{return $this->user()?->hasPermission('approve documents')??false;} public function rules():array{return ['action'=>['required','in:APPROVE,REJECT,REVISION_REQUESTED'],'notes'=>['nullable','string','max:5000','required_if:action,REVISION_REQUESTED']];} }
