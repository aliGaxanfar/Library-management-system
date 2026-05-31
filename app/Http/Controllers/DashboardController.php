<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        Loan::where('status', 'ACTIVE')
            ->where('dueDate', '<', Carbon::today())
            ->update(['status' => 'OVERDUE']);

        $stats = [
            'totalBooks'    => Book::count(),
            'totalMembers'  => Member::count(),
            'activeLoans'   => Loan::where('status', 'ACTIVE')->count(),
            'overdueLoans'  => Loan::where('status', 'OVERDUE')->count(),
            'pendingFines'  => Fine::where('status', 'PENDING')->sum('amount'),
            'pendingReserv' => Reservation::where('status', 'PENDING')->count(),
        ];

        $recentLoans = Loan::with(['member.user', 'book'])->latest()->take(5)->get();
        $recentAudit = AuditLog::with('user')->latest('performedAt')->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentLoans', 'recentAudit'));
    }

    public function librarian()
    {
        Loan::where('status', 'ACTIVE')
            ->where('dueDate', '<', Carbon::today())
            ->update(['status' => 'OVERDUE']);

        $stats = [
            'activeLoans'   => Loan::where('status', 'ACTIVE')->count(),
            'overdueLoans'  => Loan::where('status', 'OVERDUE')->count(),
            'availBooks'    => Book::where('availableCopies', '>', 0)->count(),
            'pendingReserv' => Reservation::where('status', 'PENDING')->count(),
        ];

        $overdueLoans = Loan::with(['member.user', 'book'])
            ->where('status', 'OVERDUE')->take(10)->get();

        return view('librarian.dashboard', compact('stats', 'overdueLoans'));
    }

    public function member()
    {
        $member        = Auth::user()->member;
        $activeLoans   = $member->activeLoans()->with('book')->get();
        $pendingFines  = $member->pendingFines()->with('loan.book')->get();
        $reservations  = $member->reservations()
            ->with('book')->whereIn('status', ['PENDING', 'CONFIRMED'])->get();
        $notifications = $member->notifications()
            ->where('isRead', false)->latest()->take(5)->get();

        return view('member.dashboard', compact(
            'member', 'activeLoans', 'pendingFines', 'reservations', 'notifications'
        ));
    }
}
