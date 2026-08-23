<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VoterController;

// Root redirect
Route::get('/', function () {
    return redirect()->route('voter.login');
})->name('home');

// Safety alias — Laravel auth middleware redirects to 'login' by default.
// bootstrap/app.php overrides this, but this keeps it working as a fallback.
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Admin panel
Route::prefix('admin')->group(function () {
    // Public auth routes
    Route::get('/login',   [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login',  [AdminController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    // Protected routes
    Route::middleware('auth')->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Students
        Route::get('/students',              [AdminController::class, 'students'])->name('admin.students');
        Route::post('/students',             [AdminController::class, 'storeStudent'])->name('admin.students.store');
        Route::post('/students/upload',      [AdminController::class, 'uploadStudents'])->name('admin.students.upload');
        Route::post('/students/reset',       [AdminController::class, 'resetStudents'])->name('admin.students.reset');
        Route::delete('/students',           [AdminController::class, 'deleteAllStudents'])->name('admin.students.deleteAll');
        Route::delete('/students/{student}', [AdminController::class, 'deleteStudent'])->name('admin.students.delete');

        // Candidates
        Route::get('/candidates',                [AdminController::class, 'candidates'])->name('admin.candidates');
        Route::post('/candidates',               [AdminController::class, 'storeCandidate'])->name('admin.candidates.store');
        Route::put('/candidates/{candidate}',    [AdminController::class, 'updateCandidate'])->name('admin.candidates.update');
        Route::delete('/candidates/{candidate}', [AdminController::class, 'deleteCandidate'])->name('admin.candidates.delete');

        // Positions
        Route::post('/positions',              [AdminController::class, 'storePosition'])->name('admin.positions.store');
        Route::delete('/positions/{position}', [AdminController::class, 'deletePosition'])->name('admin.positions.delete');

        // System / Tokens
        Route::get('/system',                  [AdminController::class, 'system'])->name('admin.system');
        Route::get('/tokens/print',            [AdminController::class, 'printTokens'])->name('admin.tokens.print');
        Route::post('/tokens/generate',        [AdminController::class, 'generateTokens'])->name('admin.tokens.generate');
        Route::delete('/tokens/unused',        [AdminController::class, 'deleteUnusedTokens'])->name('admin.tokens.deleteUnused');
        Route::delete('/tokens/{votingToken}', [AdminController::class, 'deleteToken'])->name('admin.tokens.delete');

        // Ledger integrity
        Route::get('/ledger',  [AdminController::class, 'ledger'])->name('admin.ledger');
        Route::post('/ledger/verify', [AdminController::class, 'verifyLedger'])->name('admin.ledger.verify');

        // Election settings
        Route::post('/settings/status', [AdminController::class, 'updateElectionStatus'])->name('admin.settings.status');

    });

});

// ─── Voter portal ───
Route::prefix('vote')->name('voter.')->group(function () {
    Route::get('/',        [VoterController::class, 'showLogin'])->name('login');
    Route::post('/',       [VoterController::class, 'login'])->name('login.post');
    Route::post('/logout', [VoterController::class, 'logout'])->name('logout');

    Route::get('/dashboard',  [VoterController::class, 'dashboard'])->name('dashboard');
    Route::get('/ballot',     [VoterController::class, 'vote'])->name('vote');
    Route::post('/ballot',    [VoterController::class, 'saveStep'])->name('step.save');
    Route::get('/review',     [VoterController::class, 'review'])->name('review');
    Route::post('/submit',    [VoterController::class, 'submit'])->name('submit');
    Route::get('/receipt',    [VoterController::class, 'receipt'])->name('receipt');
});
