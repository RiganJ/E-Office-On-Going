<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreDispositionRequest extends FormRequest { public function authorize():bool{return $this->user()?->hasPermission('view documents')??false;} public function rules():array{return ['to_user_id'=>['nullable','exists:users,id'],'to_unit_id'=>['nullable','exists:units,id'],'to_position_id'=>['nullable','exists:positions,id'],'from_position_id'=>['nullable','exists:positions,id'],'from_unit_id'=>['nullable','exists:units,id'],'instruction'=>['required','string','max:5000'],'notes'=>['nullable','string','max:5000'],'priority'=>['required','in:LOW,NORMAL,HIGH,URGENT'],'deadline'=>['nullable','date']];} }
