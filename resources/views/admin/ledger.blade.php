@extends('admin.layout')
@php $pageTitle = 'Ledger & Election Settings'; @endphp

@section('content')

<div class="page-header">
    <h2>Ledger &amp; Integrity</h2>
    <p>Blockchain-style vote ledger and election status controls.</p>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- ─── TOP ROW: settings + verify ─── --}}
<div class="grid-2" style="margin-bottom:24px;">

    {{-- Election Status Control --}}
    <div class="card">
        <div class="card-header">
            <h3>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Election Status
            </h3>
        </div>
        <div class="card-body">
            <div style="margin-bottom:16px;">
                <div style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:6px;">Current Status</div>
                @php
                    $statusClass = match($electionStatus) { 'open' => 'badge-green', 'closed' => 'badge-red', default => 'badge-amber' };
                    $statusLabel = match($electionStatus) { 'open' => 'Open', 'closed' => 'Closed', default => 'Pending' };
                @endphp
                <span class="badge {{ $statusClass }}" style="font-size:14px; padding:4px 14px;">{{ $statusLabel }}</span>
            </div>
            <form method="POST" action="{{ route('admin.settings.status') }}">
                @csrf
                <div class="form-group" style="margin-bottom:12px;">
                    <label>Change Status</label>
                    <select name="status" class="form-control">
                        <option value="open"    {{ $electionStatus === 'open'    ? 'selected' : '' }}>Open — voting allowed</option>
                        <option value="pending" {{ $electionStatus === 'pending' ? 'selected' : '' }}>Pending — not yet open</option>
                        <option value="closed"  {{ $electionStatus === 'closed'  ? 'selected' : '' }}>Closed — voting ended</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
            </form>
        </div>
    </div>

    {{-- Ledger Summary --}}
    <div class="card">
        <div class="card-header">
            <h3>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Ledger Integrity
            </h3>
        </div>
        <div class="card-body">
            <div style="display:flex; gap:20px; margin-bottom:18px;">
                <div>
                    <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Total Blocks</div>
                    <div style="font-size:28px;font-weight:800;color:#0f172a;margin-top:2px;">{{ $blocks->count() }}</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;">Last Block</div>
                    <div style="font-size:14px;font-weight:700;color:#0f172a;margin-top:6px;">
                        {{ $blocks->last() ? '#' . $blocks->last()->block_index : '—' }}
                    </div>
                </div>
            </div>

            @if (session('ledger_ok'))
                <div class="alert alert-success">{{ session('ledger_ok') }}</div>
            @endif

            @if (session('ledger_broken'))
                <div class="alert alert-error">{{ session('ledger_broken') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.ledger.verify') }}">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Verify Chain Integrity
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ─── LEDGER TABLE ─── --}}
<div class="card">
    <div class="card-header">
        <h3>
            Vote Ledger
            <span class="badge badge-blue" style="font-size:11px;">{{ $blocks->count() }} block(s)</span>
        </h3>
    </div>

    @if ($blocks->isEmpty())
        <div class="card-body" style="text-align:center; padding:48px; color:#94a3b8;">
            <div style="font-size:36px; margin-bottom:10px;">📋</div>
            <div style="font-size:14px;">No votes recorded yet. The ledger will populate as ballots are submitted.</div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Block Hash (truncated)</th>
                        <th>Previous Hash (truncated)</th>
                        <th>Receipt Code</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blocks as $block)
                        <tr>
                            <td style="font-weight:700;">{{ $block->block_index }}</td>
                            <td><span class="mono" style="font-size:11px;color:#1e3a8a;">{{ substr($block->block_hash, 0, 20) }}…</span></td>
                            <td>
                                @if ($block->block_index === 1)
                                    <span class="badge badge-gray" style="font-size:10px;">Genesis</span>
                                @else
                                    <span class="mono" style="font-size:11px;color:#64748b;">{{ substr($block->previous_hash, 0, 20) }}…</span>
                                @endif
                            </td>
                            <td><span class="mono badge badge-blue" style="font-size:11px;">{{ $block->receipt_code }}</span></td>
                            <td style="font-size:12px; color:#64748b;">{{ $block->block_timestamp->format('M d, Y H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
