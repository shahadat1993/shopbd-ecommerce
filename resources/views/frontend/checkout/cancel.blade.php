@extends('layouts.frontend.app')
@section('title','Payment Cancelled')
@section('content')
<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6 text-center">
        <div style="font-size:72px;margin-bottom:20px">❌</div>
        <h2 class="fw-800 mb-2">Payment Cancelled</h2>
        <p class="text-muted mb-4">Your payment was cancelled or failed. Your cart items are still saved.</p>
        <div class="d-flex gap-2 justify-content-center">
          <a href="{{ route('checkout.index') }}" class="btn btn-primary">Try Again</a>
          <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">Back to Cart</a>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
