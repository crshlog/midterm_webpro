@extends('layouts.ascend')
@section('title', 'Sign In — Ascend Freelance Hub')

@section('content')
<div style="max-width:480px;margin:40px auto">
  <div class="glass-panel">
    <span class="eyebrow">Portal Access</span>
    <h1 style="font-size:26px;font-weight:800;margin-bottom:6px">Sign In to Ascend</h1>
    <p style="color:var(--muted);font-size:13.5px;margin-bottom:20px">
      Access your candidate status tracker or employer hiring dashboard.
    </p>

    <!-- Quick Demo Logins Box -->
    <div style="background:rgba(255,255,255,.03);border:1px solid var(--line);border-radius:12px;padding:12px 14px;margin-bottom:20px;font-size:12.5px">
      <div style="font-weight:700;color:var(--accent);margin-bottom:6px">⚡ Quick Demo Test Credentials (password: password)</div>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button type="button" class="btn btn-ghost btn-sm" onclick="setDemo('employer.nexus@ascend.io')" style="font-size:12px;padding:4px 10px">
          Employer Demo (Nexus)
        </button>
        <button type="button" class="btn btn-ghost btn-sm" onclick="setDemo('freelancer@ascend.io')" style="font-size:12px;padding:4px 10px">
          Freelancer Demo (Alex Rivera)
        </button>
      </div>
      <div style="margin-top:10px;padding-top:8px;border-top:1px solid rgba(255,255,255,.05);display:flex;justify-content:space-between;align-items:center">
        <span style="color:var(--muted);font-size:11.5px">Debugger &amp; QC tool:</span>
        <a href="{{ route('debug.accounts.index') }}" style="color:#f59e0b;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:4px">
          🛠 QA Accounts Tracker &amp; Switcher →
        </a>
      </div>
    </div>

    <form action="{{ route('login.submit') }}" method="POST" style="display:flex;flex-direction:column;gap:16px">
      @csrf
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" id="emailInput" name="email" class="input" value="{{ old('email', 'freelancer@ascend.io') }}" required autofocus>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" id="passwordInput" name="password" class="input" value="password" required>
      </div>

      <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">
        <input type="checkbox" name="remember" checked> Keep session active
      </label>

      <button type="submit" class="btn btn-primary" style="width:100%;margin-top:6px">
        Sign In to Portal
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </button>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:13.5px;color:var(--muted)">
      New student or business? <a href="{{ route('register') }}" style="color:var(--accent);font-weight:600">Register Here</a>
    </p>
  </div>
</div>

<script>
function setDemo(email) {
  document.getElementById('emailInput').value = email;
  document.getElementById('passwordInput').value = 'password';
}
</script>
@endsection
