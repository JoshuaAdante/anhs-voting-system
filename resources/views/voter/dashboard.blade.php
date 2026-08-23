@extends('voter.layout')
@php $pageTitle = 'Dashboard'; $maxWidth = '560px'; @endphp

@section('content')

@if ($token->used)
{{-- ─── ALREADY VOTED STATE ─── --}}
<div class="card" style="text-align:center; padding:48px 32px;">
    <div style="font-size:56px; margin-bottom:16px;">✅</div>
    <h2 style="font-size:20px; font-weight:800; color:#0f172a; margin-bottom:8px;">You Have Already Voted</h2>
    <p style="font-size:14px; color:#64748b; max-width:340px; margin:0 auto 24px;">
        Your vote has been recorded and secured in the election ledger. Thank you for participating!
    </p>
    @if (session('voter_receipt_code'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:16px 24px; display:inline-block; margin-bottom:24px;">
            <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:4px;">Your Receipt Code</div>
            <div class="mono" style="font-size:22px; color:#166534; letter-spacing:0.25em;">{{ session('voter_receipt_code') }}</div>
        </div>
    @endif
    <div>
        <form method="POST" action="{{ route('voter.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-ghost">Logout</button>
        </form>
    </div>
</div>

@else
{{-- ─── READY TO VOTE STATE ─── --}}
<div class="card">
    <div class="card-header" style="text-align:center; padding:28px 24px 20px;">
        <div style="font-size:44px; margin-bottom:10px;">🗳️</div>
        <h2 style="font-size:18px;">Ready to Cast Your Vote</h2>
        <p>Review the candidates carefully before submitting. You can only vote once.</p>
    </div>
    <div class="card-body" style="text-align:center;">
        <div style="background:#f8fafc; border-radius:10px; padding:14px 20px; margin-bottom:24px; display:flex; gap:24px; justify-content:center; flex-wrap:wrap;">
            <div style="text-align:center;">
                <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.06em;">Token</div>
                <div class="mono" style="font-size:15px; color:#0f172a; margin-top:3px;">{{ $token->code }}</div>
            </div>
            @if($token->student)
            <div style="text-align:center;">
                <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.06em;">Student</div>
                <div style="font-size:14px; font-weight:600; color:#0f172a; margin-top:3px;">{{ $token->student->last_name }}, {{ $token->student->first_name }}</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.06em;">Grade / Section</div>
                <div style="font-size:14px; font-weight:600; color:#0f172a; margin-top:3px;">{{ $token->student->grade }} – {{ $token->student->section }}</div>
            </div>
            @endif
        </div>

        <a href="{{ route('voter.vote') }}" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Cast My Vote
        </a>

        <p style="font-size:12px; color:#94a3b8; margin-top:16px;">
            You will be guided through each position one by one.<br>
            You can review your choices before finalizing.
        </p>
    </div>
</div>
@endif

@endsection
