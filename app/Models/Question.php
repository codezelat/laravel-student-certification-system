<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'question_text',
        'order',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class)->orderBy('order');
    }

    public function correctAnswers()
    {
        return $this->hasMany(Answer::class)->where('is_correct', true);
    }
}
