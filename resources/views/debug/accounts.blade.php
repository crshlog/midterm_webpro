@extends('layouts.ascend')
@section('title', 'Registered Accounts Inspector — Debugger & QC')

@section('content')
<div class="page-header">
  <div>
    <span class="eyebrow" style="color:#f59e0b">🛠 Quality Control &amp; System Debugger</span>
    <h1>Registered Accounts Inspector</h1>
    <p>Track all registered accounts in real-time, inspect roles &amp; activity metrics, and switch authentication sessions with 1-click.</p>
  </div>
  <div style="display:flex;gap:10px;align-items:center">
    <a href="{{ route('register') }}" target="_blank" class="btn btn-outline btn-sm">+ Register New Test Account</a>
  </div>
</div>

<!-- Telemetry Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:14px;margin-bottom:24px">
  <div class="glass-panel" style="padding:16px">
    <div style="font-size:12px;color:var(--muted);text-transform:uppercase;font-weight:700;letter-spacing:.05em">Total Registered</div>
    <div style="font-size:28px;font-weight:800;color:var(--ink);margin-top:4px">{{ $stats['total_users'] }}</div>
    <div style="font-size:11.5px;color:var(--muted);margin-top:2px">All database users</div>
  </div>

  <div class="glass-panel" style="padding:16px">
    <div style="font-size:12px;color:var(--muted);text-transform:uppercase;font-weight:700;letter-spacing:.05em">Employers</div>
    <div style="font-size:28px;font-weight:800;color:#60a5fa;margin-top:4px">{{ $stats['total_employers'] }}</div>
    <div style="font-size:11.5px;color:var(--muted);margin-top:2px">Hiring companies / orgs</div>
  </div>

  <div class="glass-panel" style="padding:16px">
    <div style="font-size:12px;color:var(--muted);text-transform:uppercase;font-weight:700;letter-spacing:.05em">Freelancers</div>
    <div style="font-size:28px;font-weight:800;color:#34d399;margin-top:4px">{{ $stats['total_freelancers'] }}</div>
    <div style="font-size:11.5px;color:var(--muted);margin-top:2px">Student applicants</div>
  </div>

  <div class="glass-panel" style="padding:16px">
    <div style="font-size:12px;color:var(--muted);text-transform:uppercase;font-weight:700;letter-spacing:.05em">Published Gigs</div>
    <div style="font-size:28px;font-weight:800;color:#f59e0b;margin-top:4px">{{ $stats['total_jobs'] }}</div>
    <div style="font-size:11.5px;color:var(--muted);margin-top:2px">Live vacancy listings</div>
  </div>

  <div class="glass-panel" style="padding:16px">
    <div style="font-size:12px;color:var(--muted);text-transform:uppercase;font-weight:700;letter-spacing:.05em">Applications</div>
    <div style="font-size:28px;font-weight:800;color:#a78bfa;margin-top:4px">{{ $stats['total_applications'] }}</div>
    <div style="font-size:11.5px;color:var(--muted);margin-top:2px">Candidate proposals</div>
  </div>
</div>

<!-- Filter Bar -->
<div class="glass-panel" style="margin-bottom:20px;padding:16px">
  <form method="GET" action="{{ route('debug.accounts.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;justify-content:space-between">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <a href="{{ route('debug.accounts.index') }}" 
         class="btn btn-sm {{ !$roleFilter ? 'btn-primary' : 'btn-ghost' }}">
        All Roles ({{ $stats['total_users'] }})
      </a>
      <a href="{{ route('debug.accounts.index', ['role' => 'employer', 'search' => $search]) }}" 
         class="btn btn-sm {{ $roleFilter === 'employer' ? 'btn-primary' : 'btn-ghost' }}">
        Employers ({{ $stats['total_employers'] }})
      </a>
      <a href="{{ route('debug.accounts.index', ['role' => 'freelancer', 'search' => $search]) }}" 
         class="btn btn-sm {{ $roleFilter === 'freelancer' ? 'btn-primary' : 'btn-ghost' }}">
        Freelancers ({{ $stats['total_freelancers'] }})
      </a>
    </div>

    <div style="display:flex;gap:8px;align-items:center">
      <input type="text" name="search" value="{{ $search }}" placeholder="Search name, email, company..." class="input" style="width:260px;font-size:13px;padding:6px 12px">
      @if($roleFilter)
        <input type="hidden" name="role" value="{{ $roleFilter }}">
      @endif
      <button type="submit" class="btn btn-ghost btn-sm">Search</button>
      @if($search || $roleFilter)
        <a href="{{ route('debug.accounts.index') }}" class="btn btn-ghost btn-sm" style="color:var(--muted)">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Accounts Data Table -->
<div class="glass-panel">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width:50px">ID</th>
          <th>User Account</th>
          <th>Role</th>
          <th>Organization / Profile</th>
          <th>Activity Telemetry</th>
          <th>Registered</th>
          <th style="text-align:right">Debugger Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr style="{{ auth()->id() === $user->id ? 'background:rgba(96,165,250,0.06)' : '' }}">
            <td style="font-weight:700;color:var(--muted);font-size:12px">#{{ $user->id }}</td>
            <td>
              <div style="font-weight:700;font-size:14.5px;color:var(--ink);display:flex;align-items:center;gap:6px">
                {{ $user->name }}
                @if(auth()->id() === $user->id)
                  <span class="badge badge-mint" style="font-size:10px;padding:2px 6px">Current Session</span>
                @endif
              </div>
              <div style="font-size:12.5px;color:var(--muted);font-family:monospace">{{ $user->email }}</div>
            </td>
            <td>
              @if($user->isEmployer())
                <span class="badge badge-blue">Employer</span>
              @else
                <span class="badge badge-mint">Freelancer</span>
              @endif
            </td>
            <td>
              @if($user->isEmployer() && $user->employer)
                <div style="font-weight:600;font-size:13.5px;color:var(--ink)">{{ $user->employer->company_name }}</div>
                <div style="font-size:12px;color:var(--muted)">{{ $user->employer->industry ?? 'General Business' }}</div>
              @else
                <div style="font-size:13px;color:var(--muted)">Student Candidate</div>
              @endif
            </td>
            <td>
              @if($user->isEmployer())
                <div style="font-size:13px;font-weight:600;color:#60a5fa">
                  {{ $user->employer?->jobPostings?->count() ?? 0 }} posted gigs
                </div>
              @else
                <div style="font-size:13px;color:var(--ink)">
                  <strong>{{ $user->applications_count }}</strong> proposals submitted
                </div>
                <div style="font-size:11.5px;color:var(--muted)">
                  {{ $user->bookmarks_count }} saved bookmarks
                </div>
              @endif
            </td>
            <td>
              <div style="font-size:13px;color:var(--ink)">{{ $user->created_at->format('M d, Y') }}</div>
              <div style="font-size:11.5px;color:var(--muted)">{{ $user->created_at->diffForHumans() }}</div>
            </td>
            <td style="text-align:right">
              <div style="display:inline-flex;gap:6px;align-items:center">
                @if(auth()->id() !== $user->id)
                  <form action="{{ route('debug.accounts.switch', $user) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm" style="color:#60a5fa;border:1px solid rgba(96,165,250,0.3);font-size:12px;padding:4px 10px" title="Log in as this user">
                      ⚡ Switch User
                    </button>
                  </form>
                @else
                  <span style="font-size:12px;color:var(--muted);font-style:italic;padding:4px 8px">Active User</span>
                @endif

                @if($user->id > 15)
                  <!-- Allow deletion for user-registered test accounts -->
                  <form action="{{ route('debug.accounts.destroy', $user) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this test account (#{{ $user->id }} {{ $user->name }})?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-sm" style="color:#f87171;font-size:12px;padding:4px 8px" title="Delete test account">
                      ✕
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align:center;padding:32px;color:var(--muted)">
              No registered accounts found matching your filter criteria.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($users->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--line)">
      {{ $users->links() }}
    </div>
  @endif
</div>
@endsection
