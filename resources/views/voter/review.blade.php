@extends('voter.layout')
@php $pageTitle = 'Review Ballot'; $maxWidth = '640px'; @endphp

@section('content')

<div class="card">
    <div class="card-header">
        <h2>Review Your Ballot</h2>
        <p>Check your selections below. You can edit any choice before submitting.</p>
    </div>
    <div class="card-body">

        <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:28px;">
            @foreach ($summary as $i => $row)
                <div style="display:flex; align-items:center; gap:14px; border:1.5px solid #e2e8f0; border-radius:10px; padding:12px 16px; background:#f8fafc;">
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#64748b; margin-bottom:3px;">
                            {{ $row['position']->name }}
                        </div>
                        @if ($row['abstained'])
                            <div style="font-size:14px; font-weight:600; color:#64748b; display:flex; align-items:center; gap:6px;">
                                <span class="badge badge-gray">Abstained</span>
                            </div>
                        @else
                            <div style="font-size:14px; font-weight:700; color:#0f172a;">
                                {{ $row['candidate']?->full_name ?? '—' }}
                            </div>
                            @if ($row['candidate']?->partylist)
                                <div style="font-size:12px; color:#64748b;">{{ $row['candidate']->partylist }}</div>
                            @endif
                        @endif
                    </div>
                    <a href="{{ route('voter.vote', ['step' => $i]) }}" class="btn btn-ghost" style="font-size:12px; padding:5px 12px; white-space:nowrap;">
                        Edit
                    </a>
                </div>
            @endforeach
        </div>

        <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:14px 18px; margin-bottom:24px;">
            <div style="font-size:13px; color:#78350f; font-weight:600;">
                ⚠️ Once submitted, your ballot cannot be changed.
            </div>
            <div style="font-size:12px; color:#92400e; margin-top:4px;">
                Please review all your selections carefully before confirming.
            </div>
        </div>

        <form method="POST" action="{{ route('voter.submit') }}" onsubmit="setLoading()">
            @csrf
            <div style="display:flex; gap:10px;">
                <a href="{{ route('voter.vote', ['step' => count($summary) - 1]) }}" class="btn btn-ghost" style="flex:1; justify-content:center;">
                    Go Back
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn" style="flex:2; justify-content:center;">
                    <span class="spinner" id="spinner" style="display:none; width:16px;height:16px;border-radius:50%;border:2px solid rgba(255,255,255,0.4);border-top-color:#fff;animation:spin 0.7s linear infinite;"></span>
                    <svg id="submitIcon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="submitText">Confirm &amp; Submit Ballot</span>
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
<script>
function setLoading() {
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('submitIcon').style.display = 'none';
    document.getElementById('submitText').textContent = 'Submitting...';
    document.getElementById('submitBtn').disabled = true;
}
</script>
@endpush

@endsection
