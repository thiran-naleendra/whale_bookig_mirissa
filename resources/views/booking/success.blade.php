@extends('layouts.public')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="alert alert-success shadow-sm">
      <h4 class="mb-1">Payment Submitted ✅</h4>
      <p class="mb-0">
        If payment is successful, your booking will be confirmed and the ticket will be emailed to you.
      </p>
      <hr>
      <p class="mb-0 text-muted">
        (Confirmation is done by PayHere notify callback.)
      </p>
    </div>
  </div>
</div>
@endsection
