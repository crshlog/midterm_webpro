<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobPostingRequest;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobPostingController extends Controller
{
    private function getEmployer()
    {
        $user = auth()->user();
        if (!$user->employer) {
            $user->employer()->create([
                'company_name' => $user->name . ' Studio',
                'industry' => 'Technology & Services',
            ]);
            $user->refresh();
        }
        return $user->employer;
    }

    public function index(): View
    {
        $employer = $this->getEmployer();
        $jobs = $employer->jobPostings()->withCount('applications')->latest()->paginate(10);

        return view('employer.jobs.index', compact('jobs'));
    }

    public function create(): View
    {
        $job = new JobPosting([
            'job_type' => 'Remote',
            'budget' => 350.00,
            'deadline' => now()->addDays(14),
            'is_active' => true,
        ]);

        return view('employer.jobs.form', compact('job'));
    }

    public function store(StoreJobPostingRequest $request): RedirectResponse
    {
        $employer = $this->getEmployer();
        $data = $request->validated();
        $data['employer_id'] = $employer->id;
        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;

        JobPosting::create($data);

        return redirect()->route('employer.jobs.index')->with('success', 'Gig posting published to the student board successfully.');
    }

    public function edit(JobPosting $job): View
    {
        $this->authorizeOwner($job);
        return view('employer.jobs.form', compact('job'));
    }

    public function update(StoreJobPostingRequest $request, JobPosting $job): RedirectResponse
    {
        $this->authorizeOwner($job);
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : false;

        $job->update($data);

        return redirect()->route('employer.jobs.index')->with('success', 'Gig posting updated successfully.');
    }

    public function destroy(JobPosting $job): RedirectResponse
    {
        $this->authorizeOwner($job);
        $job->delete();

        return redirect()->route('employer.jobs.index')->with('success', 'Gig posting deleted.');
    }

    public function toggleStatus(JobPosting $job): RedirectResponse
    {
        $this->authorizeOwner($job);
        $job->update(['is_active' => !$job->is_active]);

        $statusText = $job->is_active ? 'opened for applications' : 'closed / archived';
        return back()->with('success', "Gig listing is now {$statusText}.");
    }

    private function authorizeOwner(JobPosting $job): void
    {
        $employer = $this->getEmployer();
        if ($job->employer_id !== $employer->id) {
            abort(403, 'Unauthorized action on this gig listing.');
        }
    }
}
