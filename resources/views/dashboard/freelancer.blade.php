@extends('layouts.ascend')
@section('title', 'Freelancer Dashboard — Ascend')

@section('content')
<div class="page-header">
  <div>
    <span class="eyebrow">Student Freelancer Command Center</span>
    <h1>Welcome back, {{ auth()->user()->name }}</h1>
    <p>Track your ongoing gig pitches, saved opportunities, and verified employer interview statuses.</p>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-sm">Explore Job Board</a>
    <a href="{{ route('my-applications.index') }}" class="btn btn-outline btn-sm">Application Status Tracker</a>
    <a href="{{ route('bookmarks.index') }}" class="btn btn-ghost btn-sm">Saved Watchlist ({{ $stats['bookmarks'] }})</a>
  </div>
</div>

<div class="stats" style="margin-top:0;margin-bottom:28px">
  <div class="stat">
    <div class="stat-value">{{ $stats['total_applied'] }}</div>
    <div class="stat-label">Total Gigs Applied</div>
  </div>
  <div class="stat">
    <div class="stat-value" style="color:#ffd27a">{{ $stats['pending'] }}</div>
    <div class="stat-label">Pending Review</div>
  </div>
  <div class="stat">
    <div class="stat-value" style="color:#9fc4ff">{{ $stats['interviewed'] }}</div>
    <div class="stat-label">Invited to Interview</div>
  </div>
  <div class="stat">
    <div class="stat-value" style="color:var(--accent)">{{ $stats['hired'] }}</div>
    <div class="stat-label">Gigs Hired / Won 🎉</div>
  </div>
</div>

<div class="glass-panel">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h3 style="font-size:18px;font-weight:700">My Recent Gig Applications</h3>
    <a href="{{ route('my-applications.index') }}" style="font-size:13px;color:var(--accent)">Open Full Status Tracker →</a>
  </div>

  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>Gig / Vacancy</th>
          <th>Employer / Org</th>
          <th>Budget</th>
          <th>Applied Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentApplied as $app)
          <tr>
            <td>
              <a href="{{ route('jobs.show', $app->job) }}" style="font-weight:700;color:var(--ink)">{{ $app->job->title }}</a>
              <div style="font-size:12px;color:var(--muted)">Mode: {{ $app->job->job_type }}</div>
            </td>
            <td>
              <div style="font-weight:600">{{ $app->job->employer->company_name ?? 'Employer' }}</div>
              <div style="font-size:12px;color:var(--muted-2)">{{ $app->job->employer->industry ?? 'Studio' }}</div>
            </td>
            <td style="font-weight:700;color:var(--accent)">${{ number_format($app->job->budget, 0) }}</td>
            <td style="font-size:13px;color:var(--muted)">{{ $app->created_at->format('M d, Y') }}</td>
            <td>
              <span class="badge {{ $app->statusBadgeClass() }}">{{ $app->status }}</span>
            </td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="{{ route('jobs.show', $app->job) }}" class="btn btn-ghost btn-sm">View Gig</a>
                @if($app->status === 'Pending')
                  <form action="{{ route('my-applications.withdraw', $app) }}" method="POST" onsubmit="return confirm('Withdraw this pending application?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" title="Withdraw application">Withdraw</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align:center;padding:36px;color:var(--muted)">
              You haven't applied to any gigs yet. <a href="{{ route('jobs.index') }}" style="color:var(--accent);font-weight:600">Browse the Job Board</a> to find your first project!
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
