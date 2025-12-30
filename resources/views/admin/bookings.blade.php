@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">Bookings</h4>
    <div class="text-muted small">Manage pending/confirmed/failed bookings</div>
  </div>
</div>

<form class="row g-2 mb-3">
  <div class="col-md-3">
    <input type="date" name="date" class="form-control" value="{{ $date }}">
  </div>
  <div class="col-md-3">
    <select name="status" class="form-select">
      <option value="">All Status</option>
      @foreach(['PENDING','CONFIRMED','FAILED','CANCELLED','REFUNDED'] as $s)
        <option value="{{ $s }}" @if($status===$s) selected @endif>{{ $s }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <input type="text" name="q" class="form-control" placeholder="Search name/email/order/mobile" value="{{ $q }}">
  </div>
  <div class="col-md-2 d-grid">
    <button class="btn btn-primary">Filter</button>
  </div>
</form>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Order</th>
          <th>Date</th>
          <th>Customer</th>
          <th>Qty</th>
          <th>Total</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      @foreach($bookings as $b)
        <tr>
          <td>{{ $b->id }}</td>
          <td class="fw-semibold">{{ $b->order_id }}</td>
          <td>{{ $b->tour_date }}</td>
          <td>
            <div class="fw-semibold">{{ $b->name }}</div>
            <div class="text-muted small">{{ $b->email }} • {{ $b->mobile }}</div>
          </td>
          <td>
            <span class="badge text-bg-secondary">A {{ $b->adults_qty }}</span>
            <span class="badge text-bg-secondary">C {{ $b->children_qty }}</span>
            <span class="badge text-bg-secondary">K {{ $b->kids_qty }}</span>
          </td>
          <td>{{ number_format($b->total, 2) }} LKR</td>
          <td>
            @php
              $cls = match($b->status){
                'CONFIRMED' => 'success',
                'FAILED' => 'danger',
                'PENDING' => 'warning',
                default => 'secondary'
              };
            @endphp
            <span class="badge text-bg-{{ $cls }}">{{ $b->status }}</span>
          </td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.booking.show', $b->id) }}">View</a>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">
  {{ $bookings->links() }}
</div>
@endsection
