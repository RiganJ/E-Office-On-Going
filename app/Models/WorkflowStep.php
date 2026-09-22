<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class WorkflowStep extends Model { protected $fillable=['workflow_id','step_order','approver_type','approver_reference','action','is_required','allow_reject','allow_revision']; }
