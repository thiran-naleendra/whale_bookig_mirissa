<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
        <div class="container">

            <a class="navbar-brand fw-bold" href="{{ route('admin.bookings') }}">
                Admin Panel
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.bookings') ? 'active fw-semibold' : '' }}"
                            href="{{ route('admin.bookings') }}">
                            Bookings
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.prices') ? 'active fw-semibold' : '' }}"
                            href="{{ route('admin.prices') }}">
                            Monthly Prices
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active fw-semibold' : '' }}"
                            href="{{ route('admin.reports') }}">
                            Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.disabled_dates*') ? 'active fw-semibold' : '' }}"
                            href="{{ route('admin.disabled_dates') }}">
                            Disabled Dates
                        </a>
                    </li>


                </ul>

                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small">
                        {{ session('admin_name') }}
                    </span>

                    <form method="post" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="btn btn-outline-dark btn-sm">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </nav>

    <div class="container py-4">

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
