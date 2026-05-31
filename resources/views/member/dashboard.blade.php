@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
<h4 class="fw-bold mb-1">Welcome, {{ Auth::user()->fullName }}!</h4>
<p class="text-muted mb-4">Member ID: <code>{{ $member->memberId }}</code> &nbsp;|&nbsp; Expires: {{ $member->expiryDate->format('d M Y') }}</p>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="card stat-card primary text-center">
            <div class="card-body">
                <div class="h2 fw-bold text-primary">{{ $activeLoans->count() }}</div>
                <div class="text-muted small">Active Loans</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card stat-card warning text-center">
            <div class="card-body">
                <div class="h2 fw-bold text-warning">${{ number_format($member->totalFinesDue, 2) }}</div>
                <div class="text-muted small">Fines Due</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card stat-card success text-center">
            <div class="card-body">
                <div class="h2 fw-bold text-success">{{ $reservations->count() }}</div>
                <div class="text-muted small">Reservations</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card stat-card danger text-center">
            <div class="card-body">
                <div class="h2 fw-bold text-danger">{{ $notifications->count() }}</div>
                <div class="text-muted small">Unread Alerts</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Active Loans -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-journal me-2"></i>My Active Loans</span>
                <a href="{{ route('member.loans') }}" class="btn btn-sm btn-outline-primary">All Loans</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Book</th><th>Due</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($activeLoans as $loan)
                        <tr class="{{ $loan->status === 'OVERDUE' ? 'table-danger' : '' }}">
                            <td>{{ Str::limit($loan->book->title, 35) }}</td>
                            <td>{{ $loan->dueDate->format('d M Y') }}</td>
                            <td><span class="badge badge-status-{{ $loan->status }}">{{ $loan->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No active loans</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi bi-bell me-2"></i>Notifications</span>
                <a href="{{ route('member.notifications') }}" class="btn btn-sm btn-outline-secondary">All</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($notifications as $notif)
                    <li class="list-group-item">
                        <div class="fw-semibold small">{{ $notif->title }}</div>
                        <div class="text-muted small">{{ Str::limit($notif->message, 60) }}</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $notif->created_at->diffForHumans() }}</div>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-3">No unread notifications</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
