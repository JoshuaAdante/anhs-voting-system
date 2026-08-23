@extends('admin.layout')
@section('content')

<div class="page-header">
    <h2>Student Management</h2>
    <p>Add, filter and manage registered learners</p>
</div>

{{-- ── Action Bar ── --}}
<div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:8px;">
    <button class="btn btn-primary" onclick="openModal('addModal')">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Add Student
    </button>

    <form method="POST" action="{{ route('admin.students.upload') }}"
          enctype="multipart/form-data" id="uploadForm">
        @csrf
        <input type="file" name="csv" id="csvFile" accept=".csv,text/csv"
               style="display:none" onchange="document.getElementById('uploadForm').submit()">
        <button type="button" class="btn btn-secondary"
                onclick="document.getElementById('csvFile').click()">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Upload CSV
        </button>
    </form>

    <form method="POST" action="{{ route('admin.students.reset') }}"
          onsubmit="return confirm('Reset voted status of ALL students?')">
        @csrf
        <button type="submit" class="btn btn-ghost">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Reset All
        </button>
    </form>

    <form method="POST" action="{{ route('admin.students.deleteAll') }}"
          onsubmit="return confirm('DELETE ALL STUDENTS? This cannot be undone.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Delete All
        </button>
    </form>
</div>

<p style="font-size:11px;color:#94a3b8;margin-bottom:16px;">
    CSV format: LRN, Last name, First name, Given name, Grade, Section, Sex
</p>

{{-- ── Filters ── --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.students') }}" id="filterForm"
              style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input class="form-control" type="text" name="search" id="searchInput"
                       placeholder="Search name or LRN"
                       value="{{ request('search') }}"
                       autocomplete="off">
            </div>
            <select class="form-control" name="grade" onchange="this.form.submit()">
                <option value="">All grades</option>
                @foreach (['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $g)
                    <option value="{{ $g }}" {{ request('grade') == $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
            </select>
            <select class="form-control" name="section" onchange="this.form.submit()">
                <option value="">All sections</option>
                @foreach ($sections as $s)
                    <option value="{{ $s }}" {{ request('section') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <select class="form-control" name="status" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="voted"  {{ request('status') == 'voted'  ? 'selected' : '' }}>Voted</option>
                <option value="not"    {{ request('status') == 'not'    ? 'selected' : '' }}>Not voted</option>
            </select>
        </form>
    </div>
</div>

{{-- ── Table ── --}}
<div class="card">
    <div class="card-header">
        <h3>
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Students
        </h3>
        <span class="badge badge-blue">{{ $students->total() }} total</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Section</th>
                    <th>Sex</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $s)
                <tr>
                    <td class="mono">{{ $s->lrn }}</td>
                    <td style="font-weight:600;">{{ $s->last_name }}, {{ $s->first_name }}{{ $s->given_name ? ' '.$s->given_name : '' }}</td>
                    <td>{{ $s->grade }}</td>
                    <td>{{ $s->section }}</td>
                    <td>{{ $s->sex }}</td>
                    <td>
                        @if ($s->has_voted)
                            <span class="badge badge-green">Voted</span>
                        @else
                            <span class="badge badge-gray">Not voted</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.students.delete', $s->id) }}"
                              onsubmit="return confirm('Delete this student?')" style="display:inline;">
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
                    <td colspan="7" style="text-align:center;padding:32px;color:#94a3b8;">
                        No students found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($students->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f1f5f9;">
        {{ $students->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ══ Add Student Modal ══ --}}
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add Student</h3>
            <button class="modal-close" onclick="closeModal('addModal')">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.students.store') }}">
            @csrf
            <div class="col-span-2" style="margin-bottom:14px;">
                <div class="form-group">
                    <label>LRN</label>
                    <input class="form-control" type="text" name="lrn" required>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Last Name</label>
                    <input class="form-control" type="text" name="last_name" required>
                </div>
                <div class="form-group">
                    <label>First Name</label>
                    <input class="form-control" type="text" name="first_name" required>
                </div>
                <div class="form-group col-span-2">
                    <label>Given / Middle Name <span style="color:#94a3b8;">(optional)</span></label>
                    <input class="form-control" type="text" name="given_name">
                </div>
                <div class="form-group">
                    <label>Grade</label>
                    <select class="form-control" name="grade">
                        @foreach (['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $g)
                            <option>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Section</label>
                    <input class="form-control" type="text" name="section" required>
                </div>
                <div class="form-group">
                    <label>Sex</label>
                    <select class="form-control" name="sex">
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Student</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id)  { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    @if ($errors->any())
        openModal('addModal');
    @endif

    // Live search — debounced 350ms, auto-submits the filter form
    (function () {
        var input = document.getElementById('searchInput');
        var form  = document.getElementById('filterForm');
        var timer = null;
        if (!input) return;

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { form.submit(); }, 350);
        });
    })();
</script>
@endpush

@endsection
