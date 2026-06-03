{{-- member/notifications.blade.php --}}
@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-bell me-2 text-primary"></i>My Notifications</h4>

<div class="card">
    <div class="list-group list-group-flush">
        @forelse($notifications as $n)
            <div class="list-group-item {{ !$n->isRead ? 'bg-light' : '' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-{{ $n->type === 'FINE' ? 'danger' : ($n->type === 'OVERDUE' ? 'warning text-dark' : 'info') }} me-2">{{ $n->type }}</span>
                        <strong>{{ $n->title }}</strong>
                        @if(!$n->isRead)<span class="badge bg-primary ms-1">New</span>@endif
                    </div>
                    <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-0 mt-1 text-muted small">{{ $n->message }}</p>
            </div>
        @empty
            <div class="list-group-item text-center text-muted py-4">
                <i class="bi bi-bell-slash" style="font-size:2rem;"></i>
                <p class="mt-2 mb-0">No notifications yet.</p>
            </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
        <div class="card-footer bg-white">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
