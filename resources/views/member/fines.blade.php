{{-- member/fines.blade.php --}}
@extends('layouts.app')
@section('title', 'My Fines')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-cash-coin me-2 text-primary"></i>My Fines</h4>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Book</th><th>Amount</th><th>Reason</th><th>Issued</th><th>Paid</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($fines as $fine)
                <tr>
                    <td>{{ $fine->loan->book->title ?? '—' }}</td>
                    <td class="fw-bold text-danger">${{ number_format($fine->amount, 2) }}</td>
                    <td>{{ $fine->reason }}</td>
                    <td>{{ $fine->issuedDate->format('d M Y') }}</td>
                    <td>{{ $fine->paidDate ? $fine->paidDate->format('d M Y') : '—' }}</td>
                    <td><span class="badge badge-status-{{ $fine->status }}">{{ $fine->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4 text-success">
                    <i class="bi bi-check-circle me-2"></i>No fines — great job!
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($fines->hasPages())
        <div class="card-footer bg-white">{{ $fines->links() }}</div>
    @endif
</div>
@endsection
