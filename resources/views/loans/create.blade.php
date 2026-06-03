{{-- loans/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Issue Loan')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('librarian.loans.index') }}" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Issue New Loan</h4>
</div>

<div class="card" style="max-width:540px;">
    <div class="card-body p-4">
        <form action="{{ route('librarian.loans.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
                <select name="memberId" class="form-select @error('memberId') is-invalid @enderror" required>
                    <option value="">— Select Member —</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}" @selected(old('memberId') == $m->id)>
                            {{ $m->user->fullName }} ({{ $m->memberId }})
                        </option>
                    @endforeach
                </select>
                @error('memberId')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Book <span class="text-danger">*</span></label>
                <select name="bookId" class="form-select @error('bookId') is-invalid @enderror" required>
                    <option value="">— Select Available Book —</option>
                    @foreach($books as $book)
                        <option value="{{ $book->bookId }}" @selected(old('bookId') == $book->bookId)>
                            {{ $book->title }} — {{ $book->authorNames }} ({{ $book->availableCopies }} available)
                        </option>
                    @endforeach
                </select>
                @error('bookId')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="alert alert-info py-2">
                <i class="bi bi-info-circle me-2"></i>Loan period is <strong>14 days</strong>. Up to 2 renewals allowed.
            </div>
            <button type="submit" class="btn btn-primary px-4 mt-2">
                <i class="bi bi-journal-plus me-2"></i>Issue Loan
            </button>
        </form>
    </div>
</div>
@endsection
