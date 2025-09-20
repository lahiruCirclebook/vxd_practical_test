<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'label',
        'type',
        'options',
        'required',
        'order_index'
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
        'order_index' => 'integer'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function answers()
    {
        return $this->hasMany(SubmissionAnswer::class);
    }
}
