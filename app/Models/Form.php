<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'certificate_image',
        'orientation',
        'is_active',
        'certificate_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'certificate_settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($form) {
            if (empty($form->slug)) {
                $form->slug = Str::slug($form->title) . '-' . Str::random(6);
            }
        });
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function getShareUrlAttribute()
    {
        return url('/form/' . $this->slug);
    }
}
