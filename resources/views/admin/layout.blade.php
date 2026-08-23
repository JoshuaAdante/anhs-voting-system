<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Admin' }} – SSLG Election System</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --brand: #1e3a8a;
            --brand-hover: #1e40af;
            --brand-light: #eff6ff;
            --sidebar-w: 240px;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
        }

        /* ══════════ SIDEBAR ══════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--brand);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 200;
            box-shadow: 3px 0 14px rgba(0,0,0,0.15);
        }

        /* Brand */
        .sb-brand {
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.10);
        }

        .sb-logos {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .sb-logo-img {
            width: 34px; height: 34px;
            border-radius: 50%;
            object-fit: contain;
        }

        .sb-logo-ph {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 1.5px dashed rgba(255,255,255,0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 7px; color: rgba(255,255,255,0.65);
        }

        .sb-brand-name {
            font-size: 11px; font-weight: 800;
            color: #fff; line-height: 1.4;
            letter-spacing: 0.01em;
        }

        .sb-brand-sub {
            font-size: 10px; color: rgba(255,255,255,0.55);
            margin-top: 1px;
        }

        /* Nav */
        .sb-nav { flex: 1; padding: 8px 0; overflow-y: auto; }

        .sb-section {
            font-size: 9px; font-weight: 700;
            letter-spacing: 0.12em; text-transform: uppercase;
            color: rgba(255,255,255,0.38);
            padding: 12px 16px 4px;
        }

        .sb-link {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 16px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 13px; font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.15s;
        }

        .sb-link:hover {
            background: rgba(255,255,255,0.09);
            color: #fff;
            border-left-color: rgba(255,255,255,0.4);
        }

        .sb-link.active {
            background: rgba(255,255,255,0.14);
            color: #fff;
            border-left-color: #60a5fa;
            font-weight: 700;
        }

        .sb-link svg { width: 16px; height: 16px; flex-shrink: 0; }

        /* Bottom actions */
        .sb-bottom {
            border-top: 1px solid rgba(255,255,255,0.10);
            padding: 14px 16px;
        }

        .sb-logout {
            display: flex; align-items: center; gap: 9px;
            color: rgba(255,255,255,0.60);
            text-decoration: none; font-size: 13px;
            padding: 8px 0;
            transition: color 0.15s;
        }

        .sb-logout:hover { color: #f87171; }
        .sb-logout svg { width: 15px; height: 15px; }

        /* ══════════ MAIN ══════════ */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Topbar */
        .topbar {
            background: #fff;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }

        .topbar-left h1 {
            font-size: 16px; font-weight: 800; color: #0f172a;
        }

        .topbar-left p {
            font-size: 12px; color: #64748b; margin-top: 1px;
        }

        .topbar-right {
            display: flex; align-items: center; gap: 10px;
        }

        .admin-chip {
            padding: 4px 12px;
            background: var(--brand-light);
            color: var(--brand);
            font-size: 12px; font-weight: 700;
            border-radius: 20px;
        }

        /* Content */
        .content { padding: 24px 28px; flex: 1; }

        .page-header { margin-bottom: 22px; }
        .page-header h2 { font-size: 20px; font-weight: 800; color: #0f172a; }
        .page-header p  { font-size: 13px; color: #64748b; margin-top: 3px; }

        /* ══════════ CARDS ══════════ */
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        .card-header {
            padding: 14px 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h3 {
            font-size: 14px; font-weight: 700; color: #0f172a;
            display: flex; align-items: center; gap: 8px;
        }

        .card-body { padding: 20px; }

        /* ══════════ STATS ══════════ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px,1fr));
            gap: 14px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
            display: flex; align-items: center; gap: 14px;
        }

        .stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg { width: 22px; height: 22px; }

        .ic-blue   { background: #eff6ff; color: #3b82f6; }
        .ic-green  { background: #f0fdf4; color: #22c55e; }
        .ic-purple { background: #f5f3ff; color: #8b5cf6; }
        .ic-orange { background: #fff7ed; color: #f97316; }
        .ic-teal   { background: #f0fdfa; color: #14b8a6; }

        .stat-label {
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: #64748b;
        }

        .stat-value {
            font-size: 26px; font-weight: 800; color: #0f172a;
            line-height: 1.1; margin-top: 2px;
        }

        /* ══════════ PROGRESS BAR ══════════ */
        .progress-wrap { margin-bottom: 18px; }

        .progress-bar-bg {
            height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%; border-radius: 4px;
            background: linear-gradient(90deg, #1e3a8a, #3b82f6);
            transition: width 0.6s ease;
        }

        .progress-label {
            display: flex; justify-content: space-between;
            font-size: 12px; color: #64748b; margin-top: 5px;
        }

        /* ══════════ TABLE ══════════ */
        .table-wrap { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }

        thead th {
            padding: 10px 16px;
            text-align: left;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: #94a3b8; background: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
        }

        tbody td {
            padding: 12px 16px;
            font-size: 13px; color: #334155;
            border-bottom: 1px solid #f8fafc;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }

        /* ══════════ BADGES ══════════ */
        .badge {
            display: inline-flex; align-items: center;
            padding: 2px 10px;
            border-radius: 20px; font-size: 11px; font-weight: 700;
        }

        .badge-blue   { background: #eff6ff; color: #1d4ed8; }
        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-amber  { background: #fef3c7; color: #92400e; }
        .badge-red    { background: #fee2e2; color: #b91c1c; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .badge-purple { background: #f5f3ff; color: #6d28d9; }

        /* ══════════ BUTTONS ══════════ */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px; border: none;
            font-size: 13px; font-weight: 600; cursor: pointer;
            text-decoration: none; transition: opacity 0.15s;
        }

        .btn:hover { opacity: 0.85; }
        .btn svg { width: 15px; height: 15px; }

        .btn-primary   { background: var(--brand); color: #fff; }
        .btn-secondary { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
        .btn-danger    { background: #ef4444; color: #fff; }
        .btn-success   { background: #22c55e; color: #fff; }
        .btn-warning   { background: #f97316; color: #fff; }
        .btn-outline   { background: transparent; color: var(--brand); border: 1.5px solid var(--brand); }
        .btn-ghost     { background: transparent; color: #64748b; border: 1px solid #e2e8f0; }
        .btn-icon      { padding: 6px; border-radius: 6px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .btn-sm        { padding: 5px 12px; font-size: 12px; }

        /* ══════════ FORMS ══════════ */
        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 6px;
        }

        .form-control {
            width: 100%; padding: 9px 12px;
            border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13px; color: #0f172a; background: #f8fafc;
            outline: none; transition: border-color 0.2s;
            font-family: inherit;
        }

        .form-control:focus { border-color: var(--brand); background: #fff; }

        select.form-control { appearance: none; cursor: pointer; }

        /* ══════════ MODAL ══════════ */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(15,23,42,0.5); z-index: 500;
            align-items: center; justify-content: center;
        }

        .modal-overlay.open { display: flex; }

        .modal {
            background: #fff; border-radius: 14px;
            width: 100%; max-width: 480px; max-height: 90vh;
            overflow-y: auto; padding: 28px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }

        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }

        .modal-header h3 { font-size: 16px; font-weight: 700; color: #0f172a; }

        .modal-close {
            background: transparent; border: none; cursor: pointer;
            color: #94a3b8; font-size: 20px; line-height: 1;
            display: flex; align-items: center; justify-content: center;
            width: 28px; height: 28px; border-radius: 6px;
        }

        .modal-close:hover { background: #f1f5f9; color: #475569; }

        .modal-footer { margin-top: 22px; display: flex; gap: 8px; justify-content: flex-end; }

        /* ══════════ GRID HELPERS ══════════ */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .col-span-2 { grid-column: span 2; }

        /* ══════════ LIVE DOT ══════════ */
        .live-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #22c55e; display: inline-block;
            animation: pulse 1.4s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.25; }
        }

        /* ══════════ TABS ══════════ */
        .tabs { display: flex; gap: 4px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; }

        .tab-btn {
            padding: 9px 18px; background: transparent; border: none;
            font-size: 13px; font-weight: 600; color: #64748b;
            cursor: pointer; border-bottom: 2px solid transparent;
            margin-bottom: -2px; transition: all 0.15s;
        }

        .tab-btn.active { color: var(--brand); border-bottom-color: var(--brand); }
        .tab-btn:hover  { color: var(--brand); }

        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        /* ══════════ SEARCH INPUT ══════════ */
        .search-wrap { position: relative; }

        .search-wrap svg {
            position: absolute; left: 10px; top: 50%;
            transform: translateY(-50%);
            width: 15px; height: 15px; color: #94a3b8;
        }

        .search-wrap input { padding-left: 32px; }

        /* ══════════ CANDIDATE CARDS ══════════ */
        .candidate-card {
            display: flex; align-items: center; gap: 12px;
            border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;
        }

        .candidate-avatar {
            width: 48px; height: 48px; border-radius: 50%;
            background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
            color: #fff; font-size: 18px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden;
        }

        .candidate-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .candidate-info { flex: 1; min-width: 0; }
        .candidate-name { font-size: 13px; font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .candidate-meta { font-size: 11px; color: #64748b; margin-top: 1px; }

        .candidate-actions { display: flex; gap: 4px; }

        /* ══════════ ALERT ══════════ */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; }
        .alert-error   { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

        /* ══════════ TOKEN TABLE ══════════ */
        .mono { font-family: 'Courier New', monospace; letter-spacing: 0.12em; font-weight: 700; }

        /* ══════════ RESPONSIVE ══════════ */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.25s; }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .content { padding: 16px; }
            .topbar { padding: 12px 16px; }
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar" id="sidebar">

    <div class="sb-brand">
        <div class="sb-logos">
            {{-- <img src="{{ asset('images/logo1.png') }}" alt="L1" class="sb-logo-img"> --}}
            {{-- <img src="{{ asset('images/deped.png') }}" alt="L2" class="sb-logo-img"> --}}
            <div class="sb-logo-ph">L1</div>
            <div class="sb-logo-ph">L2</div>
        </div>
        <div class="sb-brand-name">SSLG Election System</div>
        <div class="sb-brand-sub">Agusan National High School</div>
    </div>

    <nav class="sb-nav">
        <div class="sb-section">Main</div>

        <a href="{{ route('admin.dashboard') }}"
           class="sb-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('admin.students') }}"
           class="sb-link {{ request()->routeIs('admin.students') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Students
        </a>

        <a href="{{ route('admin.candidates') }}"
           class="sb-link {{ request()->routeIs('admin.candidates') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            Candidates
        </a>

        <a href="{{ route('admin.system') }}"
           class="sb-link {{ request()->routeIs('admin.system') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            System / Tokens
        </a>

        <a href="{{ route('admin.ledger') }}"
           class="sb-link {{ request()->routeIs('admin.ledger') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Ledger
        </a>
    </nav>

    <div class="sb-bottom">
        <form method="POST" action="{{ route('admin.logout') }}" onsubmit="return confirm('Logout?')">
            @csrf
            <button type="submit" class="sb-logout" style="background:none;border:none;cursor:pointer;width:100%;text-align:left;font-family:inherit;display:flex;align-items:center;gap:9px;color:rgba(255,255,255,.60);font-size:13px;padding:8px 0;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:15px;height:15px;flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>

</aside>

<!-- ══════════ MAIN ══════════ -->
<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <h1>Agusan National High School</h1>
            <p>SSLG Election System · Admin Console</p>
        </div>
        <div class="topbar-right">
            <span class="admin-chip">
                {{ Auth::user()->name ?? 'Admin' }}
            </span>
        </div>
    </div>

    <div class="content">
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>

</div>

{{-- Page-specific scripts slot --}}
@stack('scripts')

</body>
</html>
