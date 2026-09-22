<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;
class Document extends Model { use SoftDeletes;
 protected $fillable=['uuid','verification_token','document_type_id','unit_id','creator_id','subject','content','status','document_date','deadline','number','archived_at','file_hash'];
 protected $casts=['document_date'=>'date','deadline'=>'datetime','archived_at'=>'datetime'];
 public function getRouteKeyName():string{return 'uuid';}
 public function getDeadlineBadgeAttribute():string{if(in_array($this->status,['COMPLETED','ARCHIVED'],true))return 'COMPLETED';if(!$this->deadline)return 'NORMAL';return $this->deadline->isPast()?'OVERDUE':($this->deadline->lte(now()->addDays(3))?'DUE_SOON':'NORMAL');}
 public function type(){return $this->belongsTo(DocumentType::class,'document_type_id');} public function creator(){return $this->belongsTo(User::class,'creator_id');} public function unit(){return $this->belongsTo(Unit::class);} public function dispositions(){return $this->hasMany(Disposition::class);} public function workflowInstance(){return $this->hasOne(WorkflowInstance::class);} public function activityLogs(){return $this->morphMany(ActivityLog::class,'subject')->latest();} public function metadata(){return $this->hasOne(DocumentMetadata::class);} public function generatedDocument(){return $this->hasOne(GeneratedDocument::class);} public function files(){return $this->hasMany(DocumentFile::class);} public function incomingLetter(){return $this->hasOne(IncomingLetter::class);}
}
