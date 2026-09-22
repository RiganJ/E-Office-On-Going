<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class WorkflowApproval extends Model { protected $fillable=['workflow_instance_id','workflow_step_id','cycle','approver_user_id','approver_position_id','status','notes','acted_at']; protected $casts=['acted_at'=>'datetime']; public function instance(){return $this->belongsTo(WorkflowInstance::class,'workflow_instance_id');} public function step(){return $this->belongsTo(WorkflowStep::class,'workflow_step_id');} public function approver(){return $this->belongsTo(User::class,'approver_user_id');} }
