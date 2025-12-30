@extends('layouts.admin')

@section('content')
<h4 class="mb-3">Disabled Booking Dates</h4>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="post" action="{{ route('admin.disabled_dates.add') }}" class="row g-2">
      @csrf
      <div class="col-md-3">
        <input type="date" name="date" class="form-control" required>
      </div>
      <div class="col-md-6">
        <input type="text" name="note" class="form-control" placeholder="Reason (optional)">
      </div>
      <div class="col-md-3 d-grid">
        <button class="btn btn-primary">Disable Date</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Note</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      @forelse($items as $it)
        <tr>
          <td>{{ $it->date }}</td>
          <td>{{ $it->note ?? '—' }}</td>
          <td class="text-end">
            <form method="post" action="{{ route('admin.disabled_dates.delete', $it->id) }}">
              @csrf
              <button class="btn btn-sm btn-outline-danger">Remove</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="3" class="text-center text-muted">No disabled dates</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
