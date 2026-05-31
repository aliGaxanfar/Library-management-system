@extends('layouts.app')
@section('title', 'Register — LMS')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg,#1e3a5f,#2e5898);">
    <div class="card shadow-lg" style="width:480px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="bi bi-person-plus text-primary" style="font-size:2.5rem;"></i>
                <h3 class="mt-2 fw-bold">Create Member Account</h3>
            </div>

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="fullName" class="form-control @error('fullName') is-invalid @enderror"
                           value="{{ old('fullName') }}" required>
                    @error('fullName')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Membership Type</label>
                    <select name="membershipType" class="form-select" required>
                        <option value="STANDARD">Standard</option>
                        <option value="PREMIUM">Premium</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-person-check me-2"></i>Register
                </button>
            </form>
            <p class="text-center mt-3 mb-0">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </div>
</div>
@endsection
