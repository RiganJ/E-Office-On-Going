<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingLetter extends Model
{
    protected $fillable = ['document_id', 'agenda_number', 'sender', 'sender_institution', 'letter_number', 'letter_date', 'received_date', 'nature', 'classification'];

    protected $casts = ['letter_date' => 'date', 'received_date' => 'date'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
