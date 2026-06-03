<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Notification;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isMember()) {
            $reservations = $user->member->reservations()->with('book')->latest()->paginate(10);
        } else {
            $reservations = Reservation::with(['member.user', 'book'])->latest()->paginate(20);
        }

        return view('reservations.index', compact('reservations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bookId' => 'required|exists:books,bookId',
        ]);

        $member = Auth::user()->member;
        $book   = Book::findOrFail($data['bookId']);

        // Check not already reserved by this member
        $exists = Reservation::where('memberId', $member->id)
            ->where('bookId', $book->bookId)
            ->whereIn('status', ['PENDING', 'CONFIRMED'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['bookId' => 'You already have a reservation for this book.']);
        }

        $queuePos = Reservation::where('bookId', $book->bookId)
            ->whereIn('status', ['PENDING', 'CONFIRMED'])->count() + 1;

        Reservation::create([
            'memberId'        => $member->id,
            'bookId'          => $book->bookId,
            'reservationDate' => now(),
            'expiryDate'      => now()->addDays(7),
            'status'          => 'PENDING',
            'queuePosition'   => $queuePos,
        ]);

        return back()->with('success', "Reserved '{$book->title}'. Queue position: {$queuePos}.");
    }

    public function confirm(Reservation $reservation)
    {
        $reservation->confirm();

        // Create the loan
        $librarian = Auth::user()->librarian;
        $loan = Loan::create([
            'memberId'    => $reservation->memberId,
            'bookId'      => $reservation->bookId,
            'librarianId' => $librarian->id,
            'issueDate'   => Carbon::today(),
            'dueDate'     => Carbon::today()->addDays(14),
            'status'      => 'ACTIVE',
        ]);

        $reservation->update(['loanId' => $loan->loanId]);
        $reservation->book->decrement('availableCopies');

        Notification::create([
            'memberId'    => $reservation->memberId,
            'type'        => 'AVAIL',
            'title'       => 'Reservation Ready',
            'message'     => "Your reserved book '{$reservation->book->title}' is ready for collection.",
            'channel'     => 'IN_APP',
            'deliveredAt' => now(),
        ]);

        return back()->with('success', 'Reservation confirmed and loan created.');
    }

    public function cancel(Reservation $reservation)
    {
        $reservation->cancel();
        return back()->with('success', 'Reservation cancelled.');
    }
}
