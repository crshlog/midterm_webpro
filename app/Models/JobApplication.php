<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'applicant_id',
        'cover_letter',
        'portfolio_url',
        'cv_file',
        'status',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function cvUrl(): ?string
    {
        return $this->cv_file ? asset('storage/' . $this->cv_file) : null;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'Pending' => 'badge-gold',
            'Interviewed' => 'badge-blue',
            'Hired' => 'badge-mint',
            'Rejected' => 'badge-danger',
            default => 'badge',
        };
    }
}
