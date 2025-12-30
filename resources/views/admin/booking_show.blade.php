@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">Booking Details</h4>
    <div class="text-muted small">Order ID: {{ $booking->order_id }}</div>
  </div>
  <a class="btn btn-outline-secondary" href="{{ route('admin.bookings') }}">Back</a>
</div>

<div class="row g-3">

  {{-- LEFT: CUSTOMER + TOUR --}}
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-body">

        <h5 class="mb-3">Customer Details</h5>
        <div><b>Name:</b> {{ $booking->name }}</div>
        <div><b>Email:</b> {{ $booking->email }}</div>
        <div><b>Mobile:</b> {{ $booking->mobile }}</div>
        <div><b>Pickup Hotel:</b> {{ $booking->pickup_hotel ?? 'N/A' }}</div>

        <div class="mt-3">
          <b>Special Requests:</b><br>
          {{ $booking->special_requests ?? '—' }}
        </div>

        <hr>

        <h5 class="mb-3">Tour Details</h5>
        <div><b>Date:</b> {{ $booking->tour_date }}</div>
        <div>
          <b>Passengers:</b>
          Adults {{ $booking->adults_qty }},
          Children {{ $booking->children_qty }},
          Kids {{ $booking->kids_qty }}
        </div>

        <hr>

        <h5 class="mb-3">Booking Summary</h5>
        <div><b>Subtotal:</b> {{ number_format($booking->subtotal, 2) }} LKR</div>
        <div><b>Discount:</b> {{ number_format($booking->discount, 2) }} LKR</div>
        <div class="fw-bold"><b>Total:</b> {{ number_format($booking->total, 2) }} LKR</div>

      </div>
    </div>
  </div>

  {{-- RIGHT: PAYMENT + TICKET --}}
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">

        <h5 class="mb-3">Payment Details (PayHere)</h5>

        <div>
          <b>Status:</b>
          @php
            $cls = match($booking->status) {
              'CONFIRMED' => 'success',
              'FAILED' => 'danger',
              'PENDING' => 'warning',
              default => 'secondary'
            };
          @endphp
          <span class="badge bg-{{ $cls }}">{{ $booking->status }}</span>
        </div>

        <hr class="my-2">

        <div><b>PayHere Payment ID:</b> {{ $booking->payhere_payment_id ?? '—' }}</div>
        <div><b>Status Code:</b> {{ $booking->payhere_status_code ?? '—' }}</div>
        <div><b>Status Message:</b> {{ $booking->payhere_status_message ?? '—' }}</div>
        <div><b>Payment Method:</b> {{ $booking->payhere_method ?? '—' }}</div>

        <hr class="my-2">

        <div>
          <b>Paid Amount:</b>
          {{ $booking->payhere_amount ?? '—' }}
          {{ $booking->payhere_currency ?? '' }}
        </div>

        <div><b>Card Number:</b> {{ $booking->payhere_card_no ?? '—' }}</div>
        <div><b>Card Holder:</b> {{ $booking->payhere_card_holder_name ?? '—' }}</div>

        <div class="mt-2 text-muted small">
          <b>PayHere md5sig:</b><br>
          <code>{{ $booking->payhere_md5sig ?? '—' }}</code>
        </div>

        <hr>

        <h5 class="mb-2">Ticket</h5>
        <div><b>Ticket No:</b> {{ $booking->ticket_no ?? '—' }}</div>
        <div><b>Email Sent At:</b> {{ $booking->ticket_sent_at ?? '—' }}</div>

        <div class="mt-3">
          <form method="post" action="{{ route('admin.booking.resend', $booking->id) }}">
            @csrf
            <button
              class="btn btn-primary w-100"
              @if($booking->status !== 'CONFIRMED') disabled @endif
            >
              Resend Ticket Email
            </button>

            @if($booking->status !== 'CONFIRMED')
              <div class="text-muted small mt-2">
                Only CONFIRMED bookings can resend tickets.
              </div>
            @endif
          </form>
        </div>

      </div>
    </div>
  </div>

</div>
@endsection
