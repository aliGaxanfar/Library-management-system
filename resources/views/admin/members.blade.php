{{-- admin/members.blade.php --}}
@extends('layouts.app')
@section('title', 'Members')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-people me-2 text-primary"></i>All Members</h4>

<div class="card mb-3">
    <div class="card-body py-2">
        <form class="row g-2" method="GET">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Name or email…" value="{{ request('search') }}">
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
        <table class="table table-hover mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Member ID</th><th>Type</th><th>Expires</th><th>Fines Due</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($members as $m)
                <tr>
                    <td>{{ $m->user->fullName }}</td>
                    <td>{{ $m->user->email }}</td>
                    <td><code>{{ $m->memberId }}</code></td>
                    <td>{{ $m->membershipType }}</td>
                    <td>{{ $m->expiryDate->format('d M Y') }}</td>
                    <td class="{{ $m->totalFinesDue > 0 ? 'text-danger fw-bold' : '' }}">${{ number_format($m->totalFinesDue, 2) }}</td>
                    <td>
                        <span class="badge {{ $m->user->isActive ? 'bg-success' : 'bg-secondary' }}">
                            {{ $m->user->isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.members.show', $m) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        <form action="{{ route('admin.members.toggle', $m) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm {{ $m->user->isActive ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                <i class="bi bi-{{ $m->user->isActive ? 'pause' : 'play' }}"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No members found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($members->hasPages())
        <div class="card-footer bg-white">{{ $members->links() }}</div>
    @endif
</div>
@endsection
