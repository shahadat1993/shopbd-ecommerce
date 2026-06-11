@extends('layouts.frontend.app')
@section('title', 'Order Placed Successfully')
@section('content')
<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6 text-center">
        <div class="mb-4" style="font-size:80px">🎉</div>
        <h2 class="fw-800 mb-2" style="color:#6c3fe0">Order Placed!</h2>
        <p class="text-muted mb-4">Thank you for your order. We'll process it right away.</p>
        <div class="card mb-4">
          <div class="card-body">
            <div class="row g-3 text-start">
              <div class="col-6"><span class="text-muted small">Order Number</span><p class="fw-700 mb-0">#{{ $order->order_number }}</p></div>
              <div class="col-6"><span class="text-muted small">Total Amount</span><p class="fw-700 mb-0" style="color:#6c3fe0">৳{{ number_format($order->total,0) }}</p></div>
              <div class="col-6"><span class="text-muted small">Payment Method</span><p class="fw-600 mb-0 text-capitalize">{{ $order->payment_method }}</p></div>
              <div class="col-6"><span class="text-muted small">Status</span><p class="mb-0"><span class="badge bg-label-{{ $order->status_badge }} text-capitalize">{{ $order->status }}</span></p></div>
            </div>
          </div>
        </div>
        <div class="d-flex gap-2 justify-content-center">
          <a href="{{ route('account.orders') }}" class="btn btn-primary px-4">Track Order</a>
          <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">Continue Shopping</a>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
