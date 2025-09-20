<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function fields()
    {
        return $this->hasMany(FormField::class)->orderBy('order_index');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function getSubmissionCountAttribute()
    {
        return $this->submissions()->count();
    }
}
