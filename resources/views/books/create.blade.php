@extends('layouts.app')
@section('title', isset($book) ? 'Edit Book' : 'Add Book')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0">
        <i class="bi bi-{{ isset($book) ? 'pencil' : 'plus-circle' }} me-2 text-primary"></i>
        {{ isset($book) ? 'Edit Book' : 'Add New Book' }}
    </h4>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-body p-4">
        <form action="{{ isset($book) ? route('books.update', $book) : route('books.store') }}" method="POST">
            @csrf
            @if(isset($book)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $book->title ?? '') }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Published Year</label>
                    <input type="number" name="publishedYear" class="form-control"
                           value="{{ old('publishedYear', $book->publishedYear ?? '') }}" min="1000" max="{{ date('Y') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">ISBN <span class="text-danger">*</span></label>
                    <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror"
                           value="{{ old('isbn', $book->isbn ?? '') }}" required>
                    @error('isbn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Total Copies <span class="text-danger">*</span></label>
                    <input type="number" name="totalCopies" class="form-control" min="1"
                           value="{{ old('totalCopies', $book->totalCopies ?? 1) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Location</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. A-12"
                           value="{{ old('location', $book->location ?? '') }}">
                </div>

                @isset($book)
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['AVAILABLE','BORROWED','RESERVED','LOST'] as $s)
                            <option value="{{ $s }}" @selected(old('status', $book->status) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                @endisset

                <div class="col-12">
                    <label class="form-label fw-semibold">Author(s) <span class="text-danger">*</span></label>
                    <select name="authors[]" class="form-select @error('authors') is-invalid @enderror" multiple size="5" required>
                        @foreach($authors as $author)
                            <option value="{{ $author->authorId }}"
                                @if(isset($book) && $book->authors->contains($author->authorId)) selected
                                @elseif(in_array($author->authorId, old('authors', []))) selected
                                @endif>
                                {{ $author->fullName }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                    @error('authors')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Categories</label>
                    <div class="row g-2">
                        @foreach($categories as $cat)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="categories[]" value="{{ $cat->categoryId }}"
                                           class="form-check-input" id="cat{{ $cat->categoryId }}"
                                           @if(isset($book) && $book->categories->contains($cat->categoryId)) checked
                                           @elseif(in_array($cat->categoryId, old('categories', []))) checked
                                           @endif>
                                    <label class="form-check-label" for="cat{{ $cat->categoryId }}">{{ $cat->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-{{ isset($book) ? 'check-lg' : 'plus-lg' }} me-2"></i>
                    {{ isset($book) ? 'Update Book' : 'Add Book' }}
                </button>
                <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
