@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2 text-primary"></i>Admin Dashboard</h4>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Total Books</div>
                <div class="h3 fw-bold text-primary">{{ $stats['totalBooks'] }}</div>
                <i class="bi bi-books text-primary opacity-25" style="font-size:2rem; position:absolute; right:12px; bottom:8px;"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Members</div>
                <div class="h3 fw-bold text-success">{{ $stats['totalMembers'] }}</div>
                <i class="bi bi-people text-success opacity-25" style="font-size:2rem; position:absolute; right:12px; bottom:8px;"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Active Loans</div>
                <div class="h3 fw-bold text-primary">{{ $stats['activeLoans'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card danger h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Overdue</div>
                <div class="h3 fw-bold text-danger">{{ $stats['overdueLoans'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Pending Fines</div>
                <div class="h3 fw-bold text-warning">${{ number_format($stats['pendingFines'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Reservations</div>
                <div class="h3 fw-bold text-success">{{ $stats['pendingReserv'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Recent Loans -->
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-check me-2"></i>Recent Loans</span>
                <a href="{{ route('librarian.loans.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr>
                            <th>Member</th><th>Book</th><th>Due</th><th>Status</th>
                        </tr></thead>
                        <tbody>
                        @forelse($recentLoans as $loan)
                            <tr>
                                <td>{{ $loan->member->user->fullName }}</td>
                                <td>{{ Str::limit($loan->book->title, 30) }}</td>
                                <td>{{ $loan->dueDate->format('d M Y') }}</td>
                                <td><span class="badge badge-status-{{ $loan->status }} px-2 py-1">{{ $loan->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No loans yet</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Audit Log -->
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-shield-check me-2"></i>Recent Audit</span>
                <a href="{{ route('admin.audit_log') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentAudit as $log)
                        <li class="list-group-item py-2 px-3">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <span class="badge bg-{{ $log->action === 'DELETE' ? 'danger' : ($log->action === 'CREATE' ? 'success' : 'primary') }} me-1">{{ $log->action }}</span>
                                    {{ $log->tableName }} #{{ $log->recordId }}
                                </span>
                                <small class="text-muted">{{ $log->performedAt->diffForHumans() }}</small>
                            </div>
                            <small class="text-muted">by {{ $log->user->fullName ?? 'System' }}</small>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-3">No audit logs yet</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
