@extends('layouts.ascend')
@section('title', 'Saved Gigs Watchlist — Ascend')

@section('content')
<div class="page-header">
  <div>
    <span class="eyebrow">Feature #6 · Job Bookmarking / Save List</span>
    <h1>My Saved Gigs Watchlist</h1>
    <p>Personal bookmarks of student opportunities you want to review, prepare portfolio samples for, and pitch to later.</p>
  </div>
  <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-sm">Explore More Gigs</a>
</div>

<div class="market-grid">
  @forelse($bookmarkedJobs as $job)
    <div class="market-card">
      <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <span class="badge badge-mint">{{ $job->job_type }} Mode</span>
          <span style="font-size:12px;color:var(--muted)">Closes {{ $job->deadline->format('M d, Y') }}</span>
        </div>

        <h3 style="font-size:18px;font-weight:700;margin-bottom:6px">
          <a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a>
        </h3>

        <div style="font-size:12.5px;color:var(--muted-2);margin-bottom:12px">
          By <strong>{{ $job->employer->company_name ?? 'Local Studio' }}</strong> · {{ $job->employer->industry ?? 'Technology' }}
        </div>

        <p style="font-size:13.5px;color:var(--muted);line-height:1.55;margin-bottom:18px">
          {{ Str::limit($job->description ?: $job->requirements, 120) }}
        </p>
      </div>

      <div style="border-top:1px solid var(--line);padding-top:14px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <div>
            <span style="font-size:11px;color:var(--muted);display:block">Stipend</span>
            <span class="price-tag" style="font-size:22px">${{ number_format($job->budget, 0) }}</span>
          </div>
          <span class="badge {{ $job->is_active ? 'badge-blue' : 'badge-danger' }}">{{ $job->is_active ? 'Open' : 'Closed' }}</span>
        </div>

        <div style="display:flex;gap:8px;align-items:center">
          <a href="{{ route('jobs.show', $job) }}" class="btn btn-primary btn-sm" style="flex:1">View &amp; Pitch</a>

          <!-- Remove from Watchlist -->
          <form action="{{ route('jobs.bookmark', $job) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm" title="Remove from watchlist">Remove</button>
          </form>
        </div>
      </div>
    </div>
  @empty
    <div class="glass-panel" style="grid-column:1 / -1;text-align:center;padding:48px">
      <h3>Your saved watchlist is currently empty.</h3>
      <p style="color:var(--muted);margin-top:6px">When browsing the job board, click "☆ Save" on any gig to keep it on your personal list.</p>
      <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-sm" style="margin-top:14px">Explore Job Board</a>
    </div>
  @endforelse
</div>

<div style="margin-top:24px">
  {{ $bookmarkedJobs->links() }}
</div>
@endsection
