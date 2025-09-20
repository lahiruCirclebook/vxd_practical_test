<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubmissionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'form_field_id',
        'answer_value'
    ];

    protected $casts = [
        'answer_value' => 'array'
    ];

    public function submission()
    {
        return $this->belongsTo(FormSubmission::class);
    }

    public function field()
    {
        return $this->belongsTo(FormField::class, 'form_field_id');
    }
}
