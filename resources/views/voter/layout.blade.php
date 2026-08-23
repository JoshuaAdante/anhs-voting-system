<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'SSLG Election' }} – Voter Portal</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --brand: #1e3a8a;
            --brand-hover: #1e40af;
            --brand-light: #eff6ff;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            background: var(--brand);
            padding: 12px 24px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.18);
            position: sticky; top: 0; z-index: 100;
        }

        .topbar-left {
            display: flex; align-items: center; gap: 10px;
        }

        .topbar-logos {
            display: flex; gap: 6px;
        }

        .topbar-logo-ph {
            width: 28px; height: 28px; border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 1px dashed rgba(255,255,255,0.35);
            display: flex; align-items: center; justify-content: center;
            font-size: 7px; color: rgba(255,255,255,0.6);
        }

        .topbar-logo-img { width: 28px; height: 28px; border-radius: 50%; object-fit: contain; }

        .topbar-title {
            font-size: 13px; font-weight: 800; color: #fff;
            line-height: 1.3;
        }

        .topbar-sub { font-size: 10px; color: rgba(255,255,255,0.60); }

        .topbar-right {
            display: flex; align-items: center; gap: 10px;
        }

        .topbar-chip {
            padding: 3px 12px;
            background: rgba(255,255,255,0.14);
            color: rgba(255,255,255,0.85);
            font-size: 11px; font-weight: 700; border-radius: 20px;
            font-family: 'Courier New', monospace; letter-spacing: 0.1em;
        }

        .topbar-logout {
            background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.20);
            color: rgba(255,255,255,0.80); font-size: 12px; font-weight: 600;
            padding: 5px 12px; border-radius: 6px; cursor: pointer;
            font-family: inherit; transition: background 0.15s; display: inline-flex;
            align-items: center; gap: 5px; text-decoration: none;
        }

        .topbar-logout:hover { background: rgba(255,255,255,0.20); color: #fff; }
        .topbar-logout svg { width: 13px; height: 13px; }

        /* Content wrap */
        .page-wrap {
            flex: 1;
            display: flex; flex-direction: column;
            align-items: center;
            padding: 32px 16px;
        }

        .page-inner {
            width: 100%;
            max-width: {{ $maxWidth ?? '680px' }};
        }

        /* Card */
        .card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-header h2 {
            font-size: 16px; font-weight: 800; color: #0f172a;
        }

        .card-header p {
            font-size: 13px; color: #64748b; margin-top: 3px;
        }

        .card-body { padding: 24px; }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px; border-radius: 8px; border: none;
            font-size: 14px; font-weight: 700; cursor: pointer;
            text-decoration: none; transition: opacity 0.15s; font-family: inherit;
        }

        .btn:hover { opacity: 0.86; }
        .btn svg { width: 16px; height: 16px; }
        .btn-primary   { background: var(--brand); color: #fff; }
        .btn-secondary { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
        .btn-danger    { background: #ef4444; color: #fff; }
        .btn-ghost     { background: transparent; color: #64748b; border: 1px solid #e2e8f0; }
        .btn-lg        { padding: 14px 28px; font-size: 15px; }

        /* Alert */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; }
        .alert-error   { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }

        /* Badge */
        .badge {
            display: inline-flex; align-items: center;
            padding: 2px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }

        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-blue   { background: #eff6ff; color: #1d4ed8; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .badge-amber  { background: #fef3c7; color: #92400e; }

        /* Progress stepper */
        .stepper {
            display: flex; align-items: center;
            gap: 0; margin-bottom: 28px; overflow-x: auto;
            padding: 4px 0;
        }

        .stepper-step {
            display: flex; align-items: center; flex-shrink: 0;
        }

        .step-dot {
            width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 800;
            border: 2px solid #e2e8f0;
            background: #fff; color: #94a3b8;
            flex-shrink: 0;
        }

        .step-dot.done {
            background: #22c55e; border-color: #22c55e; color: #fff;
        }

        .step-dot.active {
            background: var(--brand); border-color: var(--brand); color: #fff;
        }

        .step-line {
            flex: 1; height: 2px; background: #e2e8f0; min-width: 16px; max-width: 40px;
        }

        .step-line.done { background: #22c55e; }

        .step-label {
            font-size: 10px; color: #94a3b8; text-align: center;
            margin-top: 4px; white-space: nowrap;
        }

        .step-label.active { color: var(--brand); font-weight: 700; }
        .step-label.done   { color: #15803d; }

        /* Form controls */
        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 6px;
        }

        .form-control {
            width: 100%; padding: 9px 12px;
            border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13px; color: #0f172a; background: #f8fafc;
            outline: none; transition: border-color 0.2s; font-family: inherit;
        }

        .form-control:focus { border-color: var(--brand); background: #fff; }

        /* Candidate choice cards */
        .choice-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }

        .choice-item {
            display: flex; align-items: center; gap: 14px;
            border: 2px solid #e2e8f0; border-radius: 10px; padding: 12px 16px;
            cursor: pointer; transition: border-color 0.15s, background 0.15s;
        }

        .choice-item:hover { border-color: #93c5fd; background: #f8fafc; }

        .choice-item.selected {
            border-color: var(--brand); background: var(--brand-light);
        }

        .choice-item input[type="radio"] { display: none; }

        .choice-radio {
            width: 20px; height: 20px; border-radius: 50%;
            border: 2px solid #cbd5e1; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            transition: border-color 0.15s;
        }

        .choice-item.selected .choice-radio {
            border-color: var(--brand);
        }

        .choice-radio-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: var(--brand); display: none;
        }

        .choice-item.selected .choice-radio-dot { display: block; }

        .choice-avatar {
            width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
            color: #fff; font-size: 16px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden;
        }

        .choice-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .choice-info { flex: 1; min-width: 0; }
        .choice-name { font-size: 14px; font-weight: 700; color: #0f172a; }
        .choice-meta { font-size: 12px; color: #64748b; margin-top: 2px; }

        .choice-abstain {
            border-style: dashed; border-color: #cbd5e1;
        }

        .choice-abstain:hover { border-color: #94a3b8; background: #f8fafc; }
        .choice-abstain.selected { border-color: #64748b; background: #f1f5f9; }
        .choice-abstain.selected .choice-radio { border-color: #64748b; }
        .choice-abstain.selected .choice-radio-dot { background: #64748b; }

        /* Mono */
        .mono { font-family: 'Courier New', monospace; letter-spacing: 0.12em; font-weight: 700; }

        @media (max-width: 600px) {
            .topbar-sub { display: none; }
            .card-body  { padding: 16px; }
        }
    </style>
</head>
<body>

<!-- Topbar -->
<header class="topbar">
    <div class="topbar-left">
        <div class="topbar-logos">
            {{-- <img src="{{ asset('images/logo1.png') }}" class="topbar-logo-img" alt="ANHS"> --}}
            <div class="topbar-logo-ph">L1</div>
            <div class="topbar-logo-ph">L2</div>
        </div>
        <div>
            <div class="topbar-title">SSLG Election System</div>
            <div class="topbar-sub">Agusan National High School</div>
        </div>
    </div>
    <div class="topbar-right">
        @if(session('voter_token_id'))
            <span class="topbar-chip">
                {{ \App\Models\VotingToken::find(session('voter_token_id'))?->code ?? '' }}
            </span>
            <form method="POST" action="{{ route('voter.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="topbar-logout">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        @endif
    </div>
</header>

<!-- Page content -->
<div class="page-wrap">
    <div class="page-inner" style="max-width: {{ $maxWidth ?? '680px' }};">
        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
