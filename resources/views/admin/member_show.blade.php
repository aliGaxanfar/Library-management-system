@extends('layouts.app')
@section('title', 'Member Profile')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.members') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0"><i class="bi bi-person me-2 text-primary"></i>{{ $member->user->fullName }}</h4>
</div>

<div class="row g-3">
    <!-- Profile -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Profile</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Member ID</dt><dd class="col-7"><code>{{ $member->memberId }}</code></dd>
                    <dt class="col-5">Email</dt><dd class="col-7">{{ $member->user->email }}</dd>
                    <dt class="col-5">Type</dt><dd class="col-7">{{ $member->membershipType }}</dd>
                    <dt class="col-5">Joined</dt><dd class="col-7">{{ $member->joinDate->format('d M Y') }}</dd>
                    <dt class="col-5">Expires</dt><dd class="col-7">{{ $member->expiryDate->format('d M Y') }}</dd>
                    <dt class="col-5">Fines Due</dt>
                    <dd class="col-7 {{ $member->totalFinesDue > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                        ${{ number_format($member->totalFinesDue, 2) }}
                    </dd>
                    <dt class="col-5">Status</dt>
                    <dd class="col-7">
                        <span class="badge {{ $member->user->isActive ? 'bg-success' : 'bg-secondary' }}">
                            {{ $member->user->isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </dl>
                <form action="{{ route('admin.members.toggle', $member) }}" method="POST" class="mt-3">
                    @csrf
                    <button class="btn btn-sm {{ $member->user->isActive ? 'btn-outline-danger' : 'btn-outline-success' }} w-100">
                        {{ $member->user->isActive ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Loan History -->
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">Loan History ({{ $member->loans->count() }})</div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>Book</th><th>Issued</th><th>Due</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($member->loans->take(5) as $loan)
                        <tr>
                            <td>{{ Str::limit($loan->book->title, 30) }}</td>
                            <td>{{ $loan->issueDate->format('d M Y') }}</td>
                            <td>{{ $loan->dueDate->format('d M Y') }}</td>
                            <td><span class="badge badge-status-{{ $loan->status }}">{{ $loan->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-2">No loans</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Fines -->
        <div class="card">
            <div class="card-header">Fines ({{ $member->fines->count() }})</div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>Amount</th><th>Reason</th><th>Issued</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($member->fines as $fine)
                        <tr>
                            <td class="text-danger fw-bold">${{ number_format($fine->amount, 2) }}</td>
                            <td>{{ $fine->reason }}</td>
                            <td>{{ $fine->issuedDate->format('d M Y') }}</td>
                            <td><span class="badge badge-status-{{ $fine->status }}">{{ $fine->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-2">No fines</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
