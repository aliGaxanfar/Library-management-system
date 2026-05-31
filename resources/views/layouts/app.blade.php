<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Library Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f4f8; }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .sidebar { min-height: calc(100vh - 56px); background: #1e3a5f; }
        .sidebar .nav-link { color: #c9d9ed; padding: .6rem 1.2rem; border-radius: 6px; margin: 2px 8px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #2e5898; color: #fff; }
        .sidebar .nav-link i { width: 20px; }
        .sidebar .nav-section { color: #7a99bb; font-size: .72rem; text-transform: uppercase; padding: .8rem 1.2rem .2rem; letter-spacing: 1px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .card-header { background: #fff; border-bottom: 1px solid #e8edf2; font-weight: 600; }
        .stat-card { border-left: 4px solid; }
        .stat-card.primary { border-color: #0d6efd; }
        .stat-card.success { border-color: #198754; }
        .stat-card.warning { border-color: #ffc107; }
        .stat-card.danger  { border-color: #dc3545; }
        .table th { background: #f8fafc; font-size: .85rem; text-transform: uppercase; letter-spacing: .5px; }
        .badge-status-ACTIVE    { background: #d1fae5; color: #065f46; }
        .badge-status-RETURNED  { background: #e0e7ff; color: #3730a3; }
        .badge-status-OVERDUE   { background: #fee2e2; color: #991b1b; }
        .badge-status-PENDING   { background: #fef3c7; color: #92400e; }
        .badge-status-PAID      { background: #d1fae5; color: #065f46; }
        .badge-status-WAIVED    { background: #e5e7eb; color: #374151; }
        .badge-status-AVAILABLE { background: #d1fae5; color: #065f46; }
        .badge-status-BORROWED  { background: #fef3c7; color: #92400e; }
        .badge-status-LOST      { background: #fee2e2; color: #991b1b; }
    </style>
    @stack('styles')
</head>
<body>

@auth
<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#1e3a5f;">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="bi bi-book-half me-2"></i>LMS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center gap-2">
                <li class="nav-item">
                    <span class="navbar-text text-light me-2">
                        <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->fullName }}
                        <span class="badge bg-secondary ms-1">{{ Auth::user()->role }}</span>
                    </span>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-auto sidebar py-3" style="width:230px; min-width:230px;">
            @if(Auth::user()->isAdmin())
                <p class="nav-section">Admin</p>
                <a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="{{ route('books.index') }}" class="nav-link"><i class="bi bi-books me-2"></i>Books</a>
                <a href="{{ route('librarian.loans.index') }}" class="nav-link"><i class="bi bi-journal-check me-2"></i>Loans</a>
                <a href="{{ route('librarian.fines.index') }}" class="nav-link"><i class="bi bi-cash me-2"></i>Fines</a>
                <a href="{{ route('librarian.reservations.index') }}" class="nav-link"><i class="bi bi-bookmark me-2"></i>Reservations</a>
                <a href="{{ route('admin.members') }}" class="nav-link"><i class="bi bi-people me-2"></i>Members</a>
                <a href="{{ route('admin.audit_log') }}" class="nav-link"><i class="bi bi-shield-check me-2"></i>Audit Log</a>
                <a href="{{ route('admin.reports') }}" class="nav-link"><i class="bi bi-bar-chart me-2"></i>Reports</a>
            @elseif(Auth::user()->isLibrarian())
                <p class="nav-section">Librarian</p>
                <a href="{{ route('librarian.dashboard') }}" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="{{ route('books.index') }}" class="nav-link"><i class="bi bi-books me-2"></i>Books</a>
                <a href="{{ route('librarian.loans.index') }}" class="nav-link"><i class="bi bi-journal-check me-2"></i>Loans</a>
                <a href="{{ route('librarian.fines.index') }}" class="nav-link"><i class="bi bi-cash me-2"></i>Fines</a>
                <a href="{{ route('librarian.reservations.index') }}" class="nav-link"><i class="bi bi-bookmark me-2"></i>Reservations</a>
            @else
                <p class="nav-section">Member</p>
                <a href="{{ route('member.dashboard') }}" class="nav-link"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="{{ route('member.catalogue') }}" class="nav-link"><i class="bi bi-search me-2"></i>Browse Books</a>
                <a href="{{ route('member.loans') }}" class="nav-link"><i class="bi bi-journal me-2"></i>My Loans</a>
                <a href="{{ route('member.reservations') }}" class="nav-link"><i class="bi bi-bookmark me-2"></i>Reservations</a>
                <a href="{{ route('member.fines') }}" class="nav-link"><i class="bi bi-cash-coin me-2"></i>My Fines</a>
                <a href="{{ route('member.notifications') }}" class="nav-link"><i class="bi bi-bell me-2"></i>Notifications</a>
            @endif
        </div>

        <!-- Main Content -->
        <div class="col p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>

@else
<!-- Guest pages (login, register) — full standalone layout -->
@yield('content')
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>