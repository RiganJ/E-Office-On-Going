<?php
namespace App\Http\Controllers;
use App\Models\Document;
class VerificationController extends Controller { public function show(string $token){$document=Document::with(['type','creator','generatedDocument'])->where('verification_token',$token)->whereIn('status',['COMPLETED','ARCHIVED'])->firstOrFail();return view('verification.show',compact('document'));} }
