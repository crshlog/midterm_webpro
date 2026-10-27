<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    // Feature 3: Application Submission
    public function store(StoreJobApplicationRequest $request, JobPosting $job): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isEmployer() && $job->employer->user_id === $user->id) {
            return back()->withErrors(['application' => 'You cannot apply to your own gig posting.']);
        }

        $existing = JobApplication::where('job_id', $job->id)
            ->where('applicant_id', $user->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['application' => 'You have already submitted an application for this gig.']);
        }

        $cvPath = null;
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('cvs', 'public');
        }

        JobApplication::create([
            'job_id' => $job->id,
            'applicant_id' => $user->id,
            'cover_letter' => $request->input('cover_letter'),
            'portfolio_url' => $request->input('portfolio_url'),
            'cv_file' => $cvPath,
            'status' => 'Pending',
        ]);

        return redirect()->route('my-applications.index')
            ->with('success', 'Your pitch and application have been submitted to the employer!');
    }

    // Feature 5: Applicant Status Tracker (Freelancer view)
    public function myApplications(): View
    {
        $applications = JobApplication::with(['job.employer'])
            ->where('applicant_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('freelancer.applications.index', compact('applications'));
    }

    // Feature 7: Application Withdrawal
    public function withdraw(JobApplication $application): RedirectResponse
    {
        if ($application->applicant_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($application->status !== 'Pending') {
            return back()->withErrors(['withdraw' => 'Cannot withdraw application once an employer has reviewed or updated its status.']);
        }

        if ($application->cv_file && Storage::disk('public')->exists($application->cv_file)) {
            Storage::disk('public')->delete($application->cv_file);
        }

        $application->delete();

        return back()->with('success', 'Application withdrawn successfully.');
    }

    // Feature 4: Employer Applicant Review Dashboard
    public function employerReview(Request $request): View
    {
        $employer = auth()->user()->employer;
        if (!$employer) {
            abort(403, 'Employer profile not found.');
        }

        $employerJobs = $employer->jobPostings()->get();
        $selectedJobId = $request->query('job_id');
        $selectedStatus = $request->query('status');

        $query = JobApplication::with(['job', 'applicant'])
            ->whereIn('job_id', $employerJobs->pluck('id'));

        if ($selectedJobId) {
            $query->where('job_id', $selectedJobId);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        $applications = $query->latest()->paginate(10)->withQueryString();

        $statuses = ['Pending', 'Interviewed', 'Hired', 'Rejected'];

        return view('employer.applications.index', compact('applications', 'employerJobs', 'selectedJobId', 'selectedStatus', 'statuses'));
    }

    // Feature 4: Update candidate status
    public function updateStatus(Request $request, JobApplication $application): RedirectResponse
    {
        $employer = auth()->user()->employer;
        if (!$employer || $application->job->employer_id !== $employer->id) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'status' => ['required', 'in:Pending,Interviewed,Hired,Rejected'],
        ]);

        $application->update(['status' => $request->input('status')]);

        return back()->with('success', "Candidate status updated to '{$application->status}'.");
    }
}
