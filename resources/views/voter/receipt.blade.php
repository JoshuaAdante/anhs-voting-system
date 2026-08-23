@extends('voter.layout')
@php $pageTitle = 'Vote Receipt'; $maxWidth = '520px'; @endphp

@section('content')

<div class="card" style="text-align:center; overflow:visible;">
    <div style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8); padding:36px 32px 48px; border-radius:14px 14px 0 0; position:relative;">
        <div style="font-size:52px; margin-bottom:12px;">🗳️</div>
        <h2 style="font-size:20px; font-weight:900; color:#fff; margin-bottom:6px;">Vote Recorded!</h2>
        <p style="font-size:14px; color:rgba(255,255,255,0.75);">Your ballot has been securely submitted and added to the election ledger.</p>
    </div>

    {{-- Receipt code card --}}
    <div style="margin:-24px 32px 0; position:relative; z-index:1;">
        <div style="background:#fff; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.12); padding:24px;">
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.10em; color:#64748b; margin-bottom:10px;">
                Your Verification Receipt Code
            </div>
            <div class="mono" style="font-size:28px; color:#1e3a8a; letter-spacing:0.30em; margin-bottom:10px; word-break:break-all;">
                {{ $receiptCode }}
            </div>
            @if ($blockIndex)
                <div style="font-size:12px; color:#94a3b8;">
                    Ledger block #{{ $blockIndex }}
                </div>
            @endif
        </div>
    </div>

    <div class="card-body" style="padding-top:32px;">

        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px 18px; margin-bottom:24px; text-align:left;">
            <div style="font-size:13px; font-weight:700; color:#166534; margin-bottom:4px;">What is this code for?</div>
            <div style="font-size:13px; color:#14532d; line-height:1.6;">
                Your receipt code uniquely identifies your ballot in the election ledger.
                You can use it to verify your vote was counted — without revealing who you voted for.
                <strong>Write it down or take a photo.</strong>
            </div>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <form method="POST" action="{{ route('voter.logout') }}" style="flex:1;">
                @csrf
                <button type="submit" class="btn btn-ghost" style="width:100%; justify-content:center;">
                    Logout
                </button>
            </form>
            <a href="{{ route('voter.dashboard') }}" class="btn btn-primary" style="flex:1; justify-content:center;">
                View Dashboard
            </a>
        </div>

    </div>
</div>

@endsection
