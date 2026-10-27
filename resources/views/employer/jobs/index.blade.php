@extends('layouts.ascend')
@section('title', 'Manage Gig Postings — Ascend Employer')

@section('content')
<div class="page-header">
  <div>
    <span class="eyebrow">Feature #2 · Job Posting CRUD</span>
    <h1>My Posted Gigs &amp; Vacancies</h1>
    <p>Manage, draft, update, or toggle active/closed status on all student gig opportunities.</p>
  </div>
  <a href="{{ route('employer.jobs.create') }}" class="btn btn-primary btn-sm">+ Post a New Gig</a>
</div>

<div class="glass-panel">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>Gig Title</th>
          <th>Work Mode</th>
          <th>Budget</th>
          <th>Deadline</th>
          <th>Status</th>
          <th>Candidates</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($jobs as $job)
          <tr>
            <td>
              <a href="{{ route('jobs.show', $job) }}" style="font-weight:700;font-size:15px;color:var(--ink)">{{ $job->title }}</a>
              <div style="font-size:12px;color:var(--muted)">Created {{ $job->created_at->format('M d, Y') }}</div>
            </td>
            <td><span class="badge badge-mint">{{ $job->job_type }}</span></td>
            <td style="font-weight:700;color:var(--accent)">${{ number_format($job->budget, 2) }}</td>
            <td style="font-size:13px;color:var(--muted)">{{ $job->deadline->format('M d, Y') }}</td>
            <td>
              <!-- 1-Click Toggle Active / Closed -->
              <form action="{{ route('employer.jobs.toggle-status', $job) }}" method="POST" style="display:inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="badge {{ $job->is_active ? 'badge-blue' : 'badge-danger' }}" style="cursor:pointer;border:none" title="Click to toggle status">
                  {{ $job->is_active ? '● Active (Open)' : '○ Closed (Archived)' }}
                </button>
              </form>
            </td>
            <td>
              <a href="{{ route('employer.applications.index', ['job_id' => $job->id]) }}" class="badge badge-gold" title="Review applicants">
                {{ $job->applications_count }} applicant{{ $job->applications_count === 1 ? '' : 's' }} →
              </a>
            </td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="{{ route('jobs.show', $job) }}" class="btn btn-ghost btn-sm">Inspect</a>
                <a href="{{ route('employer.jobs.edit', $job) }}" class="btn btn-outline btn-sm">Edit</a>
                <form action="{{ route('employer.jobs.destroy', $job) }}" method="POST" onsubmit="return confirm('Delete this gig posting permanently?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align:center;padding:36px;color:var(--muted)">
              You haven't posted any gigs yet. Click "+ Post a New Gig" to get started!
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:18px">
    {{ $jobs->links() }}
  </div>
</div>
@endsection
