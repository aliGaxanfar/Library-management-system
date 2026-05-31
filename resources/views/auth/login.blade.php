@extends('layouts.app')
@section('title', 'Login — LMS')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg,#1e3a5f,#2e5898);">
    <div class="card shadow-lg" style="width:420px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="bi bi-book-half text-primary" style="font-size:3rem;"></i>
                <h3 class="mt-2 fw-bold">Library Management</h3>
                <p class="text-muted">Sign in to your account</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>

            <hr class="my-4">
            <p class="text-center mb-0">
                New member? <a href="{{ route('register') }}">Create account</a>
            </p>
        </div>
    </div>
</div>
@endsection
