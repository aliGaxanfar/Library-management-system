@extends('layouts.app')
@section('title', 'Fines')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-cash me-2 text-primary"></i>
    {{ Auth::user()->isMember() ? 'My Fines' : 'All Fines' }}
</h4>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>Loan #</th>
                    @if(!Auth::user()->isMember())<th>Member</th>@endif
                    <th>Book</th>
                    <th>Amount</th>
                    <th>Reason</th>
                    <th>Issued</th>
                    <th>Status</th>
                    @if(!Auth::user()->isMember())<th>Actions</th>@endif
                </tr></thead>
                <tbody>
                @forelse($fines as $fine)
                    <tr>
                        <td>{{ $fine->loanId }}</td>
                        @if(!Auth::user()->isMember())<td>{{ $fine->member->user->fullName }}</td>@endif
                        <td>{{ Str::limit($fine->loan->book->title ?? '—', 30) }}</td>
                        <td class="fw-bold text-danger">${{ number_format($fine->amount, 2) }}</td>
                        <td>{{ $fine->reason }}</td>
                        <td>{{ $fine->issuedDate->format('d M Y') }}</td>
                        <td><span class="badge badge-status-{{ $fine->status }} px-2 py-1">{{ $fine->status }}</span></td>
                        @if(!Auth::user()->isMember())
                        <td>
                            @if($fine->status === 'PENDING')
                                <form action="{{ route('librarian.fines.paid', $fine) }}" method="POST" class="d-inline">
                                    @csrf <button class="btn btn-sm btn-success">Paid</button>
                                </form>
                                <form action="{{ route('librarian.fines.waive', $fine) }}" method="POST" class="d-inline">
                                    @csrf <button class="btn btn-sm btn-outline-secondary">Waive</button>
                                </form>
                            @endif
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No fines found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($fines->hasPages())
        <div class="card-footer bg-white">{{ $fines->links() }}</div>
    @endif
</div>
@endsection
