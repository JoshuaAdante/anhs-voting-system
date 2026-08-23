@extends('voter.layout')
@php $pageTitle = 'Cast Vote – ' . $position->name; $maxWidth = '640px'; @endphp

@section('content')

{{-- Stepper --}}
<div style="margin-bottom:24px;">
    <div class="stepper">
        @foreach ($positions as $i => $pos)
            @php
                $isDone   = $i < $step;
                $isActive = $i === $step;
            @endphp
            @if ($i > 0)
                <div class="step-line {{ $isDone ? 'done' : '' }}"></div>
            @endif
            <div class="stepper-step" style="flex-direction:column; align-items:center; gap:4px;">
                <div class="step-dot {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}">
                    @if ($isDone)
                        <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" style="width:14px;height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        {{ $i + 1 }}
                    @endif
                </div>
                <div class="step-label {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}" style="max-width:60px; overflow:hidden; text-overflow:ellipsis;">
                    {{ $pos->name }}
                </div>
            </div>
        @endforeach
        {{-- Review step --}}
        <div class="step-line"></div>
        <div class="stepper-step" style="flex-direction:column; align-items:center; gap:4px;">
            <div class="step-dot">R</div>
            <div class="step-label">Review</div>
        </div>
    </div>
</div>

{{-- Voting card --}}
<div class="card">
    <div class="card-header">
        <h2>{{ $position->name }}</h2>
        <p>Step {{ $step + 1 }} of {{ $total }} — Select one candidate or abstain.</p>
    </div>
    <div class="card-body">

        <form method="POST" action="{{ route('voter.step.save') }}">
            @csrf
            <input type="hidden" name="step" value="{{ $step }}">
            <input type="hidden" name="position_id" value="{{ $position->id }}">

            @if ($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <div class="choice-list" id="choiceList">

                @foreach ($position->candidates as $candidate)
                    @php
                        $isSelected = isset($selections[$position->id]) && (string)$selections[$position->id] === (string)$candidate->id;
                    @endphp
                    <label class="choice-item {{ $isSelected ? 'selected' : '' }}" data-value="{{ $candidate->id }}">
                        <input type="radio" name="candidate_id" value="{{ $candidate->id }}" {{ $isSelected ? 'checked' : '' }}>
                        <div class="choice-radio">
                            <div class="choice-radio-dot"></div>
                        </div>
                        <div class="choice-avatar">
                            @if ($candidate->photo_url)
                                <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->full_name }}">
                            @else
                                {{ strtoupper(substr($candidate->full_name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="choice-info">
                            <div class="choice-name">{{ $candidate->full_name }}</div>
                            <div class="choice-meta">
                                @if ($candidate->partylist) {{ $candidate->partylist }} &middot; @endif
                                {{ $candidate->grade ?? '' }} {{ $candidate->section ?? '' }}
                            </div>
                        </div>
                    </label>
                @endforeach

                {{-- Abstain option --}}
                @php $isAbstained = array_key_exists($position->id, $selections) && $selections[$position->id] === null; @endphp
                <label class="choice-item choice-abstain {{ $isAbstained ? 'selected' : '' }}" data-value="abstain">
                    <input type="radio" name="candidate_id" value="" {{ $isAbstained ? 'checked' : '' }}>
                    <div class="choice-radio">
                        <div class="choice-radio-dot"></div>
                    </div>
                    <div class="choice-avatar" style="background: #e2e8f0; color: #64748b; font-size:20px;">
                        –
                    </div>
                    <div class="choice-info">
                        <div class="choice-name" style="color:#475569;">Abstain</div>
                        <div class="choice-meta">Skip this position — your abstain will be recorded.</div>
                    </div>
                </label>

            </div>

            <div style="display:flex; gap:10px; justify-content:space-between; margin-top:4px;">
                @if ($step > 0)
                    <a href="{{ route('voter.vote', ['step' => $step - 1]) }}" class="btn btn-ghost">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                @else
                    <a href="{{ route('voter.dashboard') }}" class="btn btn-ghost">Cancel</a>
                @endif

                <button type="submit" class="btn btn-primary" id="nextBtn" disabled>
                    {{ $step < $total - 1 ? 'Next' : 'Review Ballot' }}
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var items   = document.querySelectorAll('#choiceList .choice-item');
    var nextBtn = document.getElementById('nextBtn');

    // Check if already has a selection from session
    var anyChecked = document.querySelector('#choiceList input[type="radio"]:checked');
    if (anyChecked) nextBtn.disabled = false;

    items.forEach(function (item) {
        item.addEventListener('click', function () {
            // Deselect all
            items.forEach(function (el) { el.classList.remove('selected'); });
            // Select clicked
            item.classList.add('selected');
            var radio = item.querySelector('input[type="radio"]');
            radio.checked = true;
            nextBtn.disabled = false;
        });
    });
})();
</script>
@endpush

@endsection
