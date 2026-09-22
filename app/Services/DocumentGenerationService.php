<?php
namespace App\Services;
use App\Models\{Document,DocumentTemplate,GeneratedDocument}; use Dompdf\Dompdf; use Endroid\QrCode\QrCode; use Endroid\QrCode\Writer\SvgWriter; use Illuminate\Support\Facades\Storage;
class DocumentGenerationService {
 public function generate(Document $document): GeneratedDocument {
  if($existing=$document->generatedDocument)return $existing;
  $template=DocumentTemplate::where('document_type_id',$document->document_type_id)->where('is_active',true)->where(fn($q)=>$q->whereNull('unit_id')->orWhere('unit_id',$document->unit_id))->orderByRaw('unit_id is null')->first();
  $data=array_merge(['nomor_surat'=>$document->number,'tanggal'=>optional($document->document_date)->format('d-m-Y'),'unit'=>$document->unit?->name,'jenis_dokumen'=>$document->type?->name,'perihal'=>$document->subject,'content'=>$document->content],$document->metadata?->data??[]);
  $body=$template?->content??'<h2>{{ perihal }}</h2><p>{{ content }}</p>';$rendered=preg_replace_callback('/{{\s*([\w_]+)\s*}}/',fn($m)=>e((string)($data[$m[1]]??'')),$body);
  $verificationUrl=url('/verifikasi/'.$document->verification_token);$qr=(new SvgWriter())->write(new QrCode($verificationUrl,size:150,margin:8))->getDataUri();
  $html='<html><body>'.($template?->header??'').$rendered.($template?->footer??'').'<hr><table><tr><td><img src="'.$qr.'" width="105" height="105"></td><td>Scan QR untuk verifikasi dokumen.<br>'.e($verificationUrl).'</td></tr></table></body></html>';
  $pdf=new Dompdf();$pdf->loadHtml($html);$pdf->setPaper('A4');$pdf->render();$path='eoffice/generated/'.$document->uuid.'.pdf';$contents=$pdf->output();Storage::disk('local')->put($path,$contents);$document->update(['file_hash'=>hash('sha256',$contents)]);
  return GeneratedDocument::create(['document_id'=>$document->id,'template_id'=>$template?->id,'html_snapshot'=>$html,'data_snapshot'=>$data,'pdf_path'=>$path]);
 }
}
