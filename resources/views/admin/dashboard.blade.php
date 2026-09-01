@extends('admin.layout')
@section('content')

{{-- ── Page header ── --}}
<div class="page-header">
    <h2>Dashboard</h2>
    <p>Quick stats and live election results</p>
</div>

{{-- ── Stat cards ── --}}
<div class="stats-grid" style="margin-bottom:24px;">
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

{{-- ── Charts row ── --}}
<div class="dash-charts-row" style="display:grid;grid-template-columns:clamp(260px,28%,320px) 1fr;gap:24px;margin-bottom:24px;align-items:start;">

    {{-- Donut: overall turnout --}}
    <div class="card">
        <div class="card-header">
            <h3>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:15px;height:15px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Voter Turnout
            </h3>
            <span class="badge badge-blue">{{ $stats['voted'] }} / {{ $stats['students'] }}</span>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;align-items:center;gap:16px;">
            <div style="position:relative;width:160px;height:160px;">
                <canvas id="turnoutDonut" width="160" height="160"></canvas>
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none;">
                    <span style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;">{{ $stats['turnout'] }}%</span>
                    <span style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;margin-top:2px;">Turnout</span>
                </div>
            </div>
            <div style="width:100%;">
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width:{{ $stats['turnout'] }}%;"></div>
                </div>
                <div class="progress-label">
                    <span>{{ $stats['voted'] }} voted</span>
                    <span>{{ $stats['students'] > 0 ? $stats['students'] - $stats['voted'] : 0 }} remaining</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Bar chart: votes per position --}}
    <div class="card">
        <div class="card-header">
            <h3>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:15px;height:15px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Vote Distribution by Position
            </h3>
            <span class="badge badge-gray">{{ count($results) }} position(s)</span>
        </div>
        <div class="card-body">
            @if ($results->isNotEmpty() && $results->sum('total') > 0)
                <div style="position:relative;height:{{ max(120, count($results) * 44) }}px;">
                    <canvas id="voteDistChart"></canvas>
                </div>
            @else
                <div style="text-align:center;padding:32px;color:#94a3b8;font-size:13px;">
                    No votes recorded yet. Charts will appear once ballots are submitted.
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ── Live Results ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <h3 style="font-size:16px;font-weight:800;color:#0f172a;display:flex;align-items:center;gap:8px;">
        <span class="live-dot"></span> Live Election Results
    </h3>
    <a href="{{ route('admin.candidates') }}" class="btn btn-secondary btn-sm">Manage Candidates</a>
</div>

@if ($results->isEmpty())
    <div class="card" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px;">
        <div style="font-size:36px;margin-bottom:12px;">🗳️</div>
        No positions set up yet. <a href="{{ route('admin.candidates') }}" style="color:var(--brand);">Add positions →</a>
    </div>
@else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:24px;">
        @foreach ($results as $position)
        <div class="card">
            <div class="card-header">
                <h3>{{ $position['name'] }}</h3>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="badge badge-gray">{{ count($position['candidates']) }} candidate(s)</span>
                    @if ($position['total'] > 0)
                        <span class="badge badge-blue">{{ $position['total'] }} vote(s)</span>
                    @endif
                </div>
            </div>
            <div class="card-body" style="padding-top:16px;">
                @if (count($position['candidates']) === 0)
                    <div style="text-align:center;padding:24px 0;color:#94a3b8;">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                             style="width:32px;height:32px;margin:0 auto 8px;display:block;color:#cbd5e1;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span style="font-size:13px;">No candidates added yet.</span>
                    </div>
                @else
                    @foreach ($position['candidates'] as $i => $c)
                    @php
                        $pct      = $position['total'] > 0 ? round($c['votes'] / $position['total'] * 100) : 0;
                        $isLeader = $i === 0 && $c['votes'] > 0;
                    @endphp
                    <div style="margin-bottom:{{ !$loop->last ? '14px' : '0' }};
                                {{ $isLeader ? 'background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:10px 12px;' : '' }}">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <div style="display:flex;align-items:center;gap:7px;min-width:0;flex:1;">
                                @if ($isLeader)
                                    <span style="font-size:14px;flex-shrink:0;">🏆</span>
                                @else
                                    <span style="font-size:11px;font-weight:700;color:#cbd5e1;flex-shrink:0;width:18px;text-align:center;">#{{ $i + 1 }}</span>
                                @endif
                                <span style="font-size:13px;font-weight:{{ $isLeader ? '700' : '600' }};
                                             color:{{ $isLeader ? '#15803d' : '#0f172a' }};
                                             white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $c['name'] }}
                                </span>
                            </div>
                            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;margin-left:8px;">
                                <span style="font-size:13px;font-weight:700;color:{{ $isLeader ? '#15803d' : '#0f172a' }};">
                                    {{ $c['votes'] }}
                                </span>
                                <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:99px;
                                             background:{{ $isLeader ? '#dcfce7' : '#f1f5f9' }};
                                             color:{{ $isLeader ? '#15803d' : '#64748b' }};">
                                    {{ $pct }}%
                                </span>
                            </div>
                        </div>
                        <div class="progress-bar-bg" style="height:5px;">
                            <div style="height:100%;border-radius:99px;width:{{ $pct }}%;
                                        background:{{ $isLeader ? 'linear-gradient(90deg,#16a34a,#4ade80)' : 'linear-gradient(90deg,#1e3a8a,#3b82f6)' }};
                                        transition:width 0.6s ease;"></div>
                        </div>
                    </div>
                    @endforeach

                    @if ($position['total'] === 0)
                        <div style="margin-top:12px;padding:8px 12px;background:#fafbfc;border-radius:8px;
                                    border:1px dashed #e2e8f0;text-align:center;font-size:12px;color:#94a3b8;">
                            No votes recorded yet for this position.
                        </div>
                    @endif
                @endif
            </div>
        </div>
        @endforeach
    </div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
    Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, sans-serif";

    // Turnout donut
    var donutEl = document.getElementById('turnoutDonut');
    if (donutEl) {
        new Chart(donutEl, {
            type: 'doughnut',
            data: {
                labels: ['Voted', 'Not yet'],
                datasets: [{
                    data: [{{ $stats['voted'] }}, {{ max(0, $stats['students'] - $stats['voted']) }}],
                    backgroundColor: ['#1e3a8a', '#e2e8f0'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }]
            },
            options: {
                cutout: '72%',
                responsive: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(c){ return ' '+c.label+': '+c.parsed; } } }
                }
            }
        });
    }

    // Vote distribution horizontal bar
    var barEl = document.getElementById('voteDistChart');
    if (barEl) {
        var positions = @json($results->pluck('name'));
        var totals    = @json($results->pluck('total'));
        var palette   = ['#1e3a8a','#3b82f6','#06b6d4','#8b5cf6','#f97316','#22c55e','#ef4444','#14b8a6'];
        new Chart(barEl, {
            type: 'bar',
            data: {
                labels: positions,
                datasets: [{
                    label: 'Total Votes',
                    data: totals,
                    backgroundColor: palette.slice(0, positions.length),
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(c){ return ' '+c.parsed.x+' vote(s)'; } } }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 }, color: '#94a3b8' },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        ticks: { font: { size: 12, weight: '600' }, color: '#334155' },
                        grid: { display: false }
                    }
                }
            }
        });
    }
})();
</script>
@endpush

@endsection
