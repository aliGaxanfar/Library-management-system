@extends('layouts.app')
@section('title', 'Reservations')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-bookmark me-2 text-primary"></i>
    {{ Auth::user()->isMember() ? 'My Reservations' : 'All Reservations' }}
</h4>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>#</th>
                    @if(!Auth::user()->isMember())<th>Member</th>@endif
                    <th>Book</th>
                    <th>Reserved</th>
                    <th>Expires</th>
                    <th>Queue</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($reservations as $r)
                    <tr>
                        <td>{{ $r->reservationId }}</td>
                        @if(!Auth::user()->isMember())<td>{{ $r->member->user->fullName }}</td>@endif
                        <td>{{ Str::limit($r->book->title, 30) }}</td>
                        <td>{{ $r->reservationDate->format('d M Y') }}</td>
                        <td>{{ $r->expiryDate->format('d M Y') }}</td>
                        <td class="text-center">{{ $r->queuePosition }}</td>
                        <td><span class="badge badge-status-{{ $r->status }} px-2 py-1">{{ $r->status }}</span></td>
                        <td>
                            @if($r->status === 'PENDING')
                                @if(Auth::user()->isLibrarian() || Auth::user()->isAdmin())
                                    <form action="{{ route('librarian.reservations.confirm', $r) }}" method="POST" class="d-inline">
                                        @csrf <button class="btn btn-sm btn-success">Confirm</button>
                                    </form>
                                @endif
                                <form action="{{ Auth::user()->isMember()
                                    ? route('member.reservations.cancel', $r)
                                    : route('librarian.reservations.cancel', $r) }}" method="POST" class="d-inline">
                                    @csrf <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No reservations found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reservations->hasPages())
        <div class="card-footer bg-white">{{ $reservations->links() }}</div>
    @endif
</div>
@endsection
