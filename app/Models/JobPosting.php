<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'title',
        'job_type',
        'budget',
        'deadline',
        'requirements',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'deadline' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class, 'job_id');
    }

    public function bookmarkedByUsers()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'job_id', 'user_id')->withTimestamps();
    }

    public function isBookmarkedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when($filters['search'] ?? null, function (Builder $q, string $search) {
            $q->where(function (Builder $sub) use ($search) {
                $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('requirements', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('employer', function (Builder $empQ) use ($search) {
                        $empQ->where('company_name', 'like', "%{$search}%")
                             ->orWhere('industry', 'like', "%{$search}%");
                    });
            });
        });

        $query->when($filters['job_type'] ?? null, function (Builder $q, string $type) {
            $q->where('job_type', $type);
        });

        $query->when($filters['min_budget'] ?? null, function (Builder $q, $min) {
            $q->where('budget', '>=', (float) $min);
        });

        $query->when($filters['max_budget'] ?? null, function (Builder $q, $max) {
            $q->where('budget', '<=', (float) $max);
        });

        return match ($filters['sort'] ?? 'latest') {
            'budget_desc' => $query->orderByDesc('budget'),
            'budget_asc' => $query->orderBy('budget'),
            'deadline_asc' => $query->orderBy('deadline', 'asc'),
            default => $query->latest(),
        };
    }
}
