@extends('admin.layout')
@section('content')

<div class="page-header">
    <h2>System Management</h2>
    <p>Generate one-time voting tokens — each token can only be used once</p>
</div>

<div style="display:grid;grid-template-columns:360px 1fr;gap:18px;align-items:start;">

    {{-- ── Token Generator Card ── --}}
    <div class="card">
        <div class="card-header">
            <h3>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                Generate Tokens
            </h3>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('admin.tokens.generate') }}" id="genForm">
                @csrf
                <div class="form-group">
                    <label>Generate by</label>
                    <select class="form-control" name="mode" id="genMode" onchange="toggleGenMode()">
                        <option value="name">By learner name</option>
                        <option value="grade">By grade level</option>
                    </select>
                </div>

                {{-- By name --}}
                <div id="byName">
                    <div class="form-group">
                        <label>Search learner</label>
                        <div class="search-wrap" style="margin-bottom:8px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input class="form-control" type="text" id="studentSearch"
                                   placeholder="Type a name..." oninput="filterStudents()">
                        </div>
                        <div style="max-height:180px;overflow-y:auto;border:1.5px solid #e2e8f0;border-radius:8px;padding:4px;" id="studentList">
                            @foreach ($students as $s)
                            <button type="button"
                                    class="student-item"
                                    data-id="{{ $s->id }}"
                                    data-name="{{ $s->last_name }}, {{ $s->first_name }} · {{ $s->grade }} {{ $s->section }}"
                                    onclick="selectStudent(this)"
                                    style="width:100%;text-align:left;padding:8px 10px;border:none;background:transparent;border-radius:6px;font-size:12px;cursor:pointer;color:#334155;">
                                {{ $s->last_name }}, {{ $s->first_name }}
                                <span style="color:#94a3b8;">· {{ $s->grade }} {{ $s->section }}</span>
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="student_id" id="selectedStudentId">
                        <p id="selectedStudentName" style="font-size:12px;color:#1e3a8a;font-weight:600;margin-top:6px;"></p>
                    </div>
                </div>

                {{-- By grade --}}
                <div id="byGrade" style="display:none;">
                    <div class="form-group">
                        <label>Grade level</label>
                        <select class="form-control" name="grade" id="gradeSelect" onchange="updateSections()">
                            <option value="">-- Select grade --</option>
                            @foreach (['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $g)
                                <option>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="sectionWrap" style="display:none;">
                        <label>Section <span style="font-weight:400;color:#94a3b8;">(optional — leave blank for all)</span></label>
                        <select class="form-control" name="section" id="sectionSelect">
                            <option value="">All sections</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;margin-bottom:8px;">
                    Generate Token(s)
                </button>
            </form>

            <a href="{{ route('admin.tokens.print') }}" target="_blank" class="btn btn-secondary" style="width:100%;margin-bottom:8px;display:flex;align-items:center;gap:6px;justify-content:center;text-decoration:none;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:15px;height:15px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Token Cards
            </a>

            <form method="POST" action="{{ route('admin.tokens.deleteUnused') }}"
                  onsubmit="return confirm('Delete all unused tokens?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="width:100%;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:15px;height:15px;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Unused Tokens
                </button>
            </form>

        </div>
    </div>

    {{-- ── Tokens Table ── --}}
    <div class="card">
        <div class="card-header">
            <h3>Tokens</h3>
            <span class="badge badge-blue">
                {{ $unusedCount }} unused / {{ $tokens->total() }} total
            </span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Assigned To</th>
                        <th>Grade</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tokens as $t)
                    <tr>
                        <td class="mono">{{ $t->code }}</td>
                        <td>{{ $t->student ? $t->student->last_name.', '.$t->student->first_name : 'Unassigned' }}</td>
                        <td>{{ $t->grade ?? '—' }}</td>
                        <td>
                            @if ($t->used)
                                <span class="badge badge-gray">Used</span>
                            @else
                                <span class="badge badge-green">Unused</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.tokens.delete', $t->id) }}"
                                  onsubmit="return confirm('Delete this token?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon" title="Delete">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:32px;color:#94a3b8;">
                            No tokens generated yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tokens->hasPages())
        <div style="padding:14px 20px;border-top:1px solid #f1f5f9;">
            {{ $tokens->links() }}
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    // Grade -> sections map from server
    var sectionsByGrade = @json($sectionsByGrade);

    function toggleGenMode() {
        var mode = document.getElementById('genMode').value;
        document.getElementById('byName').style.display  = mode === 'name'  ? '' : 'none';
        document.getElementById('byGrade').style.display = mode === 'grade' ? '' : 'none';
    }

    function updateSections() {
        var grade   = document.getElementById('gradeSelect').value;
        var wrap    = document.getElementById('sectionWrap');
        var select  = document.getElementById('sectionSelect');
        var sections = grade && sectionsByGrade[grade] ? sectionsByGrade[grade] : [];

        // Rebuild options
        select.innerHTML = '<option value="">All sections</option>';
        sections.forEach(function (s) {
            var opt = document.createElement('option');
            opt.value = s; opt.textContent = s;
            select.appendChild(opt);
        });

        wrap.style.display = sections.length > 0 ? '' : 'none';
    }

    function filterStudents() {
        var q = document.getElementById('studentSearch').value.toLowerCase();
        document.querySelectorAll('.student-item').forEach(function (btn) {
            btn.style.display = btn.dataset.name.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    function selectStudent(btn) {
        document.querySelectorAll('.student-item').forEach(function (b) { b.style.background = ''; });
        btn.style.background = '#eff6ff';
        document.getElementById('selectedStudentId').value = btn.dataset.id;
        document.getElementById('selectedStudentName').textContent = 'Selected: ' + btn.dataset.name;
    }
</script>
@endpush

@endsection
