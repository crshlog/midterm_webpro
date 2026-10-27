@extends('layouts.ascend')
@section('title', ($job->exists ? 'Edit Gig: ' . $job->title : 'Post a New Student Gig') . ' — Ascend')

@section('content')
<div style="max-width:760px;margin:0 auto">
  <div class="page-header">
    <div>
      <span class="eyebrow">Feature #2 · Job Posting CRUD Form</span>
      <h1>{{ $job->exists ? 'Edit Gig Posting' : 'Post a New Student Gig' }}</h1>
      <p>Draft, publish, and manage hiring requirements for student freelancers.</p>
    </div>
    <a href="{{ route('employer.jobs.index') }}" class="btn btn-ghost btn-sm">← Back to My Gigs</a>
  </div>

  <div class="glass-panel">
    <form action="{{ $job->exists ? route('employer.jobs.update', $job) : route('employer.jobs.store') }}" method="POST" class="form-grid">
      @csrf
      @if($job->exists)
        @method('PUT')
      @endif

      <div class="form-group full">
        <label class="form-label">Gig Title *</label>
        <input type="text" name="title" class="input" value="{{ old('title', $job->title) }}" placeholder="e.g. Landing Page Redesign (Figma to Tailwind) or Peer Tutor" required>
      </div>

      <div class="form-group">
        <label class="form-label">Work Mode *</label>
        <select name="job_type" class="select" required>
          <option value="Remote" @selected(old('job_type', $job->job_type) === 'Remote')>Remote (Anywhere)</option>
          <option value="On-site" @selected(old('job_type', $job->job_type) === 'On-site')>On-site (On Campus / Local Office)</option>
          <option value="Hybrid" @selected(old('job_type', $job->job_type) === 'Hybrid')>Hybrid (Flexible)</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Budget / Stipend ($ USD) *</label>
        <input type="number" step="0.01" min="5" name="budget" class="input" value="{{ old('budget', $job->budget) }}" placeholder="e.g. 450.00" required>
      </div>

      <div class="form-group">
        <label class="form-label">Application Deadline *</label>
        <input type="date" name="deadline" class="input" value="{{ old('deadline', optional($job->deadline)->format('Y-m-d') ?? now()->addDays(14)->format('Y-m-d')) }}" required>
      </div>

      <div class="form-group">
        <label class="form-label">Listing Visibility</label>
        <div style="padding:10px 0">
          <label style="display:flex;align-items:center;gap:8px;font-size:14px;color:var(--ink);cursor:pointer">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $job->is_active ?? true))>
            <span>Publish active immediately on Student Job Board</span>
          </label>
        </div>
      </div>

      <div class="form-group full">
        <label class="form-label">Key Requirements &amp; Deliverables *</label>
        <textarea name="requirements" rows="4" class="textarea" placeholder="List bullet points of skills required (e.g. Tailwind, Figma, Python, Grade A in Math)..." required>{{ old('requirements', $job->requirements) }}</textarea>
      </div>

      <div class="form-group full">
        <label class="form-label">Detailed Project Scope / Description</label>
        <textarea name="description" rows="5" class="textarea" placeholder="Provide background about your company, project timeline, milestones, and expected deliverables...">{{ old('description', $job->description) }}</textarea>
      </div>

      <div class="form-group full" style="display:flex;flex-direction:row;gap:12px;margin-top:10px">
        <button type="submit" class="btn btn-primary">{{ $job->exists ? 'Update Gig Posting' : 'Publish Gig to Student Board' }}</button>
        <a href="{{ route('employer.jobs.index') }}" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
