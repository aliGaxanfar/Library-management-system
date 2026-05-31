@extends('layouts.app')
@section('title', 'Librarian Dashboard')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2 text-primary"></i>Librarian Dashboard</h4>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="card stat-card primary text-center">
            <div class="card-body"><div class="h2 fw-bold text-primary">{{ $stats['activeLoans'] }}</div><div class="text-muted small">Active Loans</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card stat-card danger text-center">
            <div class="card-body"><div class="h2 fw-bold text-danger">{{ $stats['overdueLoans'] }}</div><div class="text-muted small">Overdue</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card stat-card success text-center">
            <div class="card-body"><div class="h2 fw-bold text-success">{{ $stats['availBooks'] }}</div><div class="text-muted small">Books Available</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card stat-card warning text-center">
            <div class="card-body"><div class="h2 fw-bold text-warning">{{ $stats['pendingReserv'] }}</div><div class="text-muted small">Pending Reserv.</div></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Overdue Loans</span>
        <a href="{{ route('librarian.loans.index') }}?status=OVERDUE" class="btn btn-sm btn-outline-danger">All Overdue</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Member</th><th>Book</th><th>Due Date</th><th>Days Overdue</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($overdueLoans as $loan)
                <tr class="table-danger">
                    <td>{{ $loan->member->user->fullName }}</td>
                    <td>{{ Str::limit($loan->book->title, 30) }}</td>
                    <td>{{ $loan->dueDate->format('d M Y') }}</td>
                    <td class="text-danger fw-bold">{{ $loan->getDaysOverdue() }} days</td>
                    <td>
                        <form action="{{ route('librarian.loans.return', $loan) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success" onclick="return confirm('Mark returned?')">
                                <i class="bi bi-arrow-return-left me-1"></i>Return
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-3 text-success"><i class="bi bi-check-circle me-2"></i>No overdue loans!</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
