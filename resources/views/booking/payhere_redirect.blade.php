@extends('layouts.public')

@section('content')
<div class="text-center">
    <h4>Redirecting to PayHere...</h4>
    <p>Please wait</p>

    @php
        // ✅ Required values
        $merchantId = env('PAYHERE_MERCHANT_ID');
        $merchantSecret = env('PAYHERE_MERCHANT_SECRET');

        $orderId = $booking->order_id;
        $amount = number_format($booking->total, 2, '.', ''); // IMPORTANT: 2 decimals
        $currency = env('PAYHERE_CURRENCY', 'LKR');

        // ✅ hash required by PayHere Checkout API
        // hash = strtoupper(md5(merchant_id + order_id + amount + currency + strtoupper(md5(merchant_secret))))
        $hash = strtoupper(md5($merchantId . $orderId . $amount . $currency . strtoupper(md5($merchantSecret))));

        $baseUrl = rtrim(env('PAYHERE_BASE_URL', 'https://sandbox.payhere.lk'), '/');
        $actionUrl = $baseUrl . '/pay/checkout';
    @endphp

    <form method="post" action="{{ $actionUrl }}" id="payhereForm">
        <input type="hidden" name="merchant_id" value="{{ $merchantId }}">
        <input type="hidden" name="return_url" value="{{ env('PAYHERE_RETURN_URL') }}">
        <input type="hidden" name="cancel_url" value="{{ env('PAYHERE_CANCEL_URL') }}">
        <input type="hidden" name="notify_url" value="{{ env('PAYHERE_NOTIFY_URL') }}">

        <input type="hidden" name="order_id" value="{{ $orderId }}">
        <input type="hidden" name="items" value="Whale Watching Booking">
        <input type="hidden" name="currency" value="{{ $currency }}">
        <input type="hidden" name="amount" value="{{ $amount }}">

        <!-- ✅ REQUIRED -->
        <input type="hidden" name="hash" value="{{ $hash }}">

        <input type="hidden" name="first_name" value="{{ $booking->name }}">
        <input type="hidden" name="last_name" value="">
        <input type="hidden" name="email" value="{{ $booking->email }}">
        <input type="hidden" name="phone" value="{{ $booking->mobile }}">
        <input type="hidden" name="address" value="{{ $booking->pickup_hotel ?? 'N/A' }}">
        <input type="hidden" name="city" value="Mirissa">
        <input type="hidden" name="country" value="Sri Lanka">
    </form>

    <script>
        document.getElementById('payhereForm').submit();
    </script>

    <div class="text-muted small mt-3">
        If you see PH-0012, check PAYHERE_BASE_URL (sandbox/live), merchant id/secret, and notify_url must be public.
    </div>
</div>
@endsection
