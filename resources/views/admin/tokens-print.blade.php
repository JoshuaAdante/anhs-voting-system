<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Token Cards – SSLG Election</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            padding: 20px;
        }

        /* Screen control bar */
        .screen-bar {
            max-width: 960px;
            margin: 0 auto 20px;
            background: #fff;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.08);
            display: flex;
            gap: 14px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .screen-bar h1 { font-size: 15px; font-weight: 800; color: #0f172a; }
        .screen-bar p  { font-size: 12px; color: #64748b; margin-top: 2px; }

        .filter-group {
            display: flex; gap: 8px; align-items: flex-end;
            flex-wrap: wrap; flex: 1;
        }

        .filter-item { display: flex; flex-direction: column; gap: 4px; }

        .filter-item label {
            font-size: 11px; font-weight: 700; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.06em;
        }

        .filter-item select {
            padding: 7px 10px;
            border: 1.5px solid #e2e8f0; border-radius: 7px;
            font-size: 13px; color: #0f172a; background: #f8fafc;
            outline: none; font-family: inherit; cursor: pointer;
            min-width: 145px;
        }

        .filter-item select:focus { border-color: #1e3a8a; }

        .count-chip {
            display: inline-flex; align-items: center;
            background: #eff6ff; color: #1d4ed8;
            border-radius: 20px; padding: 4px 14px;
            font-size: 12px; font-weight: 700;
        }

        .btn-print {
            background: #1e3a8a; color: #fff;
            border: none; border-radius: 8px;
            padding: 9px 20px; font-size: 14px; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; gap: 7px;
            font-family: inherit; white-space: nowrap;
        }

        .btn-print:hover { background: #1e40af; }
        .btn-print svg { width: 15px; height: 15px; }

        .btn-back {
            background: #f1f5f9; color: #334155;
            border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 9px 16px; font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px;
            font-family: inherit; white-space: nowrap;
        }

        /* Cards grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 14px;
            max-width: 960px;
            margin: 0 auto;
        }

        /* Token card */
        .token-card {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .card-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
            padding: 12px 14px;
            display: flex; align-items: center; gap: 9px;
        }

        .card-logos { display: flex; gap: 5px; flex-shrink: 0; }

        .logo-ph {
            width: 30px; height: 30px; border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 1.5px dashed rgba(255,255,255,0.40);
            display: flex; align-items: center; justify-content: center;
            font-size: 7px; color: rgba(255,255,255,0.60);
            overflow: hidden; flex-shrink: 0;
        }

        .logo-img { width: 30px; height: 30px; border-radius: 50%; object-fit: contain; }

        .card-school { flex: 1; min-width: 0; }

        .card-school-name {
            font-size: 10px; font-weight: 800; color: #fff; line-height: 1.3;
        }

        .card-school-sub {
            font-size: 9px; color: rgba(255,255,255,0.65); margin-top: 1px;
        }

        .card-body { padding: 13px 14px 14px; }

        .student-name {
            font-size: 14px; font-weight: 800; color: #0f172a;
            margin-bottom: 2px; line-height: 1.3;
        }

        .student-meta {
            font-size: 11px; color: #64748b; margin-bottom: 12px;
        }

        .token-box {
            border: 2px dashed #1e3a8a; border-radius: 8px;
            padding: 10px 12px; text-align: center; margin-bottom: 10px;
        }

        .token-label {
            font-size: 8px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.14em; color: #64748b; margin-bottom: 5px;
        }

        .token-code {
            font-family: 'Courier New', 'Consolas', monospace;
            font-size: 22px; font-weight: 900; color: #1e3a8a; letter-spacing: 0.26em;
        }

        .token-hint { font-size: 9px; color: #94a3b8; margin-top: 4px; }

        .card-footer {
            display: flex; align-items: center; justify-content: space-between;
        }

        .status-used {
            font-size: 10px; font-weight: 700; color: #b91c1c;
            background: #fee2e2; border-radius: 20px; padding: 2px 10px;
        }

        .status-unused {
            font-size: 10px; font-weight: 700; color: #15803d;
            background: #dcfce7; border-radius: 20px; padding: 2px 10px;
        }

        .private-note { font-size: 9px; color: #cbd5e1; }

        .empty {
            text-align: center; padding: 64px 32px; color: #94a3b8;
            max-width: 960px; margin: 0 auto;
        }

        /* Print styles */
        @media print {
            body { background: #fff; padding: 4px; }
            .screen-bar { display: none !important; }

            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px; max-width: 100%;
            }

            .token-card { border: 1px solid #cbd5e1; border-radius: 8px; }

            @page { size: A4; margin: 10mm; }
        }
    </style>
</head>
<body>

<!-- Screen filter/action bar -->
<div class="screen-bar">
    <div>
        <h1>Print Token Cards</h1>
        <p>Select grade and section, then click Print.</p>
    </div>

    <form method="GET" action="{{ route('admin.tokens.print') }}" class="filter-group" id="filterForm">
        <div class="filter-item">
            <label>Grade</label>
            <select name="grade" id="gradeSelect" onchange="updateSections(); this.form.submit()">
                <option value="">All grades</option>
                @foreach ($sectionsByGrade->keys() as $grade)
                    <option value="{{ $grade }}" {{ $selectedGrade == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-item" id="sectionWrap" style="{{ $selectedGrade ? '' : 'display:none' }}">
            <label>Section</label>
            <select name="section" id="sectionSelect" onchange="this.form.submit()">
                <option value="">All sections</option>
                @if ($selectedGrade && isset($sectionsByGrade[$selectedGrade]))
                    @foreach ($sectionsByGrade[$selectedGrade] as $sec)
                        <option value="{{ $sec }}" {{ $selectedSection == $sec ? 'selected' : '' }}>{{ $sec }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="filter-item" style="justify-content:flex-end;">
            <span class="count-chip">{{ $tokens->count() }} card(s)</span>
        </div>
    </form>

    <div style="display:flex;gap:8px;align-items:center;">
        <a href="{{ route('admin.system') }}" class="btn-back">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
        <button class="btn-print" onclick="window.print()">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        </button>
    </div>
</div>

<!-- Cards -->
@if ($tokens->isEmpty())
    <div class="empty">
        <div style="font-size:48px;margin-bottom:12px;">🔑</div>
        <div style="font-size:15px;font-weight:700;color:#475569;margin-bottom:6px;">No tokens found</div>
        <div style="font-size:13px;">
            @if ($selectedGrade)
                No tokens match the selected filter. Make sure tokens have been generated for this grade/section.
            @else
                Generate tokens first from the System page, then come back here to print.
            @endif
        </div>
    </div>
@else
    <div class="cards-grid">
        @foreach ($tokens as $token)
        <div class="token-card">

            <div class="card-header">
                <div class="card-logos">
                    {{-- <img src="{{ asset('images/logo1.png') }}" class="logo-img" alt="ANHS"> --}}
                    {{-- <img src="{{ asset('images/deped.png') }}" class="logo-img" alt="DepEd"> --}}
                    <div class="logo-ph">L1</div>
                    <div class="logo-ph">L2</div>
                </div>
                <div class="card-school">
                    <div class="card-school-name">Agusan National High School</div>
                    <div class="card-school-sub">SSLG Election System &middot; Official Voting Token</div>
                </div>
            </div>

            <div class="card-body">
                @if ($token->student)
                    <div class="student-name">
                        {{ $token->student->last_name }}, {{ $token->student->first_name }}
                        {{ $token->student->given_name ?? '' }}
                    </div>
                    <div class="student-meta">
                        {{ $token->student->grade }}
                        &middot; {{ $token->student->section }}
                        &middot; {{ $token->student->sex }}
                    </div>
                @else
                    <div class="student-name" style="color:#94a3b8;">Unassigned Token</div>
                    <div class="student-meta">{{ $token->grade ?? '—' }}</div>
                @endif

                <div class="token-box">
                    <div class="token-label">One-Time Voting Token</div>
                    <div class="token-code">{{ $token->code }}</div>
                    <div class="token-hint">Enter this code at the voting station &middot; Single use only</div>
                </div>

                <div class="card-footer">
                    <span class="{{ $token->used ? 'status-used' : 'status-unused' }}">
                        {{ $token->used ? 'Already Used' : 'Unused' }}
                    </span>
                    <span class="private-note">Keep this token private</span>
                </div>
            </div>

        </div>
        @endforeach
    </div>
@endif

<script>
var sectionsByGrade = @json($sectionsByGrade);

function updateSections() {
    var grade    = document.getElementById('gradeSelect').value;
    var wrap     = document.getElementById('sectionWrap');
    var select   = document.getElementById('sectionSelect');
    var sections = (grade && sectionsByGrade[grade]) ? sectionsByGrade[grade] : [];

    select.innerHTML = '<option value="">All sections</option>';
    sections.forEach(function(s) {
        var opt = document.createElement('option');
        opt.value = s; opt.textContent = s;
        select.appendChild(opt);
    });

    wrap.style.display = sections.length > 0 ? '' : 'none';
}
</script>

</body>
</html>
