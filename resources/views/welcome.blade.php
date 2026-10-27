<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ascend — Freelance Job Board &amp; Student Application Portal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/ascend.css') }}">
  <script type="importmap">
  { "imports": {
    "three": "https://unpkg.com/three@0.143.0/build/three.module.js",
    "three/examples/jsm/": "https://unpkg.com/three@0.143.0/examples/jsm/"
  }}
  </script>
</head>
<body>
  <canvas class="planet-canvas"></canvas>

  <div class="page" id="top">
    <!-- SECTION 1 · HERO -->
    <header class="hero">
      <nav class="nav">
        <a href="#top" class="brand"><span class="brand-mark">▲</span> Ascend <span style="font-size:12px;color:var(--accent);letter-spacing:.08em;font-weight:600;margin-left:4px">GIGS</span></a>
        <div class="nav-links">
          <a href="#features">Features</a>
          <a href="{{ route('jobs.index') }}">Job Board</a>
          <a href="#gigs">Featured Gigs</a>
          <a href="#cta">Employers</a>
        </div>
        <div class="nav-actions">
          @auth
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Dashboard</a>
          @else
            <a href="{{ route('login') }}" class="btn btn-ghost">Sign In</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Join Hub</a>
          @endauth
        </div>
      </nav>

      <div class="hero-inner">
        <span class="eyebrow" data-reveal style="--rd:30ms">Micro-Recruitment for Student Talent</span>
        <h1 class="hero-title" data-reveal style="--rd:60ms">Work Smarter,<br>Earn <em>Faster</em></h1>
        <p class="hero-sub" data-reveal style="--rd:180ms">A micro-recruitment hub connecting local businesses and student organizations with student freelancers — designers, coders, writers, and tutors.</p>
        <div class="hero-actions" data-reveal style="--rd:300ms">
          <a href="{{ route('jobs.index') }}" class="btn btn-primary">
            Explore Job Board
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
          <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="btn btn-outline">Post a Gig / Join</a>
        </div>
      </div>

      <div class="logos" data-reveal style="--rd:420ms">
        <div class="logos-label">Trusted by campus student clubs &amp; local startups</div>
        <div class="logos-row">
          <span class="logo">Nexus Studio</span>
          <span class="logo">NovaTech AI</span>
          <span class="logo">Campus Tutors</span>
          <span class="logo">ByteCraft</span>
          <span class="logo">Apex Media</span>
          <span class="logo">Vertx Labs</span>
        </div>
      </div>
    </header>

    <!-- SECTION 2 · FEATURES -->
    <section class="section features" id="features">
      <div class="section-head">
        <span class="eyebrow" data-reveal style="--rd:0ms">Built for student careers</span>
        <h2 data-reveal style="--rd:80ms">Everything you need to hire or freelance on campus</h2>
        <p data-reveal style="--rd:160ms">From instant vacancy search with transparent budgets to PDF CV uploads and real-time application trackers — Ascend streamlines student recruitment.</p>
      </div>

      <div class="feature-grid">
        <div class="card" data-reveal style="--rd:0ms">
          <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="6" height="6" rx="1.5"/><rect x="15" y="15" width="6" height="6" rx="1.5"/><path d="M6 9v3a3 3 0 0 0 3 3h3"/></svg></div>
          <h3>Job Board Search &amp; Filters</h3>
          <p>Find gigs quickly by keyword, work mode (Remote, On-site, Hybrid), and custom budget range with instant sorting.</p>
        </div>
        <div class="card" data-reveal style="--rd:90ms">
          <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 16v-4"/><path d="M13 16V8"/><path d="M18 16v-7"/></svg></div>
          <h3>Job Posting CRUD</h3>
          <p>Employers draft, edit, publish, or close listings anytime with full deadline and budget management.</p>
        </div>
        <div class="card" data-reveal style="--rd:180ms">
          <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
          <h3>Application Submission</h3>
          <p>Apply in seconds with custom pitch text, portfolio link, and secure PDF CV resume upload handling.</p>
        </div>
        <div class="card" data-reveal style="--rd:270ms">
          <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"/></svg></div>
          <h3>Applicant Review Hub</h3>
          <p>Employers review incoming submissions and update hiring status: Pending, Interviewed, Hired, or Rejected.</p>
        </div>
        <div class="card" data-reveal style="--rd:360ms">
          <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div>
          <h3>Status Tracker &amp; Withdrawal</h3>
          <p>Freelancers track every application status live and can withdraw pending applications before employers review.</p>
        </div>
        <div class="card" data-reveal style="--rd:450ms">
          <div class="card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20Z"/></svg></div>
          <h3>Watchlist &amp; Company Bio</h3>
          <p>Save gigs to your personal watchlist with 1 click, while employers customize logos, bios, and social links.</p>
        </div>
      </div>
    </section>

    <!-- SECTION 3 · LIVE FEATURED GIGS -->
    <section class="section" id="gigs" style="padding-top:0">
      <div class="section-head">
        <span class="eyebrow" data-reveal style="--rd:0ms">Active Opportunities</span>
        <h2 data-reveal style="--rd:80ms">Featured Student Freelance Gigs</h2>
        <p data-reveal style="--rd:160ms">Explore trending gigs from local companies and student organizations offering transparent stipends.</p>
      </div>

      <div class="market-grid">
        @foreach($featuredJobs as $job)
          <div class="market-card">
            <div>
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                <span class="badge badge-mint">{{ $job->job_type }}</span>
                <span style="font-size:12px;color:var(--muted)">Deadline: {{ $job->deadline->format('M d') }}</span>
              </div>
              <h3 style="font-size:18px;font-weight:700;margin-bottom:8px">
                <a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a>
              </h3>
              <p style="font-size:13.5px;color:var(--muted-2);margin-bottom:12px">
                By {{ $job->employer->company_name ?? 'Local Studio' }} · {{ $job->employer->industry ?? 'Technology' }}
              </p>
              <p style="font-size:14px;color:var(--muted);line-height:1.5;margin-bottom:18px">
                {{ Str::limit($job->description ?: $job->requirements, 120) }}
              </p>
            </div>
            <div style="border-top:1px solid var(--line);padding-top:14px;display:flex;justify-content:space-between;align-items:center">
              <div>
                <span style="font-size:11px;color:var(--muted);display:block">Budget</span>
                <span class="price-tag" style="font-size:22px">${{ number_format($job->budget, 0) }}</span>
              </div>
              <a href="{{ route('jobs.show', $job) }}" class="btn btn-primary btn-sm">Inspect &amp; Apply</a>
            </div>
          </div>
        @endforeach
      </div>

      <div style="text-align:center;margin-top:36px">
        <a href="{{ route('jobs.index') }}" class="btn btn-outline">View All Gigs on Job Board →</a>
      </div>

      <!-- Live Metric Stats -->
      <div class="stats">
        <div class="stat" data-reveal style="--rd:0ms">
          <div class="stat-value">{{ $metrics['total_jobs'] }}</div>
          <div class="stat-label">Active Gigs Available</div>
        </div>
        <div class="stat" data-reveal style="--rd:80ms">
          <div class="stat-value">{{ $metrics['total_employers'] }}</div>
          <div class="stat-label">Verified Employers &amp; Clubs</div>
        </div>
        <div class="stat" data-reveal style="--rd:160ms">
          <div class="stat-value">{{ $metrics['total_applications'] }}</div>
          <div class="stat-label">Candidate Pitches Sent</div>
        </div>
        <div class="stat" data-reveal style="--rd:240ms">
          <div class="stat-value">${{ number_format($metrics['total_budget'], 0) }}</div>
          <div class="stat-label">Total Student Budget Pool</div>
        </div>
      </div>
    </section>

    <!-- SECTION 4 · CTA + FOOTER -->
    <section class="section cta" id="cta">
      <div class="cta-card">
        <span class="eyebrow" data-reveal style="--rd:60ms">Ready to hire or get hired?</span>
        <h2 data-reveal style="--rd:140ms">Start building your freelance portfolio today</h2>
        <p data-reveal style="--rd:220ms">Join hundreds of student coders, designers, writers, and tutors gaining real-world gig experience and verified portfolio credit.</p>
        <div class="hero-actions" data-reveal style="--rd:300ms">
          <a href="{{ route('register') }}" class="btn btn-primary">
            Create Free Account
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
          <a href="{{ route('jobs.index') }}" class="btn btn-outline">Browse All Vacancies</a>
        </div>
      </div>

      <footer class="footer">
        <div class="footer-brand">
          <a href="#top" class="brand"><span class="brand-mark">▲</span> Ascend GIGS</a>
          <p>Micro-recruitment portal connecting student freelancers with campus organizations &amp; businesses.</p>
        </div>
        <div class="footer-cols">
          <div class="footer-col">
            <h4>For Students</h4>
            <ul>
              <li><a href="{{ route('jobs.index') }}">Job Board Search</a></li>
              <li><a href="{{ route('login') }}">Application Status</a></li>
              <li><a href="{{ route('login') }}">Watchlist</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>For Employers</h4>
            <ul>
              <li><a href="{{ route('login') }}">Post a Gig</a></li>
              <li><a href="{{ route('login') }}">Review Applicants</a></li>
              <li><a href="{{ route('login') }}">Company Bio</a></li>
            </ul>
          </div>
          <div class="footer-col">
            <h4>Academics</h4>
            <ul>
              <li><a href="{{ route('home') }}">EF234301 WebPro (D)</a></li>
              <li><a href="{{ route('home') }}">Midterm Project</a></li>
            </ul>
          </div>
        </div>
      </footer>
      <div class="copyright">© <span id="year"></span> Ascend Freelance Hub. Built for the EF234301 Midterm Examination.</div>
    </section>
  </div>

  <script type="module" src="{{ asset('js/ascend-planet.js') }}"></script>
</body>
</html>
