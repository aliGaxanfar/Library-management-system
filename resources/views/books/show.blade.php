@extends('layouts.app')
@section('title', $book->title)

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0"><i class="bi bi-book me-2 text-primary"></i>{{ $book->title }}</h4>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">Book Details</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">ISBN</dt><dd class="col-7"><code>{{ $book->isbn }}</code></dd>
                    <dt class="col-5">Published</dt><dd class="col-7">{{ $book->publishedYear ?? '—' }}</dd>
                    <dt class="col-5">Location</dt><dd class="col-7">{{ $book->location ?? '—' }}</dd>
                    <dt class="col-5">Total Copies</dt><dd class="col-7">{{ $book->totalCopies }}</dd>
                    <dt class="col-5">Available</dt>
                    <dd class="col-7">
                        <span class="fw-bold {{ $book->availableCopies > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $book->availableCopies }}
                        </span>
                    </dd>
                    <dt class="col-5">Status</dt>
                    <dd class="col-7"><span class="badge badge-status-{{ $book->status }} px-2 py-1">{{ $book->status }}</span></dd>
                    <dt class="col-5">Author(s)</dt>
                    <dd class="col-7">
                        @foreach($book->authors as $a)
                            <span class="badge bg-light text-dark border me-1">{{ $a->fullName }}</span>
                        @endforeach
                    </dd>
                    <dt class="col-5">Categories</dt>
                    <dd class="col-7">
                        @forelse($book->categories as $c)
                            <span class="badge bg-secondary me-1">{{ $c->name }}</span>
                        @empty
                            <span class="text-muted">—</span>
                        @endforelse
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        @if(Auth::user()->isMember())
            <div class="card mb-3">
                <div class="card-body text-center py-4">
                    @if($book->availableCopies > 0)
                        <i class="bi bi-check-circle-fill text-success" style="font-size:2.5rem;"></i>
                        <p class="mt-2 mb-3 fw-semibold">This book is available to borrow.</p>
                        <p class="text-muted small">Visit the library desk to issue a loan.</p>
                    @else
                        <i class="bi bi-bookmark-x text-warning" style="font-size:2.5rem;"></i>
                        <p class="mt-2 mb-3 fw-semibold">No copies available right now.</p>
                        <form action="{{ route('member.reservations.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="bookId" value="{{ $book->bookId }}">
                            <button class="btn btn-warning">
                                <i class="bi bi-bookmark-plus me-2"></i>Reserve This Book
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        <!-- Recent Loans -->
        <div class="card">
            <div class="card-header">Recent Loan History</div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead><tr><th>Member</th><th>Issued</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($book->loans as $loan)
                        <tr>
                            <td>{{ $loan->member->user->fullName }}</td>
                            <td>{{ $loan->issueDate->format('d M Y') }}</td>
                            <td><span class="badge badge-status-{{ $loan->status }}">{{ $loan->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-2">No loans yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->isAdmin() || Auth::user()->isLibrarian())
<div class="mt-3 d-flex gap-2">
    <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-2"></i>Edit Book
    </a>
    <form action="{{ route('books.destroy', $book) }}" method="POST"
          onsubmit="return confirm('Delete this book? This cannot be undone.')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger"><i class="bi bi-trash me-2"></i>Delete</button>
    </form>
</div>
@endif
@endsection
