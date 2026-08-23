<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\ElectionSetting;
use App\Models\Position;
use App\Models\Vote;
use App\Models\VotesLedger;
use App\Models\VotingToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoterController extends Controller
{
    // ─────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────

    private function electionStatus(): string
    {
        return ElectionSetting::get('status', 'closed');
    }

    private function tokenFromSession(): ?VotingToken
    {
        $id = session('voter_token_id');
        if (!$id) return null;
        return VotingToken::find($id);
    }

    /** Build a SHA-256 block hash from its components. */
    private function blockHash(int $index, string $timestamp, string $prevHash, string $payloadHash): string
    {
        return hash('sha256', $index . $timestamp . $prevHash . $payloadHash);
    }

    /** Hash of the vote payload: sorted position→candidate pairs + token ref. Preserves secrecy. */
    private function votePayloadHash(array $selections, string $tokenHash): string
    {
        ksort($selections); // sort by position_id for determinism
        $raw = json_encode($selections) . '|' . $tokenHash;
        return hash('sha256', $raw);
    }

    /** Generate a secure alphanumeric receipt code. */
    private function makeReceiptCode(int $length = 12): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code  = '';
        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[ord($bytes[$i]) % strlen($chars)];
        }
        return $code;
    }

    // ─────────────────────────────────────────
    // LOGIN
    // ─────────────────────────────────────────

    public function showLogin()
    {
        // If already in voter session, redirect to dashboard
        if ($token = $this->tokenFromSession()) {
            return redirect()->route('voter.dashboard');
        }

        $status = $this->electionStatus();
        return view('voter.login', compact('status'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string', 'size:8'],
        ], [
            'token.size' => 'Token must be exactly 8 characters.',
        ]);

        $code  = strtoupper(trim($request->token));
        $token = VotingToken::where('code', $code)->first();

        if (!$token) {
            return back()->withInput()->with('error', 'Invalid token. Please check your token and try again.');
        }

        if ($token->used) {
            return back()->withInput()->with('error_used', true);
        }

        $status = $this->electionStatus();
        if ($status !== 'open') {
            return back()->withInput()->with('error', 'The election is not currently open.');
        }

        // Valid, unused token — start voter session
        session(['voter_token_id' => $token->id]);
        return redirect()->route('voter.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('voter_token_id');
        return redirect()->route('voter.login');
    }

    // ─────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────

    public function dashboard()
    {
        $token = $this->tokenFromSession();
        if (!$token) return redirect()->route('voter.login');

        return view('voter.dashboard', compact('token'));
    }

    // ─────────────────────────────────────────
    // VOTING FLOW — STEPPER
    // ─────────────────────────────────────────

    public function vote(Request $request)
    {
        $token = $this->tokenFromSession();
        if (!$token) return redirect()->route('voter.login');
        if ($token->used)  return redirect()->route('voter.dashboard');
        if ($this->electionStatus() !== 'open') return redirect()->route('voter.dashboard');

        $positions = Position::orderBy('sort_order')->with('candidates')->get();
        if ($positions->isEmpty()) {
            return redirect()->route('voter.dashboard')->with('info', 'No positions have been configured yet.');
        }

        // Step index from query string, default 0
        $step = max(0, (int) $request->get('step', 0));
        $step = min($step, $positions->count() - 1);

        $position = $positions[$step];
        $total    = $positions->count();

        // Retrieve in-progress selections from session
        $selections = session('voter_selections', []);

        return view('voter.vote', compact('position', 'step', 'total', 'positions', 'selections', 'token'));
    }

    public function saveStep(Request $request)
    {
        $token = $this->tokenFromSession();
        if (!$token) return redirect()->route('voter.login');
        if ($token->used)  return redirect()->route('voter.dashboard');

        $step  = (int) $request->input('step', 0);
        $posId = (int) $request->input('position_id');

        $request->validate([
            'position_id' => ['required', 'integer', 'exists:positions,id'],
            // candidate_id is optional (null = abstain)
        ]);

        $selections = session('voter_selections', []);

        // candidate_id = null means Abstain
        $selections[$posId] = $request->input('candidate_id'); // null or int

        session(['voter_selections' => $selections]);

        $totalPositions = Position::count();

        // If this was the last step, go to review
        if ($step >= $totalPositions - 1) {
            return redirect()->route('voter.review');
        }

        return redirect()->route('voter.vote', ['step' => $step + 1]);
    }

    // ─────────────────────────────────────────
    // REVIEW
    // ─────────────────────────────────────────

    public function review()
    {
        $token = $this->tokenFromSession();
        if (!$token) return redirect()->route('voter.login');
        if ($token->used)  return redirect()->route('voter.dashboard');

        $selections = session('voter_selections', []);
        $positions  = Position::orderBy('sort_order')->with('candidates')->get();

        // Ensure all positions have a selection (redirect back to first incomplete)
        foreach ($positions as $i => $pos) {
            if (!array_key_exists($pos->id, $selections)) {
                return redirect()->route('voter.vote', ['step' => $i]);
            }
        }

        // Resolve candidate names for display
        $summary = $positions->map(function ($pos) use ($selections) {
            $candidateId = $selections[$pos->id] ?? null;
            $candidate   = $candidateId ? Candidate::find($candidateId) : null;
            return [
                'position'    => $pos,
                'candidate'   => $candidate,
                'abstained'   => $candidateId === null,
            ];
        });

        return view('voter.review', compact('summary', 'token'));
    }

    // ─────────────────────────────────────────
    // SUBMIT
    // ─────────────────────────────────────────

    public function submit(Request $request)
    {
        $token = $this->tokenFromSession();
        if (!$token) return redirect()->route('voter.login');
        if ($token->used)  return redirect()->route('voter.dashboard');
        if ($this->electionStatus() !== 'open') return redirect()->route('voter.dashboard');

        $selections = session('voter_selections', []);
        $positions  = Position::orderBy('sort_order')->pluck('id');

        // Guard: must have selections for all positions
        foreach ($positions as $posId) {
            if (!array_key_exists($posId, $selections)) {
                return redirect()->route('voter.vote', ['step' => 0]);
            }
        }

        DB::transaction(function () use ($token, $selections) {
            // 1. Record votes (and abstains)
            foreach ($selections as $posId => $candidateId) {
                Vote::create([
                    'position_id'  => $posId,
                    'candidate_id' => $candidateId, // null = abstain
                    'student_id'   => $token->student_id,
                    'token_id'     => $token->id,
                ]);
            }

            // 2. Mark token used
            $token->update([
                'used'    => true,
                'used_at' => now(),
            ]);

            // 3. Mark student voted
            if ($token->student) {
                $token->student->update([
                    'has_voted' => true,
                    'voted_at'  => now(),
                ]);
            }

            // 4. Append to votes_ledger (blockchain-style)
            $tokenHash    = hash('sha256', $token->code); // one-way: can verify but not reverse
            $prevBlock    = VotesLedger::orderByDesc('block_index')->first();
            $prevHash     = $prevBlock ? $prevBlock->block_hash : str_repeat('0', 64);
            $blockIndex   = $prevBlock ? $prevBlock->block_index + 1 : 1;
            $ts           = now()->toISOString();
            $payloadHash  = $this->votePayloadHash($selections, $tokenHash);
            $blockHash    = $this->blockHash($blockIndex, $ts, $prevHash, $payloadHash);
            $receiptCode  = $this->makeReceiptCode();

            VotesLedger::create([
                'block_index'       => $blockIndex,
                'block_timestamp'   => now(),
                'previous_hash'     => $prevHash,
                'vote_payload_hash' => $payloadHash,
                'block_hash'        => $blockHash,
                'token_hash'        => $tokenHash,
                'receipt_code'      => $receiptCode,
            ]);

            // Store receipt info in session for the receipt page
            session([
                'voter_receipt_code'  => $receiptCode,
                'voter_receipt_block' => $blockIndex,
            ]);
        });

        // Clear voting progress
        session()->forget('voter_selections');

        return redirect()->route('voter.receipt');
    }

    // ─────────────────────────────────────────
    // RECEIPT
    // ─────────────────────────────────────────

    public function receipt()
    {
        $token = $this->tokenFromSession();
        if (!$token) return redirect()->route('voter.login');

        $receiptCode  = session('voter_receipt_code');
        $blockIndex   = session('voter_receipt_block');

        // If no receipt in session but token is used, show a generic confirmation
        if (!$receiptCode && $token->used) {
            $receiptCode = '—';
            $blockIndex  = null;
        } elseif (!$receiptCode) {
            return redirect()->route('voter.dashboard');
        }

        return view('voter.receipt', compact('token', 'receiptCode', 'blockIndex'));
    }
}
