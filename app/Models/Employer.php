<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Employer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'industry',
        'website',
        'bio',
        'logo_image',
        'social_media_links',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }

    public function applications(): HasManyThrough
    {
        return $this->hasManyThrough(
            JobApplication::class,
            JobPosting::class,
            'employer_id',
            'job_id',
            'id',
            'id'
        );
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_image ? asset('storage/' . $this->logo_image) : null;
    }
}

