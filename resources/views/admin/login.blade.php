<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html,body{height:100%}
body{font-family:'Segoe UI',system-ui,sans-serif;display:flex;min-height:100vh}

.brand-panel{
    display:none;
    width:50%;flex-shrink:0;
    flex-direction:column;justify-content:space-between;
    overflow:hidden;position:relative;
    padding:48px;color:#fff;
    background:linear-gradient(135deg,#1e3a8a 0%,#1e40af 55%,#2563eb 100%);
}
.brand-panel::before{
    content:'';position:absolute;inset:0;
    opacity:.15;pointer-events:none;
    background:radial-gradient(circle at 30% 25%,white,transparent 45%);
}
@media(min-width:1024px){.brand-panel{display:flex}}

.brand-logos{position:relative;display:flex;align-items:center;gap:16px}
.brand-logo-img{width:56px;height:56px;object-fit:contain;border-radius:50%}
.brand-logo-ph{
    width:56px;height:56px;border-radius:50%;
    background:rgba(255,255,255,.15);border:2px dashed rgba(255,255,255,.35);
    display:flex;align-items:center;justify-content:center;
    font-size:9px;color:rgba(255,255,255,.6);
}
.brand-body{position:relative}
.brand-tag{
    font-size:11px;font-weight:600;letter-spacing:.35em;
    text-transform:uppercase;color:#93c5fd;margin-bottom:16px;
}
.brand-title{font-size:36px;font-weight:900;line-height:1.18;margin-bottom:16px}
.brand-desc{font-size:14px;color:rgba(255,255,255,.75);max-width:380px;line-height:1.7}
.brand-footer{position:relative;font-size:11px;color:rgba(255,255,255,.5)}

.form-panel{
    flex:1;display:flex;
    align-items:center;justify-content:center;
    background:#fff;padding:64px 24px;min-height:100vh;
}
.form-inner{width:100%;max-width:384px}

.mobile-logos{display:flex;align-items:center;gap:12px}
@media(min-width:1024px){.mobile-logos{display:none}}
.mobile-logo-img{width:48px;height:48px;object-fit:contain;border-radius:50%}
.mobile-logo-ph{
    width:48px;height:48px;border-radius:50%;
    background:#e2e8f0;border:2px dashed #94a3b8;
    display:flex;align-items:center;justify-content:center;
    font-size:8px;color:#64748b;
}

.lock-badge{
    margin-top:24px;display:inline-flex;
    width:44px;height:44px;align-items:center;justify-content:center;
    border-radius:12px;
    background:linear-gradient(135deg,#1e3a8a,#2563eb);
    color:#fff;
}
.lock-badge svg{width:20px;height:20px}

.form-title{margin-top:20px;font-size:24px;font-weight:700;color:#0f172a;line-height:1.3}
.form-subtitle{margin-top:4px;font-size:14px;color:#64748b}

.alert{margin-top:16px;padding:10px 14px;border-radius:8px;font-size:13px}
.alert-error{background:#fff1f2;border:1px solid #fecdd3;color:#be123c}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534}

.auth-form{margin-top:32px;display:flex;flex-direction:column;gap:16px}
.form-group{display:flex;flex-direction:column;gap:8px}
.form-group label{font-size:14px;font-weight:500;color:#0f172a}
.form-group input{
    width:100%;padding:9px 13px;
    border:1px solid #e2e8f0;border-radius:8px;
    font-size:14px;color:#0f172a;background:#fff;
    outline:none;transition:border-color .15s,box-shadow .15s;
    font-family:inherit;
}
.form-group input:focus{border-color:#1e3a8a;box-shadow:0 0 0 3px rgba(30,58,138,.10)}
.form-group input::placeholder{color:#94a3b8}

.btn-submit{
    width:100%;padding:10px 16px;
    background:linear-gradient(135deg,#1e3a8a,#2563eb);
    color:#fff;font-size:14px;font-weight:600;
    border:none;border-radius:8px;cursor:pointer;
    display:flex;align-items:center;justify-content:center;gap:8px;
    transition:opacity .15s;font-family:inherit;
}
.btn-submit:hover{opacity:.88}
.btn-submit:disabled{opacity:.6;cursor:not-allowed}

.spinner{
    width:15px;height:15px;display:none;
    border:2px solid rgba(255,255,255,.3);
    border-top-color:#fff;border-radius:50%;
    animation:spin .7s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}

.btn-toggle{
    margin-top:16px;width:100%;
    background:transparent;border:none;
    font-size:14px;color:#64748b;
    cursor:pointer;text-align:center;
    text-underline-offset:4px;
    font-family:inherit;padding:4px 0;
}
.btn-toggle:hover{text-decoration:underline;color:#0f172a}
</style>
</head>
<body>

<div class="brand-panel">
    <div class="brand-logos">
        {{-- <img src="{{ asset('images/logo1.png') }}" class="brand-logo-img" alt="Logo 1"> --}}
        {{-- <img src="{{ asset('images/logo2.png') }}" class="brand-logo-img" alt="Logo 2"> --}}
        <div class="brand-logo-ph">L1</div>
        <div class="brand-logo-ph">L2</div>
    </div>

    <div class="brand-body">
        <p class="brand-tag">Election Committee</p>
        <h2 class="brand-title">Supreme Secondary<br>Learner Government<br>Election System</h2>
        <p class="brand-desc">Manage learners, candidates, one-time tokens and live results in one secure console.</p>
    </div>

    <p class="brand-footer">&copy; {{ date('Y') }} Agusan National High School &middot; Version 2.0</p>
</div>

<div class="form-panel">
    <div class="form-inner">

        <div class="mobile-logos">
            {{-- <img src="{{ asset('images/logo1.png') }}" class="mobile-logo-img" alt="Logo 1"> --}}
            {{-- <img src="{{ asset('images/logo2.png') }}" class="mobile-logo-img" alt="Logo 2"> --}}
            <div class="mobile-logo-ph">L1</div>
            <div class="mobile-logo-ph">L2</div>
        </div>

        <span class="lock-badge">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </span>

        <h1 class="form-title" id="formTitle">Admin Login</h1>
        <p class="form-subtitle" id="formSubtitle">Sign in to the election console.</p>

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form class="auth-form" id="signinForm"
              method="POST" action="{{ route('admin.login.post') }}"
              onsubmit="setLoading()">
            @csrf
            <div class="form-group">
                <label for="si_email">Email</label>
                <input id="si_email" type="email" name="email"
                       autocomplete="email" required
                       placeholder="admin@anhs.edu.ph"
                       value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label for="si_pass">Password</label>
                <input id="si_pass" type="password" name="password"
                       autocomplete="current-password" required
                       placeholder="••••••••">
            </div>
            <button type="submit" class="btn-submit" id="signinBtn">
                <span class="spinner" id="signinSpinner"></span>
                <span id="signinBtnText">Login</span>
            </button>
        </form>

    </div>
</div>

<script>
function setLoading() {
    document.getElementById('signinSpinner').style.display = 'block';
    document.getElementById('signinBtnText').textContent   = 'Signing in...';
    document.getElementById('signinBtn').disabled = true;
}
</script>

</body>
</html>
