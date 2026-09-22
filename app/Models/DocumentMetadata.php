<?php namespace App\Models; use Illuminate\Database\Eloquent\Model; class DocumentMetadata extends Model { protected $fillable=['document_id','data']; protected $casts=['data'=>'array']; }
