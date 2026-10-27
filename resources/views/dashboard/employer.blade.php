@extends('layouts.ascend')
@section('title', 'Employer Hiring Dashboard — Ascend')

@section('content')
<div class="page-header">
  <div>
    <span class="eyebrow">Employer Recruitment Command Center</span>
    <h1>Welcome, {{ $employer->company_name ?? auth()->user()->name }}</h1>
    <p>Manage your student gig postings, review candidate pitches, and coordinate interviews.</p>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <a href="{{ route('employer.jobs.create') }}" class="btn btn-primary btn-sm">+ Post a New Gig</a>
    <a href="{{ route('employer.applications.index') }}" class="btn btn-outline btn-sm">Review Candidates ({{ $stats['pending_reviews'] }} pending)</a>
    <a href="{{ route('employer.profile.edit') }}" class="btn btn-ghost btn-sm">Company Profile</a>
  </div>
</div>

<div class="stats" style="margin-top:0;margin-bottom:28px">
  <div class="stat">
    <div class="stat-value">{{ $stats['active_jobs'] }}</div>
    <div class="stat-label">Active Published Gigs</div>
  </div>
  <div class="stat">
    <div class="stat-value">{{ $stats['total_applications'] }}</div>
    <div class="stat-label">Total Candidate Pitches</div>
  </div>
  <div class="stat">
    <div class="stat-value" style="color:#ffd27a">{{ $stats['pending_reviews'] }}</div>
    <div class="stat-label">Pending Review</div>
  </div>
  <div class="stat">
    <div class="stat-value" style="color:var(--accent)">{{ $stats['hired_candidates'] }}</div>
    <div class="stat-label">Hired Student Freelancers</div>
  </div>
</div>

<div class="showcase-grid" style="align-items:start;margin-bottom:32px">
  <!-- My Gig Postings -->
  <div class="glass-panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <h3 style="font-size:18px;font-weight:700">My Gig Postings ({{ $myJobs->count() }})</h3>
      <a href="{{ route('employer.jobs.index') }}" style="font-size:13px;color:var(--accent)">Manage All Postings →</a>
    </div>

    <div style="display:flex;flex-direction:column;gap:12px">
      @forelse($myJobs as $job)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-radius:12px;background:rgba(255,255,255,.02);border:1px solid var(--line);gap:12px">
          <div>
            <div style="font-weight:700;font-size:14.5px">
              <a href="{{ route('jobs.show', $job) }}" style="color:var(--ink)">{{ $job->title }}</a>
            </div>
            <div style="font-size:12px;color:var(--muted);margin-top:2px">
              {{ $job->job_type }} · ${{ number_format($job->budget, 0) }} · Deadline: {{ $job->deadline->format('M d, Y') }}
            </div>
          </div>
          <div style="text-align:right;display:flex;align-items:center;gap:10px">
            <span class="badge {{ $job->is_active ? 'badge-mint' : '' }}">{{ $job->is_active ? 'Active' : 'Closed' }}</span>
            <a href="{{ route('employer.applications.index', ['job_id' => $job->id]) }}" class="badge badge-blue" style="cursor:pointer" title="View candidates">
              {{ $job->applications_count ?? $job->applications()->count() }} applicants
            </a>
          </div>
        </div>
      @empty
        <p style="color:var(--muted);font-size:14px;padding:16px 0">No gig postings yet. Click "+ Post a New Gig" above to recruit student talent!</p>
      @endforelse
    </div>
  </div>

  <!-- Recent Candidates Received -->
  <div class="glass-panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <h3 style="font-size:18px;font-weight:700">Recent Candidate Submissions</h3>
      <a href="{{ route('employer.applications.index') }}" style="font-size:13px;color:var(--accent)">Full Candidate Hub →</a>
    </div>

    <div style="display:flex;flex-direction:column;gap:12px">
      @forelse($recentApplications as $app)
        <div style="padding:12px 14px;border-radius:12px;background:rgba(255,255,255,.02);border:1px solid var(--line)">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
            <span style="font-weight:700;font-size:14px;color:var(--ink)">{{ $app->applicant->name ?? 'Candidate' }}</span>
            <span class="badge {{ $app->statusBadgeClass() }}">{{ $app->status }}</span>
          </div>
          <div style="font-size:12px;color:var(--muted);margin-bottom:8px">
            Applied for <em>{{ $app->job->title ?? 'Gig' }}</em> · {{ $app->created_at->diffForHumans() }}
          </div>
          <p style="font-size:13px;color:var(--muted);line-height:1.4;margin-bottom:8px">
            "{{ Str::limit($app->cover_letter, 90) }}"
          </p>
          <div style="display:flex;gap:8px">
            @if($app->portfolio_url)
              <a href="{{ $app->portfolio_url }}" target="_blank" class="btn btn-ghost btn-sm" style="font-size:11px;padding:3px 8px">Portfolio ↗</a>
            @endif
            @if($app->cv_file)
              <a href="{{ asset('storage/' . $app->cv_file) }}" target="_blank" class="btn btn-ghost btn-sm" style="font-size:11px;padding:3px 8px">PDF CV 📄</a>
            @endif
            <a href="{{ route('employer.applications.index', ['job_id' => $app->job_id]) }}" class="btn btn-outline btn-sm" style="font-size:11px;padding:3px 8px">Review Status</a>
          </div>
        </div>
      @empty
        <p style="color:var(--muted);font-size:14px;padding:16px 0">No candidate submissions yet.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
