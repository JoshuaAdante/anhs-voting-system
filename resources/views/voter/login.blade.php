<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Login – SSLG Election System</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --brand: #1e3a8a;
            --brand-hover: #1e40af;
            --brand-light: #eff6ff;
        }

        html, body { height: 100%; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            display: flex;
            min-height: 100vh;
            background: #f1f5f9;
        }

        /* Left brand panel (desktop only) */
        .brand-panel {
            display: none;
            flex: 1;
            background: linear-gradient(145deg, #1e3a8a 0%, #1d4ed8 60%, #3b82f6 100%);
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 1024px) {
            .brand-panel { display: flex; }
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 360px; height: 360px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .brand-logos {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 28px;
        }

        .brand-logo-ph {
            width: 62px; height: 62px; border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 2px dashed rgba(255,255,255,0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; color: rgba(255,255,255,0.65);
        }

        .brand-logo-img { width: 62px; height: 62px; border-radius: 50%; object-fit: contain; }

        .brand-title {
            font-size: 26px; font-weight: 900; color: #fff;
            text-align: center; line-height: 1.3; z-index: 1;
        }

        .brand-sub {
            font-size: 14px; color: rgba(255,255,255,0.70);
            text-align: center; margin-top: 10px; z-index: 1;
        }

        .brand-badge {
            margin-top: 36px; z-index: 1;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.20);
            border-radius: 12px; padding: 18px 28px; text-align: center;
        }

        .brand-badge p {
            font-size: 13px; color: rgba(255,255,255,0.75); line-height: 1.7;
        }

        /* Right form panel */
        .form-panel {
            width: 100%; max-width: 440px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 40px 32px;
            background: #fff;
            min-height: 100vh;
        }

        @media (min-width: 1024px) {
            .form-panel { border-radius: 0; }
        }

        .form-inner { width: 100%; max-width: 360px; }

        /* Mobile logos */
        .mobile-logos {
            display: flex; align-items: center; justify-content: center;
            gap: 12px; margin-bottom: 22px;
        }

        @media (min-width: 1024px) { .mobile-logos { display: none; } }

        .mobile-logo-ph {
            width: 44px; height: 44px; border-radius: 50%;
            background: var(--brand-light);
            border: 1.5px dashed #93c5fd;
            display: flex; align-items: center; justify-content: center;
            font-size: 8px; color: #93c5fd;
        }

        .form-title {
            font-size: 22px; font-weight: 800; color: #0f172a;
            margin-bottom: 4px;
        }

        .form-subtitle {
            font-size: 13px; color: #64748b; margin-bottom: 28px;
        }

        /* Status banners */
        .status-banner {
            padding: 20px; border-radius: 10px; margin-bottom: 24px;
            text-align: center;
        }

        .status-banner.closed {
            background: #fff1f2; border: 1px solid #fecdd3;
        }

        .status-banner.pending {
            background: #fefce8; border: 1px solid #fde68a;
        }

        .status-banner-icon {
            font-size: 32px; margin-bottom: 8px;
        }

        .status-banner h3 {
            font-size: 15px; font-weight: 700; margin-bottom: 4px;
        }

        .status-banner.closed h3 { color: #be123c; }
        .status-banner.pending h3 { color: #92400e; }

        .status-banner p { font-size: 13px; }
        .status-banner.closed p { color: #9f1239; }
        .status-banner.pending p { color: #78350f; }

        /* Alert */
        .alert { padding: 11px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; }
        .alert-error   { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }
        .alert-warning { background: #fefce8; border: 1px solid #fde68a; color: #92400e; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

        /* Form controls */
        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 6px;
        }

        .token-input-wrap { position: relative; }

        .token-input {
            width: 100%;
            padding: 11px 40px 11px 14px;
            border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 18px; font-weight: 700; color: #0f172a;
            background: #f8fafc; outline: none;
            letter-spacing: 0.22em; text-transform: uppercase;
            font-family: 'Courier New', monospace;
            text-align: center;
            transition: border-color 0.2s;
        }

        .token-input:focus { border-color: var(--brand); background: #fff; }

        .token-input::placeholder {
            font-weight: 400; font-size: 13px; letter-spacing: 0.04em;
            color: #94a3b8; font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: var(--brand); color: #fff;
            border: none; border-radius: 8px; cursor: pointer;
            font-size: 14px; font-weight: 700;
            font-family: inherit; transition: background 0.15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }

        .btn-submit:hover:not(:disabled) { background: var(--brand-hover); }
        .btn-submit:disabled { opacity: 0.65; cursor: not-allowed; }

        .spinner {
            display: none; width: 16px; height: 16px; border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .form-hint {
            font-size: 12px; color: #94a3b8; text-align: center; margin-top: 14px;
        }

        .form-hint strong { color: #64748b; }
    </style>
</head>
<body>

<!-- Left brand panel -->
<div class="brand-panel">
    <div class="brand-logos">
        {{-- <img src="{{ asset('images/logo1.png') }}" class="brand-logo-img" alt="ANHS"> --}}
        {{-- <img src="{{ asset('images/deped.png') }}" class="brand-logo-img" alt="DepEd"> --}}
        <div class="brand-logo-ph">L1</div>
        <div class="brand-logo-ph">L2</div>
    </div>
    <div class="brand-title" style="z-index:1;">SSLG Election<br>System</div>
    <div class="brand-sub" style="z-index:1;">Agusan National High School<br>Supreme Secondary Learner Government</div>
    <div class="brand-badge">
        <p>Enter your one-time voting token<br>to cast your ballot securely.</p>
    </div>
</div>

<!-- Right form panel -->
<div class="form-panel">
    <div class="form-inner">

        <!-- Mobile logos -->
        <div class="mobile-logos">
            <div class="mobile-logo-ph">L1</div>
            <div class="mobile-logo-ph">L2</div>
        </div>

        <h1 class="form-title">Voter Login</h1>
        <p class="form-subtitle">Enter your voting token to proceed.</p>

        @if ($status === 'closed')
            <div class="status-banner closed">
                <div class="status-banner-icon">🔒</div>
                <h3>Election Closed</h3>
                <p>The election has ended. Voting is no longer available.</p>
            </div>
        @elseif ($status === 'pending')
            <div class="status-banner pending">
                <div class="status-banner-icon">⏳</div>
                <h3>Election Not Yet Open</h3>
                <p>The election has not started yet. Please check back later.</p>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if (session('error_used'))
            <div class="alert alert-warning">
                <strong>Token already used.</strong> This token has already been used to vote. Each token is single-use only.
            </div>
        @endif

        @if ($status === 'open')
        <form method="POST" action="{{ route('voter.login.post') }}" onsubmit="setLoading()">
            @csrf
            <div class="form-group">
                <label for="token">Voting Token</label>
                <div class="token-input-wrap">
                    <input id="token" type="text" name="token" class="token-input"
                           maxlength="8" required autocomplete="off"
                           placeholder="e.g. ABCD1234"
                           value="{{ old('token') }}"
                           oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g,'')">
                </div>
            </div>
            <button type="submit" class="btn-submit" id="submitBtn">
                <span class="spinner" id="spinner"></span>
                <span id="btnText">Sign In</span>
            </button>
        </form>
        @endif

        <p class="form-hint">
            Your token was provided by your class adviser.<br>
            <strong>Keep it private — it can only be used once.</strong>
        </p>

    </div>
</div>

<script>
function setLoading() {
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('btnText').textContent   = 'Verifying...';
    document.getElementById('submitBtn').disabled    = true;
}
</script>

</body>
</html>
