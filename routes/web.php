<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// ── Auth ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'ADMIN')     return redirect()->route('admin.dashboard');
        if ($role === 'LIBRARIAN') return redirect()->route('librarian.dashboard');
        return redirect()->route('member.dashboard');
    }
    return redirect()->route('login');
});
Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

// ── Admin ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:ADMIN'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                         [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/members',                           [MemberController::class, 'index'])->name('members');
    Route::get('/members/{member}',                  [MemberController::class, 'show'])->name('members.show');
    Route::post('/members/{member}/toggle',          [MemberController::class, 'toggleActive'])->name('members.toggle');
    Route::get('/audit-log', fn() => view('admin.audit_log', [
        'logs' => \App\Models\AuditLog::with('user')->latest('performedAt')->paginate(30),
    ]))->name('audit_log');
    Route::get('/reports', fn() => view('admin.reports', [
        'reports' => \App\Models\Report::with('admin.user')->latest('generatedAt')->paginate(20),
    ]))->name('reports');
});

// ── Librarian + Admin shared ─────────────────────────────────────────
Route::middleware(['auth', 'role:ADMIN,LIBRARIAN'])->prefix('librarian')->name('librarian.')->group(function () {
    Route::get('/dashboard',                              [DashboardController::class, 'librarian'])->name('dashboard');

    // Loans
    Route::get('/loans',                                  [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create',                           [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans',                                 [LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{loan}',                           [LoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{loan}/return',                   [LoanController::class, 'returnBook'])->name('loans.return');
    Route::post('/loans/{loan}/renew',                    [LoanController::class, 'renew'])->name('loans.renew');

    // Reservations
    Route::get('/reservations',                           [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations/{reservation}/confirm',    [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::post('/reservations/{reservation}/cancel',     [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Fines
    Route::get('/fines',                                  [FineController::class, 'index'])->name('fines.index');
    Route::post('/fines/{fine}/paid',                     [FineController::class, 'markPaid'])->name('fines.paid');
    Route::post('/fines/{fine}/waive',                    [FineController::class, 'waive'])->name('fines.waive');
});

// ── Books (Librarian + Admin manage; anyone authenticated can view) ──
Route::middleware(['auth', 'role:ADMIN,LIBRARIAN'])->group(function () {
    Route::resource('books', BookController::class)->except(['index', 'show']);
});
Route::middleware('auth')->group(function () {
    Route::get('/books',        [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
});

// ── Member ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:MEMBER'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard',    [DashboardController::class, 'member'])->name('dashboard');

    Route::get('/loans', fn() => view('member.loans', [
        'loans' => auth()->user()->member->loans()->with('book')->latest()->paginate(10),
    ]))->name('loans');

    Route::get('/fines', fn() => view('member.fines', [
        'fines' => auth()->user()->member->fines()->with('loan.book')->latest()->paginate(10),
    ]))->name('fines');

    Route::get('/reservations',                           [ReservationController::class, 'index'])->name('reservations');
    Route::post('/reservations',                          [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservations/{reservation}/cancel',     [ReservationController::class, 'cancel'])->name('reservations.cancel');

    Route::get('/catalogue',    [BookController::class, 'index'])->name('catalogue');

    Route::get('/notifications', fn() => view('member.notifications', [
        'notifications' => auth()->user()->member->notifications()->latest()->paginate(20),
    ]))->name('notifications');
});
