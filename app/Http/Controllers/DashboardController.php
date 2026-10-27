<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if ($user->isEmployer()) {
            $employer = $user->employer;
            $myJobs = $employer ? $employer->jobPostings()->get() : collect();
            $myJobIds = $myJobs->pluck('id');

            $stats = [
                'active_jobs' => $myJobs->where('is_active', true)->count(),
                'total_jobs' => $myJobs->count(),
                'total_applications' => JobApplication::whereIn('job_id', $myJobIds)->count(),
                'pending_reviews' => JobApplication::whereIn('job_id', $myJobIds)->where('status', 'Pending')->count(),
                'hired_candidates' => JobApplication::whereIn('job_id', $myJobIds)->where('status', 'Hired')->count(),
            ];

            $recentApplications = JobApplication::with(['job', 'applicant'])
                ->whereIn('job_id', $myJobIds)
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.employer', compact('stats', 'myJobs', 'recentApplications', 'employer'));
        }

        // Freelancer Dashboard
        $myApplications = $user->applications()->with(['job.employer'])->latest()->get();
        $bookmarkedCount = $user->bookmarks()->count();

        $stats = [
            'total_applied' => $myApplications->count(),
            'pending' => $myApplications->where('status', 'Pending')->count(),
            'interviewed' => $myApplications->where('status', 'Interviewed')->count(),
            'hired' => $myApplications->where('status', 'Hired')->count(),
            'bookmarks' => $bookmarkedCount,
        ];

        $recentApplied = $myApplications->take(5);

        return view('dashboard.freelancer', compact('stats', 'recentApplied'));
    }
}
