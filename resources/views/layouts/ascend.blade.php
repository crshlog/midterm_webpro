<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Ascend — Student Freelance Job Board & Recruitment Portal')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    <nav class="nav" style="border-bottom:1px solid var(--line);background:rgba(9,11,16,.85);backdrop-filter:blur(16px);position:sticky;top:0;z-index:50;max-width:100%;padding:16px max(24px, calc((100vw - var(--maxw)) / 2))">
      <a href="{{ route('home') }}" class="brand">
        <span class="brand-mark">▲</span> Ascend <span style="font-size:12px;color:var(--accent);letter-spacing:.08em;font-weight:600;margin-left:4px">GIGS</span>
      </a>

      <div class="nav-links">
        <a href="{{ route('jobs.index') }}" class="{{ request()->routeIs('jobs.*') ? 'active' : '' }}">Job Board</a>
        @auth
          <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
          @if(auth()->user()->isEmployer())
            <a href="{{ route('employer.jobs.index') }}" class="{{ request()->routeIs('employer.jobs.*') ? 'active' : '' }}">My Postings</a>
            <a href="{{ route('employer.applications.index') }}" class="{{ request()->routeIs('employer.applications.*') ? 'active' : '' }}">Candidates</a>
            <a href="{{ route('employer.profile.edit') }}" class="{{ request()->routeIs('employer.profile.*') ? 'active' : '' }}">Company Profile</a>
          @else
            <a href="{{ route('my-applications.index') }}" class="{{ request()->routeIs('my-applications.*') ? 'active' : '' }}">Status Tracker</a>
            <a href="{{ route('bookmarks.index') }}" class="{{ request()->routeIs('bookmarks.*') ? 'active' : '' }}">Watchlist</a>
          @endif
        @else
          <a href="{{ route('home') }}#features">Features</a>
          <a href="{{ route('home') }}#roles">For Students &amp; Businesses</a>
        @endauth
        <a href="{{ route('debug.accounts.index') }}" class="{{ request()->routeIs('debug.accounts.*') ? 'active' : '' }}" style="color:#f59e0b;font-weight:600" title="Debugger & QA Accounts Inspector">🛠 QA Accounts</a>
      </div>

      <div class="nav-actions">
        @auth
          <span class="badge {{ auth()->user()->isEmployer() ? 'badge-blue' : 'badge-mint' }}">
            {{ ucfirst(auth()->user()->role) }}: {{ Str::limit(auth()->user()->name, 16) }}
          </span>
          <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-ghost btn-sm">Log Out</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Sign In</a>
          <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
        @endauth
      </div>
    </nav>

    <main class="app-container">
      @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-error">
          @foreach($errors->all() as $err)
            <div>• {{ $err }}</div>
          @endforeach
        </div>
      @endif

      @yield('content')
    </main>

    <footer class="copyright" style="padding:28px 24px;margin-top:auto;border-top:1px solid var(--line);background:rgba(9,11,16,.65);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
      <div>© <span id="year"></span> Ascend — Freelance Job Board &amp; Candidate Application Portal (EF234301 Web Programming Midterm).</div>
      <div style="font-size:12px">
        <a href="{{ route('debug.accounts.index') }}" style="color:#f59e0b;display:inline-flex;align-items:center;gap:4px">🛠 QA Account Tracker &amp; Switcher</a>
      </div>
    </footer>
  </div>

  <script type="module" src="{{ asset('js/ascend-planet.js') }}"></script>
</body>
</html>
