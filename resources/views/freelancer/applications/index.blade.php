@extends('layouts.ascend')
@section('title', 'My Application Status Tracker — Ascend')

@section('content')
<div class="page-header">
  <div>
    <span class="eyebrow">Features #5 &amp; #7 · Applicant Status Tracker &amp; Withdrawal</span>
    <h1>My Gig Applications &amp; Status Tracker</h1>
    <p>Track live hiring decisions from employers, check interview invites, and manage your active submissions.</p>
  </div>
  <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-sm">+ Pitch for More Gigs</a>
</div>

<div class="glass-panel">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>Gig Vacancy</th>
          <th>Employer / Organization</th>
          <th>Budget</th>
          <th>My Pitch &amp; Documents</th>
          <th>Applied Date</th>
          <th>Hiring Status (Feature #5)</th>
          <th>Actions (Feature #7)</th>
        </tr>
      </thead>
      <tbody>
        @forelse($applications as $app)
          <tr>
            <td>
              <a href="{{ route('jobs.show', $app->job) }}" style="font-weight:700;font-size:15px;color:var(--ink)">{{ $app->job->title }}</a>
              <div style="font-size:12px;color:var(--muted)">Mode: {{ $app->job->job_type }}</div>
            </td>
            <td>
              <div style="font-weight:600">{{ $app->job->employer->company_name ?? 'Local Studio' }}</div>
              <div style="font-size:12px;color:var(--muted-2)">{{ $app->job->employer->industry ?? 'Technology' }}</div>
            </td>
            <td style="font-weight:700;color:var(--accent)">${{ number_format($app->job->budget, 0) }}</td>
            <td style="max-width:280px">
              <div style="font-size:12.5px;color:var(--muted);line-height:1.4;margin-bottom:6px">
                "{{ Str::limit($app->cover_letter, 90) }}"
              </div>
              <div style="display:flex;gap:6px;flex-wrap:wrap">
                @if($app->portfolio_url)
                  <a href="{{ $app->portfolio_url }}" target="_blank" class="btn btn-ghost btn-sm" style="font-size:11px;padding:2px 7px">Portfolio ↗</a>
                @endif
                @if($app->cv_file)
                  <a href="{{ asset('storage/' . $app->cv_file) }}" target="_blank" class="btn btn-ghost btn-sm" style="font-size:11px;padding:2px 7px">PDF CV 📄</a>
                @endif
              </div>
            </td>
            <td style="font-size:12.5px;color:var(--muted)">
              {{ $app->created_at->format('M d, Y') }}
            </td>
            <td>
              <span class="badge {{ $app->statusBadgeClass() }}" style="font-size:12.5px;padding:5px 12px">
                {{ $app->status }}
              </span>
            </td>
            <td>
              <div style="display:flex;gap:6px;align-items:center">
                <a href="{{ route('jobs.show', $app->job) }}" class="btn btn-ghost btn-sm">Inspect</a>

                <!-- Feature 7: Application Withdrawal -->
                @if($app->status === 'Pending')
                  <form action="{{ route('my-applications.withdraw', $app) }}" method="POST" onsubmit="return confirm('Withdraw your application? You can re-apply later if the gig is still open.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" title="Withdraw pending submission">Withdraw</button>
                  </form>
                @else
                  <span style="font-size:11.5px;color:var(--muted-2)">Under Review</span>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align:center;padding:36px;color:var(--muted)">
              You haven't pitched to any gigs yet. <a href="{{ route('jobs.index') }}" style="color:var(--accent);font-weight:600">Browse the Job Board</a> to find student opportunities!
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:18px">
    {{ $applications->links() }}
  </div>
</div>
@endsection
