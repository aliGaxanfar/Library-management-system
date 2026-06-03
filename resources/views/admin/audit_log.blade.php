@extends('layouts.app')
@section('title', 'Audit Log')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-shield-check me-2 text-primary"></i>Audit Log</h4>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Table</th><th>Record ID</th><th>IP</th></tr></thead>
                <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->performedAt->format('d M Y H:i') }}</td>
                        <td>{{ $log->user->fullName ?? 'System' }}</td>
                        <td>
                            <span class="badge bg-{{ $log->action === 'DELETE' ? 'danger' : ($log->action === 'CREATE' ? 'success' : 'primary') }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td><code>{{ $log->tableName }}</code></td>
                        <td>{{ $log->recordId }}</td>
                        <td class="text-muted">{{ $log->ipAddress ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No audit logs.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-white">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
