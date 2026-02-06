<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'full_name',
        'email',
        'mobile',
        'score',
        'total_questions',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function answers()
    {
        return $this->hasMany(SubmissionAnswer::class);
    }

    public function getScorePercentageAttribute()
    {
        if ($this->total_questions == 0) return 0;
        return round(($this->score / $this->total_questions) * 100);
    }
}
