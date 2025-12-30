@extends('layouts.public')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h4 class="mb-3">Admin Login</h4>

        <form method="post" action="{{ route('admin.login.post') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="admin@example.com">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required value="admin123">
          </div>
          <div class="d-grid">
            <button class="btn btn-dark btn-lg">Login</button>
          </div>
          <p class="text-muted small mt-3 mb-0">
            Default: admin@example.com / admin123
          </p>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
