@extends('layouts.app')
@section('title', 'Books')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-books me-2 text-primary"></i>Book Catalogue</h4>
    @if(Auth::user()->isAdmin() || Auth::user()->isLibrarian())
        <a href="{{ route('books.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Add Book
        </a>
    @endif
</div>

<!-- Search/Filter Bar -->
<div class="card mb-4">
    <div class="card-body">
        <form action="" method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Title, ISBN, Author…" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->categoryId }}" @selected(request('category') == $cat->categoryId)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    @foreach(['AVAILABLE','BORROWED','RESERVED','LOST'] as $s)
                        <option value="{{ $s }}" @selected(request('status') == $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Book Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author(s)</th>
                        <th>ISBN</th>
                        <th>Year</th>
                        <th>Copies</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($books as $book)
                    <tr>
                        <td class="fw-semibold">{{ $book->title }}</td>
                        <td>{{ $book->authorNames ?: '—' }}</td>
                        <td><code>{{ $book->isbn }}</code></td>
                        <td>{{ $book->publishedYear ?? '—' }}</td>
                        <td>
                            <span class="text-success fw-bold">{{ $book->availableCopies }}</span>
                            <span class="text-muted">/ {{ $book->totalCopies }}</span>
                        </td>
                        <td>
                            <span class="badge badge-status-{{ $book->status }} px-2 py-1">{{ $book->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(Auth::user()->isAdmin() || Auth::user()->isLibrarian())
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
                            @if(Auth::user()->isMember() && $book->availableCopies == 0)
                                <form action="{{ route('member.reservations.store') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="bookId" value="{{ $book->bookId }}">
                                    <button class="btn btn-sm btn-outline-warning" title="Reserve">
                                        <i class="bi bi-bookmark-plus"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No books found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($books->hasPages())
        <div class="card-footer bg-white">{{ $books->links() }}</div>
    @endif
</div>
@endsection
