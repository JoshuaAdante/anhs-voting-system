@extends('admin.layout')
@section('content')

<div class="page-header">
    <h2>Candidate Management</h2>
    <p>Candidates and elective positions</p>
</div>

{{-- ── Tabs ── --}}
<div class="tabs">
    <button class="tab-btn active" onclick="switchTab('candidates',this)">All Candidates</button>
    <button class="tab-btn" onclick="switchTab('positions',this)">Manage Positions</button>
</div>

{{-- ══ CANDIDATES TAB ══ --}}
<div class="tab-pane active" id="tab-candidates">

    <div style="margin-bottom:18px;">
        <button class="btn btn-primary" onclick="openAddCandidate()">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Candidate
        </button>
    </div>

    @forelse ($positions as $position)
    <div class="card" style="margin-bottom:18px;">
        <div class="card-header">
            <h3>{{ $position->name }}
                <span class="badge badge-gray">{{ $position->candidates_count ?? 0 }}</span>
            </h3>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:10px;">
                @forelse ($position->candidates as $c)
                <div class="candidate-card">
                    <div class="candidate-avatar">
                        @if ($c->photo_url)
                            <img src="{{ $c->photo_url }}" alt="{{ $c->full_name }}">
                        @else
                            {{ strtoupper(substr($c->full_name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="candidate-info">
                        <div class="candidate-name">{{ $c->full_name }}</div>
                        <div class="candidate-meta">
                            {{ implode(' · ', array_filter([$c->partylist, $c->grade, $c->section])) ?: '—' }}
                        </div>
                    </div>
                    <div class="candidate-actions">
                        <button class="btn btn-icon" title="Edit"
                                onclick="openEditCandidate({{ $c->id }}, '{{ addslashes($c->full_name) }}', '{{ $c->position_id }}', '{{ addslashes($c->partylist ?? '') }}', '{{ addslashes($c->grade ?? '') }}', '{{ addslashes($c->section ?? '') }}', '{{ addslashes($c->photo_url ?? '') }}')">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('admin.candidates.delete', $c->id) }}"
                              onsubmit="return confirm('Delete this candidate?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-icon" title="Delete">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <p style="font-size:13px;color:#94a3b8;">No candidates yet.</p>
                @endforelse
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="padding:28px;text-align:center;color:#94a3b8;">
        No positions yet. Go to <strong>Manage Positions</strong> tab to create one.
    </div>
    @endforelse

</div>{{-- end candidates tab --}}

{{-- ══ POSITIONS TAB ══ --}}
<div class="tab-pane" id="tab-positions">

    <div class="card" style="max-width:520px;">
        <div class="card-header"><h3>Add Position</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.positions.store') }}"
                  style="display:flex;gap:8px;">
                @csrf
                <input class="form-control" type="text" name="name"
                       placeholder="Position name (e.g. President)" required>
                <button type="submit" class="btn btn-primary" style="white-space:nowrap;">Add</button>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top:16px;max-width:520px;">
        <div class="card-body" style="padding:0;">
            @forelse ($positions as $position)
            <div style="display:flex;align-items:center;justify-content:space-between;
                         padding:12px 20px;border-bottom:1px solid #f1f5f9;">
                <span style="font-weight:600;font-size:14px;">{{ $position->name }}</span>
                <form method="POST" action="{{ route('admin.positions.delete', $position->id) }}"
                      onsubmit="return confirm('Delete position and all its candidates?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-icon" title="Delete position">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
            @empty
            <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">
                No positions yet.
            </div>
            @endforelse
        </div>
    </div>

</div>{{-- end positions tab --}}

{{-- ══ Add / Edit Candidate Modal ══ --}}
<div class="modal-overlay" id="addCandidateModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="candidateModalTitle">Add Candidate</h3>
            <button class="modal-close" onclick="closeModal('addCandidateModal')">✕</button>
        </div>
        <form method="POST" id="candidateForm" action="{{ route('admin.candidates.store') }}">
            @csrf
            <input type="hidden" name="_method" id="candidateMethod" value="POST">

            {{-- Student name search picker --}}
            <div class="form-group">
                <label>Student Name</label>
                <div style="position:relative;">
                    <input class="form-control" type="text" id="cf_student_search"
                           placeholder="Type last name or first name to search..."
                           autocomplete="off" oninput="filterCandidateStudents()">
                    <div id="cf_student_dropdown"
                         style="display:none;position:absolute;z-index:600;top:100%;left:0;right:0;
                                background:#fff;border:1.5px solid #e2e8f0;border-radius:8px;
                                margin-top:3px;max-height:200px;overflow-y:auto;
                                box-shadow:0 4px 12px rgba(0,0,0,0.10);">
                    </div>
                </div>
                {{-- Hidden field that holds the actual typed/selected full name --}}
                <input type="hidden" name="full_name" id="cf_full_name">
                <p id="cf_selected_info" style="font-size:12px;color:#1e3a8a;font-weight:600;margin-top:5px;"></p>
            </div>
            <div class="form-group">
                <label>Position</label>
                <select class="form-control" name="position_id" id="cf_position_id">
                    @foreach ($positions as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Partylist</label>
                    <input class="form-control" type="text" name="partylist" id="cf_partylist">
                </div>
                <div class="form-group">
                    <label>Grade</label>
                    <select class="form-control" name="grade" id="cf_grade">
                        @foreach (['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $g)
                            <option>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Section</label>
                    <input class="form-control" type="text" name="section" id="cf_section">
                </div>
                <div class="form-group">
                    <label>Photo URL <span style="color:#94a3b8;">(optional)</span></label>
                    <input class="form-control" type="url" name="photo_url" id="cf_photo_url">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addCandidateModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="candidateSubmitBtn">Save Candidate</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
@php
$studentsJson = $students->map(function($s) {
    return [
        'id'        => $s->id,
        'full_name' => $s->last_name . ', ' . $s->first_name . ($s->given_name ? ' ' . $s->given_name : ''),
        'grade'     => $s->grade,
        'section'   => $s->section,
    ];
})->values();
@endphp
<script>
// Students data from server
var allStudents = @json($studentsJson);

function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

function switchTab(name, btn) {
    document.querySelectorAll('.tab-pane').forEach(function(p) { p.classList.remove('active'); });
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

// Filter student dropdown as user types
function filterCandidateStudents() {
    var q        = document.getElementById('cf_student_search').value.toLowerCase().trim();
    var dropdown = document.getElementById('cf_student_dropdown');

    // Store the typed value directly as full_name too (allow free text)
    document.getElementById('cf_full_name').value = document.getElementById('cf_student_search').value;
    document.getElementById('cf_selected_info').textContent = '';

    if (q.length < 1) { dropdown.style.display = 'none'; return; }

    var matches = allStudents.filter(function(s) {
        return s.full_name.toLowerCase().includes(q);
    }).slice(0, 30);

    if (matches.length === 0) { dropdown.style.display = 'none'; return; }

    dropdown.innerHTML = '';
    matches.forEach(function(s) {
        var item = document.createElement('button');
        item.type = 'button';
        item.style.cssText = 'width:100%;text-align:left;padding:9px 12px;border:none;background:transparent;'
            + 'border-bottom:1px solid #f1f5f9;font-size:13px;cursor:pointer;color:#0f172a;font-family:inherit;';
        item.innerHTML = '<strong>' + s.full_name + '</strong> '
            + '<span style="font-size:11px;color:#64748b;">&middot; ' + s.grade + ' ' + s.section + '</span>';
        item.onmousedown = function(e) {
            e.preventDefault(); // prevent input blur before click
            selectCandidateStudent(s);
        };
        item.onmouseover  = function() { this.style.background = '#eff6ff'; };
        item.onmouseout   = function() { this.style.background = 'transparent'; };
        dropdown.appendChild(item);
    });

    dropdown.style.display = 'block';
}

function selectCandidateStudent(s) {
    document.getElementById('cf_student_search').value  = s.full_name;
    document.getElementById('cf_full_name').value       = s.full_name;
    document.getElementById('cf_grade').value           = s.grade;
    document.getElementById('cf_section').value         = s.section;
    document.getElementById('cf_selected_info').textContent = 'Selected: ' + s.grade + ' - ' + s.section;
    document.getElementById('cf_student_dropdown').style.display = 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    var dd = document.getElementById('cf_student_dropdown');
    if (dd && !dd.contains(e.target) && e.target.id !== 'cf_student_search') {
        dd.style.display = 'none';
    }
});

function openAddCandidate() {
    document.getElementById('candidateModalTitle').textContent = 'Add Candidate';
    document.getElementById('candidateForm').action = '{{ route("admin.candidates.store") }}';
    document.getElementById('candidateMethod').value         = 'POST';
    document.getElementById('cf_student_search').value       = '';
    document.getElementById('cf_full_name').value            = '';
    document.getElementById('cf_grade').value                = 'Grade 7';
    document.getElementById('cf_section').value              = '';
    document.getElementById('cf_partylist').value            = '';
    document.getElementById('cf_photo_url').value            = '';
    document.getElementById('cf_selected_info').textContent  = '';
    document.getElementById('cf_student_dropdown').style.display = 'none';
    document.getElementById('candidateSubmitBtn').textContent = 'Save Candidate';
    openModal('addCandidateModal');
}

function openEditCandidate(id, fullName, positionId, partylist, grade, section, photoUrl) {
    document.getElementById('candidateModalTitle').textContent = 'Edit Candidate';
    document.getElementById('candidateForm').action = '/admin/candidates/' + id;
    document.getElementById('candidateMethod').value         = 'PUT';
    document.getElementById('cf_student_search').value       = fullName;
    document.getElementById('cf_full_name').value            = fullName;
    document.getElementById('cf_position_id').value          = positionId;
    document.getElementById('cf_partylist').value            = partylist;
    document.getElementById('cf_grade').value                = grade || 'Grade 7';
    document.getElementById('cf_section').value              = section;
    document.getElementById('cf_photo_url').value            = photoUrl;
    document.getElementById('cf_selected_info').textContent  = '';
    document.getElementById('cf_student_dropdown').style.display = 'none';
    document.getElementById('candidateSubmitBtn').textContent = 'Update Candidate';
    openModal('addCandidateModal');
}

// Switch to positions tab if query param present
if (new URLSearchParams(location.search).get('tab') === 'positions') {
    switchTab('positions', document.querySelectorAll('.tab-btn')[1]);
}
</script>
@endpush

@endsection
