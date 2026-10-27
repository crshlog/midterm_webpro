@extends('layouts.ascend')
@section('title', 'Join Ascend — Student Freelancer or Employer')

@section('content')
<div style="max-width:540px;margin:36px auto">
  <div class="glass-panel">
    <span class="eyebrow">Student &amp; Business Registration</span>
    <h1 style="font-size:26px;font-weight:800;margin-bottom:6px">Create Your Ascend Account</h1>
    <p style="color:var(--muted);font-size:13.5px;margin-bottom:20px">
      Join the micro-recruitment hub to freelance or hire talented student creators.
    </p>

    <form action="{{ route('register.submit') }}" method="POST" class="form-grid">
      @csrf

      <div class="form-group full">
        <label class="form-label">I am joining as a:</label>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <label style="border:1px solid var(--line-strong);padding:14px;border-radius:12px;background:rgba(255,255,255,.03);cursor:pointer;display:flex;align-items:center;gap:10px">
            <input type="radio" name="role" value="freelancer" checked onchange="toggleEmployerFields(false)">
            <div>
              <strong style="color:var(--accent);display:block">Student Freelancer</strong>
              <span style="font-size:12px;color:var(--muted)">Find gigs, pitch &amp; earn</span>
            </div>
          </label>
          <label style="border:1px solid var(--line-strong);padding:14px;border-radius:12px;background:rgba(255,255,255,.03);cursor:pointer;display:flex;align-items:center;gap:10px">
            <input type="radio" name="role" value="employer" {{ old('role') === 'employer' ? 'checked' : '' }} onchange="toggleEmployerFields(true)">
            <div>
              <strong style="color:#9fc4ff;display:block">Employer / Org</strong>
              <span style="font-size:12px;color:var(--muted)">Post gigs &amp; hire students</span>
            </div>
          </label>
        </div>
      </div>

      <div class="form-group full">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="input" value="{{ old('name') }}" placeholder="e.g. Alex Rivera or Rianti Dewi" required>
      </div>

      <div class="form-group full">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="input" value="{{ old('email') }}" placeholder="you@university.ac.id or business@company.com" required>
      </div>

      <div id="employerFields" class="form-group full" style="display:{{ old('role') === 'employer' ? 'grid' : 'none' }};grid-template-columns:1fr 1fr;gap:14px;border-top:1px solid var(--line);padding-top:14px">
        <div class="form-group">
          <label class="form-label">Company / Club Name</label>
          <input type="text" name="company_name" class="input" value="{{ old('company_name') }}" placeholder="e.g. Nexus Studio or Student Robotics">
        </div>
        <div class="form-group">
          <label class="form-label">Industry</label>
          <input type="text" name="industry" class="input" value="{{ old('industry') }}" placeholder="e.g. Web Development or Tutoring">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="input" placeholder="Min 6 characters" required>
      </div>

      <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="input" placeholder="Repeat password" required>
      </div>

      <div class="form-group full" style="margin-top:6px">
        <button type="submit" class="btn btn-primary" style="width:100%">
          Create Account &amp; Access Portal
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </button>
      </div>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:13.5px;color:var(--muted)">
      Already have an account? <a href="{{ route('login') }}" style="color:var(--accent);font-weight:600">Sign In</a>
    </p>
  </div>
</div>

<script>
function toggleEmployerFields(show) {
  document.getElementById('employerFields').style.display = show ? 'grid' : 'none';
}
</script>
@endsection
