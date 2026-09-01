<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Admin' }} – SSLG Election System</title>

    <!-- Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Tailwind CSS CDN (preflight disabled – keeps existing page CSS intact) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        /* ─── Reset & base ─── */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            color: #1e293b;
        }

        /* Material icon sizing helper */
        .ms { font-family: 'Material Symbols Outlined'; font-weight: normal;
              font-style: normal; font-size: 20px; line-height: 1; display: inline-block;
              white-space: nowrap; word-wrap: normal; direction: ltr;
              -webkit-font-smoothing: antialiased; user-select: none;
              font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

        /* ─── CSS tokens (used by page templates) ─── */
        :root {
            --brand:        #1e3a8a;
            --brand-hover:  #1e40af;
            --brand-light:  #eff6ff;
            --green-active: #f0fdf4;
            --green-text:   #15803d;
            --green-border: #bbf7d0;
            --sidebar-w:    264px;
            --topbar-h:     64px;
            --radius-card:  1.5rem;
            --radius-sm:    0.75rem;
        }

        /* ══════════════════════════════════
           CARDS
        ══════════════════════════════════ */
        .card {
            background: #fff;
            border-radius: var(--radius-card);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 4px 16px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid #eef2f7;
        }
        .card-shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.07), 0 8px 28px rgba(0,0,0,0.06); }

        .card-header {
            padding: 16px 22px;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
            background: #fafbfc;
        }
        .card-header h3 {
            font-size: 14px; font-weight: 700; color: #0f172a;
            display: flex; align-items: center; gap: 8px;
        }
        .card-body { padding: 22px; }

        /* ══════════════════════════════════
           PAGE HEADER
        ══════════════════════════════════ */
        .page-header { margin-bottom: 24px; }
        .page-header h2 { font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.025em; }
        .page-header p  { font-size: 13px; color: #64748b; margin-top: 4px; font-weight: 500; }

        /* ══════════════════════════════════
           STATS GRID
        ══════════════════════════════════ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px; margin-bottom: 32px;
        }
        .stat-card {
            background: #fff; border-radius: var(--radius-card);
            padding: 28px 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 4px 16px rgba(0,0,0,0.05);
            border: 1px solid #eef2f7;
            display: flex; align-items: center; gap: 16px;
        }
        .stat-icon {
            width: 56px; height: 56px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-icon svg { width: 26px; height: 26px; }
        .ic-blue   { background: #eff6ff; color: #3b82f6; }
        .ic-green  { background: #f0fdf4; color: #22c55e; }
        .ic-purple { background: #f5f3ff; color: #8b5cf6; }
        .ic-orange { background: #fff7ed; color: #f97316; }
        .ic-teal   { background: #f0fdfa; color: #14b8a6; }
        .ic-red    { background: #fff1f2; color: #ef4444; }
        .stat-label {
            font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.07em; color: #94a3b8;
        }
        .stat-value {
            font-size: 32px; font-weight: 800; color: #0f172a;
            line-height: 1.1; margin-top: 5px; letter-spacing: -0.03em;
        }

        /* ══════════════════════════════════
           PROGRESS BAR
        ══════════════════════════════════ */
        .progress-wrap { margin-bottom: 18px; }
        .progress-bar-bg { height: 8px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
        .progress-bar-fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, var(--brand), #3b82f6);
            transition: width 0.7s ease;
        }
        .progress-label {
            display: flex; justify-content: space-between;
            font-size: 12px; color: #64748b; margin-top: 6px; font-weight: 500;
        }

        /* ══════════════════════════════════
           TABLE
        ══════════════════════════════════ */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 11px 18px; text-align: left;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.07em;
            color: #94a3b8; background: #fafbfc;
            border-bottom: 1px solid #f1f5f9;
        }
        tbody td { padding: 13px 18px; font-size: 13px; color: #334155; border-bottom: 1px solid #f8fafc; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #fafbfd; }

        /* ══════════════════════════════════
           BADGES
        ══════════════════════════════════ */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 700;
        }
        .badge-blue   { background: #eff6ff; color: #1d4ed8; }
        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-amber  { background: #fef3c7; color: #92400e; }
        .badge-red    { background: #fee2e2; color: #b91c1c; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .badge-purple { background: #f5f3ff; color: #6d28d9; }

        /* ══════════════════════════════════
           BUTTONS
        ══════════════════════════════════ */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 10px; border: none;
            font-size: 13px; font-weight: 600; cursor: pointer;
            text-decoration: none;
            transition: opacity 0.15s, transform 0.1s, box-shadow 0.15s;
            font-family: inherit; letter-spacing: -0.01em;
        }
        .btn:hover { opacity: 0.88; }
        .btn:active { transform: scale(0.97); }
        .btn svg { width: 15px; height: 15px; }
        .btn-primary   { background: var(--brand); color: #fff; box-shadow: 0 2px 8px rgba(30,58,138,0.25); }
        .btn-secondary { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
        .btn-danger    { background: #ef4444; color: #fff; }
        .btn-success   { background: #22c55e; color: #fff; }
        .btn-warning   { background: #f97316; color: #fff; }
        .btn-outline   { background: transparent; color: var(--brand); border: 1.5px solid var(--brand); }
        .btn-ghost     { background: transparent; color: #64748b; border: 1px solid #e2e8f0; }
        .btn-icon      { padding: 6px; border-radius: 8px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .btn-sm        { padding: 5px 12px; font-size: 12px; }

        /* ══════════════════════════════════
           FORMS
        ══════════════════════════════════ */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 9px 13px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 13px; color: #0f172a; background: #f8fafc;
            outline: none; transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }
        .form-control:focus { border-color: var(--brand); background: #fff; box-shadow: 0 0 0 3px rgba(30,58,138,0.08); }
        select.form-control { appearance: none; cursor: pointer; }

        /* ══════════════════════════════════
           MODAL
        ══════════════════════════════════ */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(15,23,42,0.45); z-index: 500;
            align-items: center; justify-content: center;
            backdrop-filter: blur(3px);
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: #fff; border-radius: var(--radius-card);
            width: 100%; max-width: 480px; max-height: 90vh;
            overflow-y: auto; padding: 28px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.22); border: 1px solid #eef2f7;
        }
        .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
        .modal-header h3 { font-size: 16px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
        .modal-close {
            background: #f1f5f9; border: none; cursor: pointer; color: #64748b;
            display: flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: 8px; font-size: 18px; line-height: 1;
            transition: background 0.12s, color 0.12s;
        }
        .modal-close:hover { background: #e2e8f0; color: #0f172a; }
        .modal-footer { margin-top: 22px; display: flex; gap: 8px; justify-content: flex-end; }

        /* ══════════════════════════════════
           GRID HELPERS
        ══════════════════════════════════ */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .col-span-2 { grid-column: span 2; }

        /* ══════════════════════════════════
           LIVE DOT
        ══════════════════════════════════ */
        .live-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #22c55e; display: inline-block;
            animation: livePulse 1.4s infinite;
        }
        @keyframes livePulse { 0%,100%{opacity:1} 50%{opacity:0.25} }

        /* ══════════════════════════════════
           TABS
        ══════════════════════════════════ */
        .tabs { display: flex; gap: 2px; border-bottom: 2px solid #e8edf3; margin-bottom: 22px; }
        .tab-btn {
            padding: 10px 20px; background: transparent; border: none;
            font-size: 13px; font-weight: 600; color: #64748b;
            cursor: pointer; border-bottom: 2px solid transparent;
            margin-bottom: -2px; transition: all 0.15s; font-family: inherit;
        }
        .tab-btn.active { color: var(--brand); border-bottom-color: var(--brand); }
        .tab-btn:hover  { color: var(--brand); }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        /* ══════════════════════════════════
           SEARCH INPUT
        ══════════════════════════════════ */
        .search-wrap { position: relative; }
        .search-wrap svg { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #94a3b8; }
        .search-wrap input { padding-left: 34px; }

        /* ══════════════════════════════════
           CANDIDATE CARDS
        ══════════════════════════════════ */
        .candidate-card {
            display: flex; align-items: center; gap: 12px;
            border: 1px solid #eef2f7; border-radius: 14px; padding: 13px;
            transition: box-shadow 0.15s, transform 0.15s;
        }
        .candidate-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform: translateY(-1px); }
        .candidate-avatar {
            width: 46px; height: 46px; border-radius: 50%;
            background: linear-gradient(135deg, var(--brand), #3b82f6);
            color: #fff; font-size: 18px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden;
        }
        .candidate-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .candidate-info { flex: 1; min-width: 0; }
        .candidate-name { font-size: 13px; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .candidate-meta { font-size: 11px; color: #64748b; margin-top: 2px; }
        .candidate-actions { display: flex; gap: 4px; }

        /* ══════════════════════════════════
           ALERT
        ══════════════════════════════════ */
        .alert { padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 18px; font-weight: 500; }
        .alert-error   { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

        /* ══════════════════════════════════
           MONO / TOKEN
        ══════════════════════════════════ */
        .mono { font-family: 'Courier New', monospace; letter-spacing: 0.12em; font-weight: 700; }

        /* ══════════════════════════════════
           PAGINATION
        ══════════════════════════════════ */
        nav[role="navigation"] svg { width: 20px; height: 20px; flex-shrink: 0; display: inline-block; }
        .pagination-bar {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px; padding: 14px 22px; border-top: 1px solid #f1f5f9;
        }
        .pagination-info { font-size: 12.5px; color: #64748b; font-weight: 500; }
        .pagination { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
        .pg-btn {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 32px; height: 32px; padding: 0 8px;
            border: 1px solid #e2e8f0; border-radius: 8px;
            background: #fff; color: #374151;
            font-size: 13px; font-weight: 500; font-family: inherit;
            cursor: pointer; text-decoration: none; line-height: 1;
            transition: background 0.12s, border-color 0.12s, color 0.12s, transform 0.08s;
            user-select: none;
        }
        .pg-btn svg { width: 16px; height: 16px; flex-shrink: 0; display: block; }
        .pg-btn:hover:not(:disabled):not(.pg-active):not(.pg-ellipsis) { background: #f1f5f9; border-color: #cbd5e1; }
        .pg-btn:active:not(:disabled):not(.pg-active):not(.pg-ellipsis) { background: #e2e8f0; transform: scale(0.95); }
        .pg-btn.pg-active { background: var(--brand); border-color: var(--brand); color: #fff; cursor: default; }
        .pg-btn:disabled, .pg-btn.pg-ellipsis { color: #9ca3af; cursor: default; background: #f9fafb; }

        /* ══════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════ */
        @media (max-width: 1280px) {
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 900px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.25s; }
            .sidebar.open { transform: translateX(0); }
            .grid-2 { grid-template-columns: 1fr; }
            .dash-charts-row { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 540px) {
            .pagination-bar { flex-direction: column; align-items: flex-start; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .stat-card { padding: 20px 16px; }
            .stat-value { font-size: 26px; }
        }
    </style>
</head>
<body>

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<aside id="sidebar"
    class="fixed left-0 top-0 h-full bg-white z-50 flex flex-col border-r border-slate-200"
    style="width:264px; box-shadow: 2px 0 20px rgba(0,0,0,0.06);">

    {{-- Brand --}}
    <div class="px-5 pt-5 pb-4 border-b border-slate-100">
        <div class="flex items-center gap-2 mb-2.5">
            <img src="{{ asset('images/anhs-logo.png') }}" class="w-9 h-9 rounded-full object-contain" alt="ANHS Logo">
            <img src="{{ asset('images/deped-logo.png') }}" class="w-9 h-9 rounded-full object-contain" alt="DepEd Logo">
        </div>
        <div style="font-size:11.5px;font-weight:800;color:#0f172a;letter-spacing:-0.01em;line-height:1.4;">SSLG Election System</div>
        <div style="font-size:10px;color:#94a3b8;font-weight:500;margin-top:1px;">Agusan National High School</div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-3 overflow-y-auto" style="display:flex;flex-direction:column;gap:2px;">
        <div style="font-size:9.5px;font-weight:700;letter-spacing:0.10em;text-transform:uppercase;color:#b0bac5;padding:8px 8px 4px;">Main</div>

        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 no-underline
                  {{ request()->routeIs('admin.dashboard') ? 'bg-green-50 border border-green-200' : 'hover:bg-slate-100' }}"
           style="{{ request()->routeIs('admin.dashboard') ? 'color:#15803d;font-weight:700;' : 'color:#4b5563;font-weight:500;' }}font-size:13.5px;">
            <span class="ms" style="{{ request()->routeIs('admin.dashboard') ? 'color:#15803d;' : 'color:#9ca3af;' }}">dashboard</span>
            Dashboard
        </a>

        <a href="{{ route('admin.students') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 no-underline
                  {{ request()->routeIs('admin.students') ? 'bg-green-50 border border-green-200' : 'hover:bg-slate-100' }}"
           style="{{ request()->routeIs('admin.students') ? 'color:#15803d;font-weight:700;' : 'color:#4b5563;font-weight:500;' }}font-size:13.5px;">
            <span class="ms" style="{{ request()->routeIs('admin.students') ? 'color:#15803d;' : 'color:#9ca3af;' }}">group</span>
            Students
        </a>

        <a href="{{ route('admin.candidates') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 no-underline
                  {{ request()->routeIs('admin.candidates') ? 'bg-green-50 border border-green-200' : 'hover:bg-slate-100' }}"
           style="{{ request()->routeIs('admin.candidates') ? 'color:#15803d;font-weight:700;' : 'color:#4b5563;font-weight:500;' }}font-size:13.5px;">
            <span class="ms" style="{{ request()->routeIs('admin.candidates') ? 'color:#15803d;' : 'color:#9ca3af;' }}">person_search</span>
            Candidates
        </a>

        <a href="{{ route('admin.system') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 no-underline
                  {{ request()->routeIs('admin.system') ? 'bg-green-50 border border-green-200' : 'hover:bg-slate-100' }}"
           style="{{ request()->routeIs('admin.system') ? 'color:#15803d;font-weight:700;' : 'color:#4b5563;font-weight:500;' }}font-size:13.5px;">
            <span class="ms" style="{{ request()->routeIs('admin.system') ? 'color:#15803d;' : 'color:#9ca3af;' }}">vpn_key</span>
            System / Tokens
        </a>

        <a href="{{ route('admin.ledger') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 no-underline
                  {{ request()->routeIs('admin.ledger') ? 'bg-green-50 border border-green-200' : 'hover:bg-slate-100' }}"
           style="{{ request()->routeIs('admin.ledger') ? 'color:#15803d;font-weight:700;' : 'color:#4b5563;font-weight:500;' }}font-size:13.5px;">
            <span class="ms" style="{{ request()->routeIs('admin.ledger') ? 'color:#15803d;' : 'color:#9ca3af;' }}">history_edu</span>
            Ledger
        </a>
    </nav>

    {{-- Admin card --}}
    <div class="px-3 pb-3 pt-2 border-t border-slate-100">
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white flex-shrink-0"
                 style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);font-size:13px;font-weight:800;">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div style="font-size:12.5px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div style="font-size:10.5px;color:#94a3b8;margin-top:1px;">Administrator</div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" onsubmit="return confirm('Logout?')">
                @csrf
                <button type="submit" title="Logout"
                        class="flex items-center justify-center p-1.5 rounded-lg transition-colors"
                        style="background:none;border:none;cursor:pointer;color:#94a3b8;"
                        onmouseover="this.style.background='#fee2e2';this.style.color='#ef4444'"
                        onmouseout="this.style.background='none';this.style.color='#94a3b8'">
                    <span class="ms" style="font-size:18px;">logout</span>
                </button>
            </form>
        </div>
    </div>

</aside>

<!-- ═══════════════ MAIN ═══════════════ -->
<div style="margin-left:264px;min-height:100vh;display:flex;flex-direction:column;">

    {{-- Header --}}
    <header class="z-40 flex items-center justify-between px-7"
            style="position:fixed;top:0;left:264px;right:0;height:64px;background:rgba(255,255,255,0.92);backdrop-filter:blur(12px);border-bottom:1px solid #e2e8f0;box-shadow:0 1px 10px rgba(0,0,0,0.05);">
        <div class="flex items-center gap-3">
            <h1 style="font-size:15px;font-weight:700;color:#0f172a;">Agusan National High School</h1>
            <span style="padding:3px 10px;background:#eff6ff;color:#1e3a8a;font-size:10px;font-weight:700;border-radius:99px;border:1px solid #bfdbfe;letter-spacing:0.04em;text-transform:uppercase;">SSLG Admin</span>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full"
                 style="background:#f8fafc;border:1px solid #e2e8f0;">
                <span style="font-size:12px;font-weight:700;color:#374151;">{{ Auth::user()->name ?? 'Admin' }}</span>
                <div class="flex items-center justify-center w-7 h-7 rounded-full"
                     style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);">
                    <span class="ms" style="font-size:15px;color:#fff;">person</span>
                </div>
            </div>
        </div>
    </header>

    {{-- Page content --}}
    <main class="flex-1 p-7" style="padding-top: calc(64px + 1.75rem);">
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

</div>

@stack('scripts')
</body>
</html>
