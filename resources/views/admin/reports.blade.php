@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">Reports</h4>
    <div class="text-muted small">Bookings & revenue summary</div>
  </div>
</div>

<form class="row g-2 mb-3">
  <div class="col-md-3">
    <label class="form-label small mb-1">From</label>
    <input type="date" name="from" class="form-control" value="{{ $from }}">
  </div>
  <div class="col-md-3">
    <label class="form-label small mb-1">To</label>
    <input type="date" name="to" class="form-control" value="{{ $to }}">
  </div>
  <div class="col-md-2 d-grid align-self-end">
    <button class="btn btn-primary">Generate</button>
  </div>
</form>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted small">Total Bookings</div>
        <div class="fs-3 fw-bold">{{ $summary->total_bookings ?? 0 }}</div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted small">Confirmed Revenue</div>
        <div class="fs-3 fw-bold">{{ number_format($summary->confirmed_revenue ?? 0, 2) }} LKR</div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted small">Confirmed Bookings</div>
        <div class="fs-3 fw-bold">{{ $summary->confirmed_count ?? 0 }}</div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mt-3">
  <div class="card-body">
    <h5 class="mb-3">Status Breakdown</h5>
    <div class="row g-2">
      <div class="col-md-2"><span class="badge bg-success w-100">CONFIRMED: {{ $summary->confirmed_count ?? 0 }}</span></div>
      <div class="col-md-2"><span class="badge bg-warning text-dark w-100">PENDING: {{ $summary->pending_count ?? 0 }}</span></div>
      <div class="col-md-2"><span class="badge bg-danger w-100">FAILED: {{ $summary->failed_count ?? 0 }}</span></div>
      <div class="col-md-2"><span class="badge bg-secondary w-100">CANCELLED: {{ $summary->cancelled_count ?? 0 }}</span></div>
      <div class="col-md-2"><span class="badge bg-dark w-100">REFUNDED: {{ $summary->refunded_count ?? 0 }}</span></div>
    </div>
  </div>
</div>

<div class="row g-3 mt-1">

  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Revenue by Tour Date (Confirmed)</h5>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th class="text-end">Bookings</th>
                <th class="text-end">Revenue (LKR)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($byDate as $row)
                <tr>
                  <td>{{ $row->tour_date }}</td>
                  <td class="text-end">{{ $row->cnt }}</td>
                  <td class="text-end">{{ number_format($row->revenue, 2) }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center text-muted">No confirmed bookings in this range</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Payment Method Breakdown</h5>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Method</th>
                <th class="text-end">Bookings</th>
                <th class="text-end">Revenue</th>
              </tr>
            </thead>
            <tbody>
              @forelse($byMethod as $row)
                <tr>
                  <td>{{ $row->method }}</td>
                  <td class="text-end">{{ $row->cnt }}</td>
                  <td class="text-end">{{ number_format($row->revenue, 2) }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
