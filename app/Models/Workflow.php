<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class Workflow extends Model { protected $fillable=['name','code','document_type_id','unit_id','is_active']; public function steps(){return $this->hasMany(WorkflowStep::class)->orderBy('step_order');} public function documentType(){return $this->belongsTo(DocumentType::class);} public function unit(){return $this->belongsTo(Unit::class);} }
