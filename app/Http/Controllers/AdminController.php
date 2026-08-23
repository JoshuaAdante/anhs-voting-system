<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\ElectionSetting;
use App\Models\Position;
use App\Models\Student;
use App\Models\Vote;
use App\Models\VotesLedger;
use App\Models\VotingToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ─────────────────────────────────────────
    // AUTH
    // ─────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Incorrect email or password.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    // ─────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────

    public function dashboard()
    {
        $studentCount = Student::count();
        $voted        = Student::where('has_voted', true)->count();
        $candidates   = Candidate::count();
        $tokens       = VotingToken::count();
        $usedTokens   = VotingToken::where('used', true)->count();
        $turnout      = $studentCount > 0 ? round($voted / $studentCount * 100) : 0;

        $stats = [
            'students'   => $studentCount,
            'voted'      => $voted,
            'turnout'    => $turnout,
            'candidates' => $candidates,
            'tokens'     => $tokens,
            'usedTokens' => $usedTokens,
        ];

        $positions = Position::orderBy('sort_order')->with('candidates')->get();
        $results = $positions->map(function ($p) {
            $rows  = $p->candidates->map(fn($c) => [
                'name'  => $c->full_name,
                'votes' => Vote::where('candidate_id', $c->id)->count(),
            ])->sortByDesc('votes')->values();
            return [
                'name'       => $p->name,
                'total'      => $rows->sum('votes'),
                'candidates' => $rows,
            ];
        });

        return view('admin.dashboard', compact('stats', 'results'));
    }

    // ─────────────────────────────────────────
    // STUDENTS
    // ─────────────────────────────────────────

    public function students(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($b) use ($q) {
                $b->where('last_name', 'like', "%$q%")
                  ->orWhere('first_name', 'like', "%$q%")
                  ->orWhere('lrn', 'like', "%$q%");
            });
        }

        if ($request->filled('grade'))   $query->where('grade', $request->grade);
        if ($request->filled('section')) $query->where('section', $request->section);
        if ($request->status === 'voted') $query->where('has_voted', true);
        if ($request->status === 'not')   $query->where('has_voted', false);

        $students = $query->orderBy('last_name')->paginate(50);
        $sections = Student::distinct()->orderBy('section')->pluck('section');

        return view('admin.students', compact('students', 'sections'));
    }

    public function storeStudent(Request $request)
    {
        $data = $request->validate([
            'lrn'        => ['required', 'string', 'unique:students,lrn'],
            'last_name'  => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'given_name' => ['nullable', 'string'],
            'grade'      => ['required', 'string'],
            'section'    => ['required', 'string'],
            'sex'        => ['required', 'in:Male,Female'],
        ]);

        Student::create($data);
        return back()->with('success', 'Student added successfully.');
    }

    public function deleteStudent(Student $student)
    {
        $student->delete();
        return back()->with('success', 'Student deleted.');
    }

    public function deleteAllStudents()
    {
        Student::query()->delete();
        return back()->with('success', 'All students deleted.');
    }

    public function resetStudents()
    {
        Student::query()->update(['has_voted' => false, 'voted_at' => null]);
        return back()->with('success', 'All students reset to not voted.');
    }

    public function uploadStudents(Request $request)
    {
        $request->validate(['csv' => ['required', 'file', 'mimes:csv,txt']]);

        $lines = explode("\n", file_get_contents($request->file('csv')->getRealPath()));
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $cols = array_map('trim', str_getcsv($line));
            if (count($cols) < 3) continue;

            [$lrn, $last_name, $first_name] = $cols;
            if (strtolower($lrn) === 'lrn' || empty($lrn)) continue;

            Student::updateOrCreate(['lrn' => $lrn], [
                'last_name'  => $last_name,
                'first_name' => $first_name,
                'given_name' => $cols[3] ?? null,
                'grade'      => $cols[4] ?? 'Grade 7',
                'section'    => $cols[5] ?? 'N/A',
                'sex'        => $cols[6] ?? 'Male',
            ]);
            $count++;
        }

        return back()->with('success', "$count student(s) uploaded.");
    }

    // ─────────────────────────────────────────
    // CANDIDATES & POSITIONS
    // ─────────────────────────────────────────

    public function candidates()
    {
        $positions = Position::orderBy('sort_order')
            ->withCount('candidates')
            ->with('candidates')
            ->get();

        $students = Student::orderBy('last_name')->orderBy('first_name')->get();

        return view('admin.candidates', compact('positions', 'students'));
    }

    public function storeCandidate(Request $request)
    {
        $data = $request->validate([
            'full_name'   => ['required', 'string'],
            'position_id' => ['required', 'exists:positions,id'],
            'partylist'   => ['nullable', 'string'],
            'grade'       => ['nullable', 'string'],
            'section'     => ['nullable', 'string'],
            'photo_url'   => ['nullable', 'url'],
        ]);

        Candidate::create($data);
        return back()->with('success', 'Candidate added.');
    }

    public function updateCandidate(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'full_name'   => ['required', 'string'],
            'position_id' => ['required', 'exists:positions,id'],
            'partylist'   => ['nullable', 'string'],
            'grade'       => ['nullable', 'string'],
            'section'     => ['nullable', 'string'],
            'photo_url'   => ['nullable', 'url'],
        ]);

        $candidate->update($data);
        return back()->with('success', 'Candidate updated.');
    }

    public function deleteCandidate(Candidate $candidate)
    {
        $candidate->delete();
        return back()->with('success', 'Candidate deleted.');
    }

    public function storePosition(Request $request)
    {
        $request->validate(['name' => ['required', 'string']]);
        Position::create([
            'name'       => $request->name,
            'sort_order' => Position::count() + 1,
        ]);
        return back()->with('success', 'Position added.');
    }

    public function deletePosition(Position $position)
    {
        $position->delete();
        return back()->with('success', 'Position deleted.');
    }

    // ─────────────────────────────────────────
    // SYSTEM / TOKENS
    // ─────────────────────────────────────────

    public function system()
    {
        $tokens      = VotingToken::with('student')->latest()->paginate(50);
        $unusedCount = VotingToken::where('used', false)->count();
        $students    = Student::orderBy('last_name')->get();

        // Build grade -> sorted sections map for the token generator
        $sectionsByGrade = Student::selectRaw('grade, section')
            ->distinct()
            ->orderBy('grade')->orderBy('section')
            ->get()
            ->groupBy('grade')
            ->map(fn($rows) => $rows->pluck('section')->filter()->unique()->sort()->values());

        return view('admin.system', compact('tokens', 'unusedCount', 'students', 'sectionsByGrade'));
    }

    public function printTokens(Request $request)
    {
        $query = VotingToken::with('student');

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        // Filter by section via the related student
        if ($request->filled('section')) {
            $query->whereHas('student', fn($q) => $q->where('section', $request->section));
        }

        $tokens = $query->get()->sortBy(function ($t) {
            return ($t->student?->last_name ?? 'ZZZZZ') . ($t->student?->first_name ?? '');
        })->values();

        // For the filter dropdowns in the print view
        $sectionsByGrade = Student::selectRaw('grade, section')
            ->distinct()
            ->orderBy('grade')->orderBy('section')
            ->get()
            ->groupBy('grade')
            ->map(fn($rows) => $rows->pluck('section')->filter()->unique()->sort()->values());

        $selectedGrade   = $request->grade;
        $selectedSection = $request->section;

        return view('admin.tokens-print', compact('tokens', 'sectionsByGrade', 'selectedGrade', 'selectedSection'));
    }

    public function generateTokens(Request $request)
    {
        $request->validate(['mode' => ['required', 'in:name,grade']]);
        $generated = 0;

        if ($request->mode === 'name') {
            $request->validate(['student_id' => ['required', 'exists:students,id']]);
            $student = Student::findOrFail($request->student_id);

            VotingToken::create([
                'code'       => $this->makeCode(),
                'student_id' => $student->id,
                'grade'      => $student->grade,
            ]);
            $generated = 1;
        } else {
            $request->validate(['grade' => ['required', 'string']]);

            $existing = VotingToken::where('used', false)
                ->whereNotNull('student_id')
                ->pluck('student_id')
                ->toArray();

            $query = Student::where('grade', $request->grade);
            if ($request->filled('section')) {
                $query->where('section', $request->section);
            }
            $targets = $query->whereNotIn('id', $existing)->get();

            foreach ($targets as $s) {
                VotingToken::create([
                    'code'       => $this->makeCode(),
                    'student_id' => $s->id,
                    'grade'      => $s->grade,
                ]);
                $generated++;
            }
        }

        return back()->with('success', "$generated token(s) generated.");
    }

    public function deleteToken(VotingToken $votingToken)
    {
        $votingToken->delete();
        return back()->with('success', 'Token deleted.');
    }

    public function deleteUnusedTokens()
    {
        $count = VotingToken::where('used', false)->count();
        VotingToken::where('used', false)->delete();
        return back()->with('success', "$count unused token(s) deleted.");
    }

    // ─────────────────────────────────────────
    // LEDGER / BLOCKCHAIN INTEGRITY
    // ─────────────────────────────────────────

    public function ledger()
    {
        $blocks       = VotesLedger::orderBy('block_index')->get();
        $electionStatus = ElectionSetting::get('status', 'open');
        $electionTitle  = ElectionSetting::get('title', 'SSLG Election');
        return view('admin.ledger', compact('blocks', 'electionStatus', 'electionTitle'));
    }

    public function verifyLedger()
    {
        $blocks   = VotesLedger::orderBy('block_index')->get();
        $broken   = [];
        $prevHash = str_repeat('0', 64);

        foreach ($blocks as $block) {
            $expected = hash('sha256',
                $block->block_index .
                $block->block_timestamp->toISOString() .
                $prevHash .
                $block->vote_payload_hash
            );

            if ($expected !== $block->block_hash || $block->previous_hash !== $prevHash) {
                $broken[] = $block->block_index;
            }

            $prevHash = $block->block_hash;
        }

        if (empty($broken)) {
            return back()->with('ledger_ok', 'Chain is intact. All ' . $blocks->count() . ' block(s) verified successfully.');
        }

        return back()->with('ledger_broken', 'Integrity check failed! Broken at block(s): ' . implode(', ', $broken));
    }

    public function updateElectionStatus(Request $request)
    {
        $request->validate(['status' => ['required', 'in:open,closed,pending']]);
        ElectionSetting::set('status', $request->status);
        return back()->with('success', 'Election status updated to "' . $request->status . '".');
    }

    // ─────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────

    private function makeCode(int $length = 8): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code  = '';
        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[ord($bytes[$i]) % strlen($chars)];
        }
        return $code;
    }
}
