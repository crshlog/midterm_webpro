@extends('layouts.ascend')
@section('title', 'Student Freelance Job Board — Ascend')

@section('content')
<div class="page-header">
  <div>
    <span class="eyebrow">Feature #1 · Vacancy Discovery</span>
    <h1>Student Freelance Job Board</h1>
    <p>Discover freelance projects and tutoring roles posted by local businesses and campus student organizations.</p>
  </div>
  @auth
    @if(auth()->user()->isEmployer())
      <a href="{{ route('employer.jobs.create') }}" class="btn btn-primary btn-sm">+ Post a New Gig</a>
    @else
      <a href="{{ route('my-applications.index') }}" class="btn btn-outline btn-sm">My Applied Gigs</a>
    @endif
  @else
    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Join to Pitch &amp; Apply</a>
  @endauth
</div>

<!-- Feature 1: Search & Filter Form -->
<form action="{{ route('jobs.index') }}" method="GET" class="glass-panel filter-bar">
  <input type="text" name="search" class="input" placeholder="Search keywords (e.g. Figma, Python, Tutor, React)..." value="{{ $filters['search'] ?? '' }}">

  <select name="job_type" class="select">
    <option value="">All Job Types</option>
    @foreach($jobTypes as $type)
      <option value="{{ $type }}" @selected(($filters['job_type'] ?? '') === $type)>{{ $type }} Mode</option>
    @endforeach
  </select>

  <div style="display:flex;gap:6px">
    <input type="number" name="min_budget" class="input" placeholder="Min $" value="{{ $filters['min_budget'] ?? '' }}" style="width:50%">
    <input type="number" name="max_budget" class="input" placeholder="Max $" value="{{ $filters['max_budget'] ?? '' }}" style="width:50%">
  </div>

  <select name="sort" class="select">
    <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Newest Gigs</option>
    <option value="budget_desc" @selected(($filters['sort'] ?? '') === 'budget_desc')>Budget: High to Low</option>
    <option value="budget_asc" @selected(($filters['sort'] ?? '') === 'budget_asc')>Budget: Low to High</option>
    <option value="deadline_asc" @selected(($filters['sort'] ?? '') === 'deadline_asc')>Deadline: Closing Soon</option>
  </select>

  <div style="display:flex;gap:8px">
    <button type="submit" class="btn btn-primary btn-sm">Apply Filter</button>
    <a href="{{ route('jobs.index') }}" class="btn btn-ghost btn-sm">Reset</a>
  </div>
</form>

<!-- Job Listings Grid -->
<div class="market-grid">
  @forelse($jobs as $job)
    <div class="market-card">
      <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;gap:8px">
          <span class="badge badge-mint">{{ $job->job_type }}</span>
          <span style="font-size:12px;color:var(--muted)">Closes {{ $job->deadline->format('M d, Y') }}</span>
        </div>

        <h3 style="font-size:18px;font-weight:700;margin-bottom:6px">
          <a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a>
        </h3>

        <div style="font-size:12.5px;color:var(--muted-2);margin-bottom:12px">
          By <strong>{{ $job->employer->company_name ?? 'Local Studio' }}</strong> · {{ $job->employer->industry ?? 'Creative' }}
        </div>

        <p style="font-size:13.5px;color:var(--muted);line-height:1.55;margin-bottom:18px">
          {{ Str::limit($job->description ?: $job->requirements, 120) }}
        </p>
      </div>

      <div style="border-top:1px solid var(--line);padding-top:14px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <div>
            <span style="font-size:11px;color:var(--muted);display:block">Fixed Stipend</span>
            <span class="price-tag" style="font-size:24px">${{ number_format($job->budget, 0) }}</span>
          </div>
          <span class="badge" style="font-size:11.5px">{{ $job->applications()->count() }} pitches</span>
        </div>

        <div style="display:flex;gap:8px;align-items:center">
          <a href="{{ route('jobs.show', $job) }}" class="btn btn-primary btn-sm" style="flex:1">View &amp; Pitch</a>

          <!-- Feature 6: 1-Click Bookmark Toggle -->
          @auth
            <form action="{{ route('jobs.bookmark', $job) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-outline btn-sm" title="{{ $job->isBookmarkedBy(auth()->user()) ? 'Remove from saved' : 'Save to watchlist' }}">
                {{ $job->isBookmarkedBy(auth()->user()) ? '★ Saved' : '☆ Save' }}
              </button>
            </form>
          @else
            <a href="{{ route('login') }}" class="btn btn-outline btn-sm" title="Save to watchlist">☆ Save</a>
          @endauth
        </div>
      </div>
    </div>
  @empty
    <div class="glass-panel" style="grid-column:1 / -1;text-align:center;padding:48px">
      <h3>No gigs found matching your search and filter criteria.</h3>
      <p style="color:var(--muted);margin-top:6px">Try adjusting your keyword, expanding the budget range, or clearing filters.</p>
      <a href="{{ route('jobs.index') }}" class="btn btn-ghost btn-sm" style="margin-top:14px">Reset Filters</a>
    </div>
  @endforelse
</div>

<div style="margin-top:24px">
  {{ $jobs->links() }}
</div>
@endsection
