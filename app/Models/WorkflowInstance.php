<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class WorkflowInstance extends Model { protected $fillable=['workflow_id','document_id','current_step','status','started_by','started_at','completed_at']; protected $casts=['started_at'=>'datetime','completed_at'=>'datetime']; public function workflow(){return $this->belongsTo(Workflow::class);} public function document(){return $this->belongsTo(Document::class);} public function approvals(){return $this->hasMany(WorkflowApproval::class);} }
