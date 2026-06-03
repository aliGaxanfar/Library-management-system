<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Librarian;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with(['member.user', 'book', 'librarian.user']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->input('search')) {
            $query->whereHas('member.user', fn($q) => $q->where('fullName', 'like', "%$search%"))
                  ->orWhereHas('book', fn($q) => $q->where('title', 'like', "%$search%"));
        }

        // Auto-mark overdue
        Loan::where('status', 'ACTIVE')
            ->where('dueDate', '<', Carbon::today())
            ->update(['status' => 'OVERDUE']);

        $loans = $query->latest('issueDate')->paginate(20)->withQueryString();
        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $members = Member::with('user')->get();
        $books   = Book::where('availableCopies', '>', 0)->get();
        return view('loans.create', compact('members', 'books'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'memberId' => 'required|exists:members,id',
            'bookId'   => 'required|exists:books,bookId',
        ]);

        $book = Book::findOrFail($data['bookId']);
        if ($book->availableCopies < 1) {
            return back()->withErrors(['bookId' => 'No copies available.']);
        }

        // Admin may not have a librarian record, use first available librarian
        $librarian = Auth::user()->librarian ?? Librarian::first();

        if (!$librarian) {
            return back()->withErrors(['error' => 'No librarian found in the system. Please create a librarian account first.']);
        }

        $loan = Loan::create([
            'memberId'     => $data['memberId'],
            'bookId'       => $data['bookId'],
            'librarianId'  => $librarian->id,
            'issueDate'    => Carbon::today(),
            'dueDate'      => Carbon::today()->addDays(14),
            'status'       => 'ACTIVE',
            'renewalCount' => 0,
        ]);

        $book->decrement('availableCopies');
        if ($book->availableCopies === 0) {
            $book->update(['status' => 'BORROWED']);
        }

        AuditLog::record(Auth::id(), 'CREATE', 'loans', $loan->loanId, $loan->toArray(), request()->ip());

        return redirect()->route('librarian.loans.index')
            ->with('success', 'Loan issued successfully. Due: ' . $loan->dueDate->format('d M Y'));
    }

    public function returnBook(Request $request, Loan $loan)
    {
        if ($loan->status === 'RETURNED') {
            return back()->withErrors(['loan' => 'This loan is already returned.']);
        }

        $loan->returnDate = Carbon::today();
        $loan->status     = 'RETURNED';
        $loan->save();

        $book = $loan->book;
        $book->increment('availableCopies');
        if ($book->availableCopies > 0) {
            $book->update(['status' => 'AVAILABLE']);
        }

        // Generate fine if overdue
        $daysOverdue = Carbon::today()->diffInDays($loan->dueDate, false);
        if ($daysOverdue < 0) {
            $days   = abs($daysOverdue);
            $amount = Fine::calculateAmount($days);

            Fine::create([
                'loanId'     => $loan->loanId,
                'memberId'   => $loan->memberId,
                'amount'     => $amount,
                'reason'     => "Book returned {$days} days late",
                'issuedDate' => Carbon::today(),
                'status'     => 'PENDING',
            ]);

            $loan->member->increment('totalFinesDue', $amount);

            Notification::create([
                'memberId'    => $loan->memberId,
                'type'        => 'FINE',
                'title'       => 'Fine Issued',
                'message'     => "A fine of \${$amount} has been issued for late return of '{$book->title}'.",
                'channel'     => 'IN_APP',
                'deliveredAt' => now(),
            ]);
        }

        AuditLog::record(Auth::id(), 'UPDATE', 'loans', $loan->loanId,
            ['action' => 'return', 'returnDate' => $loan->returnDate], request()->ip());

        return redirect()->route('librarian.loans.index')
            ->with('success', 'Book returned successfully.');
    }

    public function renew(Loan $loan)
    {
        if (!$loan->renew()) {
            return back()->withErrors(['loan' => 'Maximum renewals (2) reached.']);
        }

        AuditLog::record(Auth::id(), 'UPDATE', 'loans', $loan->loanId,
            ['action' => 'renew', 'newDueDate' => $loan->dueDate], request()->ip());

        return back()->with('success', 'Loan renewed. New due date: ' . $loan->dueDate->format('d M Y'));
    }

    public function show(Loan $loan)
    {
        $loan->load(['member.user', 'book', 'librarian.user', 'fine']);
        return view('loans.show', compact('loan'));
    }
}