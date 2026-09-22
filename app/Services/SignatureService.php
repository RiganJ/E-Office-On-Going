<?php
namespace App\Services;
use App\Models\GeneratedDocument;
/** Integration boundary for a future certified electronic-signature provider. */
class SignatureService { public function provider(): string { return config('eoffice.signature_provider', 'approval_qr_validation'); } public function sign(GeneratedDocument $generated): GeneratedDocument { $generated->update(['signature_provider'=>$this->provider(),'signature_status'=>'VALIDATED']); return $generated->fresh(); } }
