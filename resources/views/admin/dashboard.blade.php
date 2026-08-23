@extends('admin.layout')
@section('content')

<div class="page-header">
    <h2>Dashboard</h2>
    <p>Quick stats and live election results</p>
</div>

{{-- ── Stats ── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon ic-blue">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ number_format($stats['students']) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon ic-green">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Already Voted</div>
            <div class="stat-value">{{ number_format($stats['voted']) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon ic-purple">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Turnout</div>
            <div class="stat-value">{{ $stats['turnout'] }}%</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon ic-orange">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Candidates</div>
            <div class="stat-value">{{ $stats['candidates'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon ic-teal">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Tokens Used</div>
            <div class="stat-value">{{ $stats['usedTokens'] }} / {{ $stats['tokens'] }}</div>
        </div>
    </div>
</div>

{{-- ── Voter Turnout Bar ── --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-body">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <span style="font-size:14px;font-weight:700;color:#0f172a;">Voter Turnout</span>
            <span class="badge badge-blue">{{ $stats['voted'] }} of {{ $stats['students'] }} learners</span>
        </div>
        <div class="progress-bar-bg">
            <div class="progress-bar-fill" style="width:{{ $stats['turnout'] }}%;"></div>
        </div>
        <div class="progress-label">
            <span>{{ $stats['turnout'] }}% turnout</span>
            <span>{{ $stats['students'] > 0 ? $stats['students'] - $stats['voted'] : 0 }} yet to vote</span>
        </div>
    </div>
</div>

{{-- ── Live Results ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
    <h3 style="font-size:16px;font-weight:800;color:#0f172a;display:flex;align-items:center;gap:8px;">
        <span class="live-dot"></span> Live Election Results
    </h3>
    <a href="{{ route('admin.candidates') }}" class="btn btn-secondary btn-sm">Manage Candidates</a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px;">
    @forelse ($results as $position)
    <div class="card">
        <div class="card-header">
            <h3>{{ $position['name'] }}</h3>
            <span class="badge badge-gray">{{ count($position['candidates']) }}</span>
        </div>
        <div class="card-body" style="padding-top:14px;">
            @forelse ($position['candidates'] as $i => $c)
            <div style="margin-bottom:14px;">
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px;margin-bottom:5px;">
                    <span style="font-weight:600;color:#0f172a;">
                        @if ($i === 0 && $c['votes'] > 0) 🏆 @endif
                        {{ $c['name'] }}
                    </span>
                    <span style="color:#64748b;font-size:12px;">
                        {{ $c['votes'] }} ({{ $position['total'] > 0 ? round($c['votes']/$position['total']*100) : 0 }}%)
                    </span>
                </div>
                <div class="progress-bar-bg" style="height:6px;">
                    <div class="progress-bar-fill"
                         style="width:{{ $position['total'] > 0 ? round($c['votes']/$position['total']*100) : 0 }}%;"></div>
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:#94a3b8;">No candidates yet.</p>
            @endforelse
        </div>
    </div>
    @empty
    <div class="card" style="padding:28px;text-align:center;color:#94a3b8;font-size:14px;">
        No positions set up yet. <a href="{{ route('admin.candidates') }}" style="color:var(--brand);">Add positions →</a>
    </div>
    @endforelse
</div>

@endsection
