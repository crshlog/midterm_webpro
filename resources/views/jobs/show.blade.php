@extends('layouts.ascend')
@section('title', $job->title . ' — Ascend Job Board')

@section('content')
<div class="page-header">
  <div>
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
      <span class="badge badge-mint">{{ $job->job_type }} Mode</span>
      <span class="badge {{ $job->is_active ? 'badge-blue' : 'badge-danger' }}">{{ $job->is_active ? 'Open for Pitches' : 'Gig Closed' }}</span>
      <span style="font-size:13px;color:var(--muted)">Closes {{ $job->deadline->format('F d, Y') }}</span>
    </div>
    <h1 style="font-size:clamp(26px,3.5vw,36px)">{{ $job->title }}</h1>
    <p>Posted by <strong>{{ $job->employer->company_name ?? 'Local Studio' }}</strong> ({{ $job->employer->industry ?? 'Technology' }})</p>
  </div>

  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <!-- Feature 6: Bookmark Toggle -->
    @auth
      <form action="{{ route('jobs.bookmark', $job) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm">
          {{ $isBookmarked ? '★ In Watchlist' : '☆ Save to Watchlist' }}
        </button>
      </form>
      @if(auth()->user()->isEmployer() && auth()->user()->employer && auth()->user()->employer->id === $job->employer_id)
        <a href="{{ route('employer.jobs.edit', $job) }}" class="btn btn-ghost btn-sm">Edit Listing</a>
        <a href="{{ route('employer.applications.index', ['job_id' => $job->id]) }}" class="btn btn-primary btn-sm">Review Pitches ({{ $job->applications()->count() }})</a>
      @endif
    @else
      <a href="{{ route('login') }}" class="btn btn-outline btn-sm">☆ Save to Watchlist</a>
    @endauth
    <a href="{{ route('jobs.index') }}" class="btn btn-ghost btn-sm">← Back to Board</a>
  </div>
</div>

<div class="showcase-grid" style="align-items:start">
  <!-- Left Column: Gig Details & Application Form -->
  <div>
    <!-- Gig Overview -->
    <div class="glass-panel" style="margin-bottom:24px">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px">
        <div>
          <span style="font-size:12px;color:var(--muted);display:block">Project Budget</span>
          <span class="price-tag" style="font-size:32px">${{ number_format($job->budget, 2) }}</span>
        </div>
        <span class="badge" style="font-size:13px;padding:6px 14px">{{ $job->applications()->count() }} candidate pitches</span>
      </div>

      <h3 style="font-size:18px;font-weight:700;margin-bottom:10px">Description &amp; Project Scope</h3>
      <p style="font-size:15px;line-height:1.7;color:var(--ink);margin-bottom:24px;white-space:pre-line">{{ $job->description ?: 'No detailed description provided.' }}</p>

      <h3 style="font-size:18px;font-weight:700;margin-bottom:10px">Requirements &amp; Deliverables</h3>
      <div style="background:rgba(255,255,255,.02);border:1px solid var(--line);border-radius:12px;padding:16px;font-size:14px;line-height:1.7;color:var(--muted);white-space:pre-line">{{ $job->requirements }}</div>
    </div>

    <!-- Feature 3 & 5 & 7: Application Section -->
    <div class="glass-panel">
      @if($hasApplied)
        <div style="border-left:4px solid var(--accent);padding-left:16px;margin-bottom:16px">
          <span class="eyebrow" style="margin-bottom:4px">Feature #5 · Applicant Status Tracker</span>
          <h3 style="font-size:20px;font-weight:700;margin-bottom:6px">You have pitched for this gig!</h3>
          <p style="font-size:14px;color:var(--muted)">
            Current Hiring Status: <span class="badge {{ $userApplication->statusBadgeClass() }}">{{ $userApplication->status }}</span>
          </p>
        </div>

        <div style="background:rgba(255,255,255,.02);border:1px solid var(--line);border-radius:12px;padding:16px;margin-bottom:18px;font-size:14px">
          <div style="font-weight:600;margin-bottom:6px;color:var(--ink)">Your Submitted Pitch:</div>
          <p style="color:var(--muted);line-height:1.6;margin-bottom:12px">"{{ $userApplication->cover_letter }}"</p>

          <div style="display:flex;gap:12px;flex-wrap:wrap;font-size:13px">
            @if($userApplication->portfolio_url)
              <span>Portfolio: <a href="{{ $userApplication->portfolio_url }}" target="_blank" style="color:var(--accent)">{{ $userApplication->portfolio_url }} ↗</a></span>
            @endif
            @if($userApplication->cv_file)
              <span>CV: <a href="{{ asset('storage/' . $userApplication->cv_file) }}" target="_blank" style="color:var(--accent)">Download PDF 📄</a></span>
            @endif
          </div>
        </div>

        <!-- Feature 7: Application Withdrawal -->
        @if($userApplication->status === 'Pending')
          <form action="{{ route('my-applications.withdraw', $userApplication) }}" method="POST" onsubmit="return confirm('Are you sure you want to withdraw your pitch before the employer reviews it?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Withdraw My Pitch</button>
            <span style="font-size:12px;color:var(--muted);margin-left:8px">(Available while status is Pending)</span>
          </form>
        @else
          <div style="font-size:13px;color:var(--muted)">Your application has been reviewed by the employer. Status updates will appear here.</div>
        @endif

      @elseif(!$job->is_active)
        <div class="alert alert-error" style="margin-bottom:0">This gig is currently closed by the employer and is not accepting new candidate pitches.</div>

      @else
        <!-- Feature 3: Application Submission Form -->
        <span class="eyebrow" style="margin-bottom:6px">Feature #3 · Pitch Submission</span>
        <h3 style="font-size:20px;font-weight:700;margin-bottom:6px">Pitch for this Gig</h3>
        <p style="font-size:13.5px;color:var(--muted);margin-bottom:20px">
          Introduce yourself, link your portfolio, and upload your PDF CV for the hiring team.
        </p>

        @auth
          <form action="{{ route('jobs.apply', $job) }}" method="POST" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:16px">
            @csrf

            <div class="form-group">
              <label class="form-label">Cover Letter / Pitch Text *</label>
              <textarea name="cover_letter" rows="4" class="textarea" placeholder="Explain your relevant skills, past student projects, and why you are a great fit..." required>{{ old('cover_letter') }}</textarea>
            </div>

            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Portfolio / GitHub / Dribbble URL (Optional)</label>
                <input type="url" name="portfolio_url" class="input" placeholder="https://github.com/your-username" value="{{ old('portfolio_url') }}">
              </div>

              <div class="form-group">
                <label class="form-label">Upload CV / Resume (PDF Only, Max 5MB)</label>
                <input type="file" name="cv_file" class="input" accept=".pdf" style="padding:8px">
              </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top:4px">
              Submit Pitch &amp; Application
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </button>
          </form>
        @else
          <div style="background:rgba(255,255,255,.02);border:1px solid var(--line);border-radius:12px;padding:24px;text-align:center">
            <h4 style="font-size:16px;margin-bottom:8px">Sign in to pitch for this gig</h4>
            <p style="font-size:13px;color:var(--muted);margin-bottom:16px">Join as a student freelancer to submit your portfolio and contact this employer.</p>
            <div style="display:flex;gap:10px;justify-content:center">
              <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Sign In</a>
              <a href="{{ route('register') }}" class="btn btn-outline btn-sm">Create Student Account</a>
            </div>
          </div>
        @endauth
      @endif
    </div>
  </div>

  <!-- Right Column: Employer Profile Card (Feature 8 Data) -->
  <div class="glass-panel">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
      @if($job->employer->logo_image)
        <img src="{{ asset('storage/' . $job->employer->logo_image) }}" alt="Logo" style="width:52px;height:52px;border-radius:12px;object-fit:cover;border:1px solid var(--line)">
      @else
        <div style="width:52px;height:52px;border-radius:12px;background:rgba(93,240,168,.1);border:1px solid rgba(93,240,168,.25);display:flex;align-items:center;justify-content:center;color:var(--accent);font-weight:800;font-size:20px">
          {{ substr($job->employer->company_name ?? 'E', 0, 1) }}
        </div>
      @endif

      <div>
        <h3 style="font-size:17px;font-weight:700;margin-bottom:2px">{{ $job->employer->company_name ?? 'Local Organization' }}</h3>
        <span class="badge">{{ $job->employer->industry ?? 'Technology' }}</span>
      </div>
    </div>

    <p style="font-size:13.5px;color:var(--muted);line-height:1.6;margin-bottom:18px">
      {{ $job->employer->bio ?? 'No company bio provided yet.' }}
    </p>

    <div style="border-top:1px solid var(--line);padding-top:14px;font-size:13px;display:flex;flex-direction:column;gap:8px">
      @if($job->employer->website)
        <div>Website: <a href="{{ $job->employer->website }}" target="_blank" style="color:var(--accent)">{{ $job->employer->website }} ↗</a></div>
      @endif
      @if($job->employer->social_media_links)
        <div>Socials: <span style="color:var(--muted)">{{ $job->employer->social_media_links }}</span></div>
      @endif
      <div>Contact Person: <strong style="color:var(--ink)">{{ $job->employer->user->name ?? 'Representative' }}</strong></div>
    </div>

    <div style="margin-top:20px;padding-top:14px;border-top:1px solid var(--line);font-size:12.5px;color:var(--muted)">
      💡 <em>Safety Tip: All communications and deliverables stay logged within the Ascend recruitment portal.</em>
    </div>
  </div>
</div>
@endsection
