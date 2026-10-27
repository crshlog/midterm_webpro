@extends('layouts.ascend')
@section('title', 'Company Profile & Branding — Ascend Employer')

@section('content')
<div style="max-width:760px;margin:0 auto">
  <div class="page-header">
    <div>
      <span class="eyebrow">Feature #8 · Employer Company Profile CRUD</span>
      <h1>Company Profile &amp; Organization Bio</h1>
      <p>Customize how your studio, business, or student organization appears to candidates on the job board.</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">← Back to Dashboard</a>
  </div>

  <div class="glass-panel">
    <form action="{{ route('employer.profile.update') }}" method="POST" enctype="multipart/form-data" class="form-grid">
      @csrf
      @method('PUT')

      <div class="form-group full" style="display:flex;align-items:center;gap:18px;margin-bottom:8px">
        @if($employer->logo_image)
          <img src="{{ asset('storage/' . $employer->logo_image) }}" alt="Current Logo" style="width:72px;height:72px;border-radius:14px;object-fit:cover;border:1px solid var(--line-strong)">
        @else
          <div style="width:72px;height:72px;border-radius:14px;background:rgba(93,240,168,.12);border:1px solid rgba(93,240,168,.3);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:26px;font-weight:800">
            {{ substr($employer->company_name ?? 'E', 0, 1) }}
          </div>
        @endif

        <div style="flex:1">
          <label class="form-label">Company Logo Image (PNG, JPG, SVG, WebP)</label>
          <input type="file" name="logo_image" class="input" accept="image/*" style="padding:8px">
          <span style="font-size:11.5px;color:var(--muted)">Square ratio recommended. Uploading a new image replaces the current logo.</span>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Company / Organization Name *</label>
        <input type="text" name="company_name" class="input" value="{{ old('company_name', $employer->company_name) }}" required>
      </div>

      <div class="form-group">
        <label class="form-label">Industry / Sector *</label>
        <input type="text" name="industry" class="input" value="{{ old('industry', $employer->industry) }}" placeholder="e.g. UI/UX Design, AI &amp; Web, Campus Club" required>
      </div>

      <div class="form-group full">
        <label class="form-label">Official Website URL (Optional)</label>
        <input type="url" name="website" class="input" value="{{ old('website', $employer->website) }}" placeholder="https://yourcompany.io">
      </div>

      <div class="form-group full">
        <label class="form-label">Social Media Links (e.g. LinkedIn, Twitter, GitHub, Instagram)</label>
        <input type="text" name="social_media_links" class="input" value="{{ old('social_media_links', $employer->social_media_links) }}" placeholder="https://linkedin.com/company/nexus, https://x.com/nexus">
      </div>

      <div class="form-group full">
        <label class="form-label">Company Bio &amp; About Us *</label>
        <textarea name="bio" rows="5" class="textarea" placeholder="Tell student freelancers about your mission, projects, team culture, and hiring expectations...">{{ old('bio', $employer->bio) }}</textarea>
      </div>

      <div class="form-group full" style="display:flex;flex-direction:row;gap:12px;margin-top:10px">
        <button type="submit" class="btn btn-primary">Save Company Profile</button>
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
