<?php

use App\Enums\UserRole;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProposalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    $proposals = $user->role === UserRole::Mahasiswa
        ? $user->proposals()->latest()->latest('id')->get()
        : collect();

    return view('dashboard', ['proposals' => $proposals]);
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'cek.peran:mahasiswa'])->group(function () {
    Route::resource('proposals', ProposalController::class)
        ->only(['index', 'create', 'edit', 'update']);

    Route::post('proposals', [ProposalController::class, 'store'])
        ->middleware('throttle:proposals')
        ->name('proposals.store');

    Route::patch('proposals/{proposal}/submit', [ProposalController::class, 'submit'])
        ->name('proposals.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'cek.peran'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    Route::get('proposals', [AdminController::class, 'proposals'])->name('proposals.index');
    Route::get('proposals/{proposal}', [AdminController::class, 'showProposal'])->name('proposals.show');
    Route::patch('proposals/{proposal}/review', [AdminController::class, 'reviewProposal'])->name('proposals.review');
});

require __DIR__.'/auth.php';
