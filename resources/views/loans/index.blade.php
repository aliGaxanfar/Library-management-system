@extends('layouts.app')
@section('title', 'Loans')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-journal-check me-2 text-primary"></i>Loans</h4>
    <a href="{{ route('librarian.loans.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Issue Loan
    </a>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search member or book…" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach(['ACTIVE','RETURNED','OVERDUE'] as $s)
                        <option value="{{ $s }}" @selected(request('status')==$s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>#</th><th>Member</th><th>Book</th><th>Issued</th><th>Due</th><th>Returned</th><th>Renewals</th><th>Status</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($loans as $loan)
                    <tr class="{{ $loan->status === 'OVERDUE' ? 'table-danger' : '' }}">
                        <td>{{ $loan->loanId }}</td>
                        <td>{{ $loan->member->user->fullName }}</td>
                        <td>{{ Str::limit($loan->book->title, 30) }}</td>
                        <td>{{ $loan->issueDate->format('d M Y') }}</td>
                        <td>{{ $loan->dueDate->format('d M Y') }}</td>
                        <td>{{ $loan->returnDate ? $loan->returnDate->format('d M Y') : '—' }}</td>
                        <td class="text-center">{{ $loan->renewalCount }}</td>
                        <td><span class="badge badge-status-{{ $loan->status }} px-2 py-1">{{ $loan->status }}</span></td>
                        <td>
                            <a href="{{ route('librarian.loans.show', $loan) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            @if($loan->status !== 'RETURNED')
                                <form action="{{ route('librarian.loans.return', $loan) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" title="Return"
                                            onclick="return confirm('Mark as returned?')">
                                        <i class="bi bi-arrow-return-left"></i>
                                    </button>
                                </form>
                                @if($loan->renewalCount < 2)
                                <form action="{{ route('librarian.loans.renew', $loan) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning" title="Renew">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center py-4 text-muted">No loans found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($loans->hasPages())
        <div class="card-footer bg-white">{{ $loans->links() }}</div>
    @endif
</div>
@endsection
