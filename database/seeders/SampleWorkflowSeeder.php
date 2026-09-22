<?php
namespace Database\Seeders;
use App\Models\{DocumentType,Permission,Role,Unit,User,Workflow}; use Illuminate\Database\Seeder;
/** A functional development example. It never modifies an existing workflow. */
class SampleWorkflowSeeder extends Seeder {
 public function run():void {
  $approve=Permission::firstOrCreate(['name'=>'approve documents'],['label'=>'Memproses approval']);
  $kaprodi=Role::firstOrCreate(['name'=>'kaprodi'],['label'=>'Kaprodi']);$kaprodi->permissions()->syncWithoutDetaching([$approve->id]);
  $dekan=Role::firstOrCreate(['name'=>'dekan'],['label'=>'Dekan']);$dekan->permissions()->syncWithoutDetaching([$approve->id]);
  // The demo administrator receives both example roles so the flow can be tested immediately.
  if($admin=User::where('email','admin@eoffice.test')->first())$admin->roles()->syncWithoutDetaching([$kaprodi->id,$dekan->id]);
  $type=DocumentType::where('code','ASSIGNMENT')->firstOrFail();$unit=Unit::where('code','FIK')->firstOrFail();
  $workflow=Workflow::firstOrCreate(['code'=>'SURAT-TUGAS-DOSEN-LENGKAP'],['name'=>'Surat Tugas Dosen — Kaprodi ke Dekan','document_type_id'=>$type->id,'unit_id'=>$unit->id,'is_active'=>true]);
  if($workflow->wasRecentlyCreated)$workflow->steps()->createMany([
   ['step_order'=>1,'approver_type'=>'ROLE','approver_reference'=>$kaprodi->id,'action'=>'APPROVE','is_required'=>true,'allow_reject'=>true,'allow_revision'=>true],
   ['step_order'=>2,'approver_type'=>'ROLE','approver_reference'=>$dekan->id,'action'=>'APPROVE','is_required'=>true,'allow_reject'=>true,'allow_revision'=>true],
  ]);
 }
}
