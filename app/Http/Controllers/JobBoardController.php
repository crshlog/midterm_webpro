<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobBoardController extends Controller
{
    public function landing(): View
    {
        $featuredJobs = JobPosting::with('employer')
            ->where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        $metrics = [
            'total_jobs' => JobPosting::where('is_active', true)->count(),
            'total_employers' => Employer::count(),
            'total_applications' => JobApplication::count(),
            'total_budget' => JobPosting::where('is_active', true)->sum('budget'),
        ];

        return view('welcome', compact('featuredJobs', 'metrics'));
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'job_type', 'min_budget', 'max_budget', 'sort']);
        
        $jobs = JobPosting::with('employer')
            ->where('is_active', true)
            ->filter($filters)
            ->paginate(9)
            ->withQueryString();

        $jobTypes = ['Remote', 'On-site', 'Hybrid'];

        return view('jobs.index', compact('jobs', 'filters', 'jobTypes'));
    }

    public function show(JobPosting $job): View
    {
        $job->load('employer.user');
        
        $hasApplied = auth()->check()
            ? $job->applications()->where('applicant_id', auth()->id())->exists()
            : false;

        $userApplication = auth()->check()
            ? $job->applications()->where('applicant_id', auth()->id())->first()
            : null;

        $isBookmarked = auth()->check() ? $job->isBookmarkedBy(auth()->user()) : false;

        return view('jobs.show', compact('job', 'hasApplied', 'userApplication', 'isBookmarked'));
    }
}
