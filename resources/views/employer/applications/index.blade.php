@extends('layouts.ascend')
@section('title', 'Candidate Review Dashboard — Ascend Employer')

@section('content')
<div class="page-header">
  <div>
    <span class="eyebrow">Feature #4 · Employer Applicant Review Dashboard</span>
    <h1>Candidate Review Dashboard</h1>
    <p>Review incoming student pitches, inspect portfolio links, download PDF CVs, and update candidate hiring stages.</p>
  </div>
  <a href="{{ route('employer.jobs.index') }}" class="btn btn-outline btn-sm">← Back to My Gigs</a>
</div>

<!-- Filter candidate submissions -->
<form action="{{ route('employer.applications.index') }}" method="GET" class="glass-panel filter-bar">
  <select name="job_id" class="select">
    <option value="">All My Gigs ({{ $employerJobs->count() }})</option>
    @foreach($employerJobs as $ej)
      <option value="{{ $ej->id }}" @selected($selectedJobId == $ej->id)>{{ $ej->title }}</option>
    @endforeach
  </select>

  <select name="status" class="select">
    <option value="">All Review Stages</option>
    @foreach($statuses as $st)
      <option value="{{ $st }}" @selected($selectedStatus === $st)>{{ $st }} Candidates</option>
    @endforeach
  </select>

  <div style="display:flex;gap:8px">
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="{{ route('employer.applications.index') }}" class="btn btn-ghost btn-sm">Clear</a>
  </div>
</form>

<div class="glass-panel">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>Applicant</th>
          <th>Gig Applied For</th>
          <th>Candidate Pitch &amp; Documents</th>
          <th>Applied Date</th>
          <th>Current Status</th>
          <th>Update Hiring Status (Feature #4)</th>
        </tr>
      </thead>
      <tbody>
        @forelse($applications as $app)
          <tr>
            <td>
              <strong style="color:var(--ink);font-size:15px">{{ $app->applicant->name ?? 'Student' }}</strong>
              <div style="font-size:12px;color:var(--muted)">{{ $app->applicant->email ?? '' }}</div>
              @if($app->applicant->phone)
                <div style="font-size:11.5px;color:var(--muted-2)">{{ $app->applicant->phone }}</div>
              @endif
            </td>
            <td>
              <a href="{{ route('jobs.show', $app->job) }}" style="font-weight:600;color:var(--accent)">{{ $app->job->title }}</a>
              <div style="font-size:12px;color:var(--muted)">Budget: ${{ number_format($app->job->budget, 0) }}</div>
            </td>
            <td style="max-width:320px">
              <div style="background:rgba(255,255,255,.02);border:1px solid var(--line);border-radius:8px;padding:10px;font-size:13px;line-height:1.45;color:var(--muted);margin-bottom:8px">
                "{{ Str::limit($app->cover_letter, 160) }}"
              </div>
              <div style="display:flex;gap:8px;flex-wrap:wrap;font-size:12px">
                @if($app->portfolio_url)
                  <a href="{{ $app->portfolio_url }}" target="_blank" class="btn btn-ghost btn-sm" style="font-size:11px;padding:3px 8px">
                    Portfolio Link ↗
                  </a>
                @endif
                @if($app->cv_file)
                  <a href="{{ asset('storage/' . $app->cv_file) }}" target="_blank" class="btn btn-primary btn-sm" style="font-size:11px;padding:3px 8px">
                    Download PDF CV 📄
                  </a>
                @else
                  <span style="color:var(--muted-2);font-size:11px;padding:3px 0">No CV PDF attached</span>
                @endif
              </div>
            </td>
            <td style="font-size:12.5px;color:var(--muted);white-space:nowrap">
              {{ $app->created_at->format('M d, Y') }}
              <div style="font-size:11px;color:var(--muted-2)">{{ $app->created_at->diffForHumans() }}</div>
            </td>
            <td>
              <span class="badge {{ $app->statusBadgeClass() }}">{{ $app->status }}</span>
            </td>
            <td>
              <!-- Feature 4: Interactive Status Update Form -->
              <form action="{{ route('employer.applications.update-status', $app) }}" method="POST" style="display:flex;gap:6px;align-items:center">
                @csrf
                @method('PATCH')
                <select name="status" class="select" style="padding:6px 10px;font-size:12.5px;width:auto">
                  @foreach($statuses as $st)
                    <option value="{{ $st }}" @selected($app->status === $st)>{{ $st }}</option>
                  @endforeach
                </select>
                <button type="submit" class="btn btn-outline btn-sm" style="font-size:12px;padding:6px 10px">Save</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align:center;padding:36px;color:var(--muted)">
              No candidate submissions found matching your filters.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:18px">
    {{ $applications->links() }}
  </div>
</div>
@endsection
