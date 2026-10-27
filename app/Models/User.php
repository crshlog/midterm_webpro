<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isFreelancer(): bool
    {
        return $this->role === 'freelancer';
    }

    public function employer(): HasOne
    {
        return $this->hasOne(Employer::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'applicant_id');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkedJobs()
    {
        return $this->belongsToMany(JobPosting::class, 'bookmarks', 'user_id', 'job_id')->withTimestamps();
    }
}
