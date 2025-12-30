<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Admin Panel' }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('admin.bookings') }}">Admin Panel</a>
    <div class="d-flex gap-2 align-items-center">
      <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.bookings') }}">Bookings</a>
      <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.prices') }}">Monthly Prices</a>
      <span class="text-muted small">Hi, {{ session('admin_name') }}</span>
      <form method="post" action="{{ route('admin.logout') }}">
        @csrf
        <button class="btn btn-dark btn-sm">Logout</button>
      </form>
    </div>
  </div>
</nav>

<div class="container py-4">
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @yield('content')
</div>
</body>
</html>
