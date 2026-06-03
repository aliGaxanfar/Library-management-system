@extends('layouts.app')
@section('title', 'My Loans')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-journal me-2 text-primary"></i>My Loans</h4>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Book</th><th>Issued</th><th>Due</th><th>Returned</th><th>Renewals</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($loans as $loan)
                    <tr class="{{ $loan->status === 'OVERDUE' ? 'table-danger' : '' }}">
                        <td>{{ $loan->book->title }}</td>
                        <td>{{ $loan->issueDate->format('d M Y') }}</td>
                        <td>{{ $loan->dueDate->format('d M Y') }}</td>
                        <td>{{ $loan->returnDate ? $loan->returnDate->format('d M Y') : '—' }}</td>
                        <td>{{ $loan->renewalCount }}</td>
                        <td><span class="badge badge-status-{{ $loan->status }}">{{ $loan->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No loans yet.</td></tr>
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
