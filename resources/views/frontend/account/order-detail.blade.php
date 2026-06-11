@extends('layouts.frontend.app')
@section('title','Order #'.$order->order_number)
@section('content')
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h2 class="fw-800 mb-1">Order #{{ $order->order_number }}</h2>
        <p class="text-muted mb-0">Placed on {{ $order->created_at->format('d M Y, H:i') }}</p>
      </div>
      <div class="d-flex gap-2">
        <span class="badge bg-label-{{ $order->status_badge }} fs-6 text-capitalize">{{ $order->status }}</span>
        <span class="badge bg-label-{{ $order->payment_status_badge }} fs-6 text-capitalize">{{ $order->payment_status }}</span>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-12 col-lg-8">
        <div class="card mb-4">
          <div class="card-header fw-700">Items Ordered</div>
          <div class="card-body p-0">
            @foreach($order->items as $item)
            <div class="d-flex gap-3 p-4 {{ !$loop->last?'border-bottom':'' }}">
              <img src="{{ $item->thumbnail?asset('storage/'.$item->thumbnail):asset('images/placeholder-product.png') }}" class="rounded-3" style="width:80px;height:80px;object-fit:cover">
              <div class="flex-grow-1">
                <p class="fw-700 mb-1">{{ $item->product_name }}</p>
                <p class="text-muted small mb-1">৳{{ number_format($item->price,0) }} × {{ $item->qty }}</p>
              </div>
              <span class="fw-700" style="color:#6c3fe0">৳{{ number_format($item->subtotal,0) }}</span>
            </div>
            @endforeach
          </div>
          <div class="card-footer">
            <div class="row justify-content-end">
              <div class="col-12 col-sm-5">
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Subtotal</span><span>৳{{ number_format($order->subtotal,0) }}</span></div>
                @if($order->coupon_discount>0)<div class="d-flex justify-content-between mb-1 text-success"><span>Coupon</span><span>-৳{{ number_format($order->coupon_discount,0) }}</span></div>@endif
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Shipping</span><span>৳{{ number_format($order->shipping_charge,0) }}</span></div>
                <hr>
                <div class="d-flex justify-content-between fw-800"><span>Total</span><span style="color:#6c3fe0">৳{{ number_format($order->total,0) }}</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card mb-4">
          <div class="card-header fw-700">Shipping Address</div>
          <div class="card-body">
            <p class="fw-600 mb-1">{{ $order->shipping_name }}</p>
            <p class="text-muted mb-1">{{ $order->shipping_phone }}</p>
            <p class="text-muted mb-0">{{ $order->shipping_address }}, {{ $order->shipping_city }}, {{ $order->shipping_country }}</p>
          </div>
        </div>
        <div class="card">
          <div class="card-header fw-700">Payment Info</div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Method</span><span class="badge bg-label-info text-uppercase">{{ $order->payment_method }}</span></div>
            <div class="d-flex justify-content-between"><span class="text-muted">Status</span><span class="badge bg-label-{{ $order->payment_status_badge }} text-capitalize">{{ $order->payment_status }}</span></div>
          </div>
        </div>
      </div>
    </div>
    <a href="{{ route('account.orders') }}" class="btn btn-outline-secondary mt-3"><i class="bi bi-arrow-left me-1"></i> Back to Orders</a>
  </div>
</section>
@endsection
