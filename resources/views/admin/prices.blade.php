@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Monthly Prices</h4>
            <div class="text-muted small">Set prices per month</div>
        </div>

        <form class="d-flex gap-2">
            <input type="number" name="year" class="form-control" style="width:120px" value="{{ $year }}">
            <button class="btn btn-outline-dark">Go</button>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.prices.save') }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control" value="{{ $year }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select" required>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Adult Price (LKR)</label>
                    <input type="number" name="adult_price" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Child Price (LKR)</label>
                    <input type="number" name="child_price" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Enabled</label>
                    <select name="is_enabled" class="form-select">
                        <option value="1">YES</option>
                        <option value="0">NO</option>
                    </select>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">Save Price</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th>Adult</th>
                        <th>Child</th>
                        <th>Enabled</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($m = 1; $m <= 12; $m++)
                        @php $p = $prices[$m] ?? null; @endphp
                        <tr>
                            <td>{{ $m }}</td>
                            <td>{{ $p ? number_format($p->adult_price, 2) : '—' }}</td>
                            <td>{{ $p ? number_format($p->child_price, 2) : '—' }}</td>
                            <td>{{ $p ? ($p->is_enabled ? 'YES' : 'NO') : '—' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
@endsection
