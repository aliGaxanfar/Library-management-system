<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Fine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FineController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isMember()) {
            $fines = $user->member->fines()->with('loan.book')->latest('issuedDate')->paginate(10);
        } else {
            $fines = Fine::with(['member.user', 'loan.book'])->latest('issuedDate')->paginate(20);
        }

        return view('fines.index', compact('fines'));
    }

    public function markPaid(Fine $fine)
    {
        $fine->update(['status' => 'PAID', 'paidDate' => Carbon::today()]);
        $fine->member->decrement('totalFinesDue', $fine->amount);

        AuditLog::record(
            Auth::id(),
            'UPDATE',
            'fines',
            $fine->fineId,
            ['action' => 'markPaid'],
            request()->ip()
        );

        return back()->with('success', 'Fine marked as paid.');
    }

    public function waive(Fine $fine)
    {
        $fine->update(['status' => 'WAIVED']);
        $fine->member->decrement('totalFinesDue', $fine->amount);

        AuditLog::record(
            Auth::id(),
            'UPDATE',
            'fines',
            $fine->fineId,
            ['action' => 'waive'],
            request()->ip()
        );

        return back()->with('success', 'Fine waived.');
    }
}
