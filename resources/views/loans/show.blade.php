@extends('layouts.app')
@section('title', 'Loan Details')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('librarian.loans.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0">Loan #{{ $loan->loanId }}</h4>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Loan Details</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Member</dt><dd class="col-7">{{ $loan->member->user->fullName }}</dd>
                    <dt class="col-5">Book</dt><dd class="col-7">{{ $loan->book->title }}</dd>
                    <dt class="col-5">Issued By</dt><dd class="col-7">{{ $loan->librarian->user->fullName }}</dd>
                    <dt class="col-5">Issue Date</dt><dd class="col-7">{{ $loan->issueDate->format('d M Y') }}</dd>
                    <dt class="col-5">Due Date</dt><dd class="col-7">{{ $loan->dueDate->format('d M Y') }}</dd>
                    <dt class="col-5">Returned</dt><dd class="col-7">{{ $loan->returnDate ? $loan->returnDate->format('d M Y') : '—' }}</dd>
                    <dt class="col-5">Renewals</dt><dd class="col-7">{{ $loan->renewalCount }} / 2</dd>
                    <dt class="col-5">Status</dt>
                    <dd class="col-7"><span class="badge badge-status-{{ $loan->status }} px-2 py-1">{{ $loan->status }}</span></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        @if($loan->fine)
        <div class="card border-danger">
            <div class="card-header text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Fine</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Amount</dt><dd class="col-7 fw-bold text-danger">${{ number_format($loan->fine->amount, 2) }}</dd>
                    <dt class="col-5">Reason</dt><dd class="col-7">{{ $loan->fine->reason }}</dd>
                    <dt class="col-5">Issued</dt><dd class="col-7">{{ $loan->fine->issuedDate->format('d M Y') }}</dd>
                    <dt class="col-5">Status</dt>
                    <dd class="col-7"><span class="badge badge-status-{{ $loan->fine->status }}">{{ $loan->fine->status }}</span></dd>
                </dl>
                @if($loan->fine->status === 'PENDING')
                <div class="mt-3 d-flex gap-2">
                    <form action="{{ route('librarian.fines.paid', $loan->fine) }}" method="POST">
                        @csrf <button class="btn btn-sm btn-success">Mark Paid</button>
                    </form>
                    <form action="{{ route('librarian.fines.waive', $loan->fine) }}" method="POST">
                        @csrf <button class="btn btn-sm btn-outline-secondary">Waive</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-check-circle text-success" style="font-size:2rem;"></i>
                <p class="mt-2 mb-0">No fine associated with this loan.</p>
            </div>
        </div>
        @endif
    </div>
</div>

@if($loan->status !== 'RETURNED')
<div class="mt-3 d-flex gap-2">
    <form action="{{ route('librarian.loans.return', $loan) }}" method="POST">
        @csrf
        <button class="btn btn-success" onclick="return confirm('Confirm return?')">
            <i class="bi bi-arrow-return-left me-2"></i>Return Book
        </button>
    </form>
    @if($loan->renewalCount < 2)
    <form action="{{ route('librarian.loans.renew', $loan) }}" method="POST">
        @csrf
        <button class="btn btn-warning"><i class="bi bi-arrow-clockwise me-2"></i>Renew Loan</button>
    </form>
    @endif
</div>
@endif
@endsection
