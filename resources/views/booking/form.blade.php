@extends('layouts.public')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h4 class="mb-1">Book Your Tour</h4>
        <p class="text-muted mb-4">Fill the details and pay via PayHere.</p>

        {{-- Price info box --}}
        <div class="alert alert-info d-none" id="priceBox">
          <div class="d-flex justify-content-between">
            <div><b>Adult Price:</b> LKR <span id="adultPrice">0</span></div>
            <div><b>Child Price:</b> LKR <span id="childPrice">0</span></div>
          </div>
          <hr class="my-2">
          <div class="fw-bold fs-5">Total: LKR <span id="totalPrice">0</span></div>
          <div class="text-muted small">Kids are free</div>
        </div>

        {{-- Price error --}}
        <div class="alert alert-danger d-none" id="priceError"></div>

        <form method="post" action="{{ route('booking.store') }}">
          @csrf

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Tour Date *</label>
              <input type="date" name="tour_date" id="tour_date" class="form-control" required>
              <div class="form-text">Select date to load monthly prices.</div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Payment Option *</label>
              <select name="payment_option" class="form-select" required>
                <option value="Full">Full Payment</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Pickup Hotel</label>
              <input type="text" name="pickup_hotel" class="form-control" placeholder="Hotel name">
            </div>

            <hr class="my-2">

            <div class="col-md-4">
              <label class="form-label">Adults *</label>
              <input type="number" name="adults_qty" id="adults_qty" class="form-control" min="0" value="1" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Children *</label>
              <input type="number" name="children_qty" id="children_qty" class="form-control" min="0" value="0" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Kids (Free) *</label>
              <input type="number" name="kids_qty" id="kids_qty" class="form-control" min="0" value="0" required>
            </div>

            <hr class="my-2">

            <div class="col-md-6">
              <label class="form-label">Name *</label>
              <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email *</label>
              <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Mobile *</label>
              <input type="text" name="mobile" class="form-control" required>
            </div>

            <div class="col-md-12">
              <label class="form-label">Special Requests</label>
              <textarea name="special_requests" class="form-control" rows="3" placeholder="Any notes..."></textarea>
            </div>

            <div class="col-12 d-grid">
              <button class="btn btn-primary btn-lg" id="payBtn" disabled>
                Book & Pay
              </button>
              <div class="text-muted small mt-2">
                * Select a date first to load prices.
              </div>
            </div>

            <div class="col-12">
              <small class="text-muted">
                Note: Monthly prices must be set in admin panel (Monthly Prices) or phpMyAdmin.
              </small>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<script>
  let adultPrice = 0;
  let childPrice = 0;

  const tourDateEl = document.getElementById('tour_date');
  const adultsEl = document.getElementById('adults_qty');
  const childrenEl = document.getElementById('children_qty');

  const priceBox = document.getElementById('priceBox');
  const priceError = document.getElementById('priceError');

  const adultPriceEl = document.getElementById('adultPrice');
  const childPriceEl = document.getElementById('childPrice');
  const totalPriceEl = document.getElementById('totalPrice');

  const payBtn = document.getElementById('payBtn');

  function formatMoney(n) {
    return Number(n || 0).toLocaleString(undefined, { maximumFractionDigits: 2 });
  }

  function calculateTotal() {
    const adults = parseInt(adultsEl.value || 0, 10);
    const children = parseInt(childrenEl.value || 0, 10);
    const total = (adults * adultPrice) + (children * childPrice);
    totalPriceEl.innerText = formatMoney(total);
  }

  function setPriceAvailable(isAvailable, message = '') {
    if (isAvailable) {
      priceError.classList.add('d-none');
      priceBox.classList.remove('d-none');
      payBtn.disabled = false;
    } else {
      priceBox.classList.add('d-none');
      priceError.classList.remove('d-none');
      priceError.innerText = message || 'Prices not available for selected date.';
      payBtn.disabled = true;
    }
  }

  async function fetchPricesForDate(dateStr) {
    // show loading state
    payBtn.disabled = true;
    priceBox.classList.add('d-none');
    priceError.classList.add('d-none');

    try {
      const res = await fetch(`/get-month-price?date=${encodeURIComponent(dateStr)}`);
      const data = await res.json();

      if (!data.available) {
        setPriceAvailable(false, data.message);
        return;
      }

      adultPrice = Number(data.adult_price || 0);
      childPrice = Number(data.child_price || 0);

      adultPriceEl.innerText = formatMoney(adultPrice);
      childPriceEl.innerText = formatMoney(childPrice);

      setPriceAvailable(true);
      calculateTotal();
    } catch (e) {
      setPriceAvailable(false, 'Error loading prices. Please try again.');
    }
  }

  tourDateEl.addEventListener('change', function () {
    const dateStr = this.value;
    if (!dateStr) {
      setPriceAvailable(false, 'Please select a date.');
      return;
    }
    fetchPricesForDate(dateStr);
  });

  adultsEl.addEventListener('input', calculateTotal);
  childrenEl.addEventListener('input', calculateTotal);

  // On page load: disable until date chosen
  setPriceAvailable(false, 'Please select a date to load prices.');
</script>
@endsection
