@extends('layouts.frontend.app')
@section('title','My Orders')
@section('content')
<section class="py-5">
  <div class="container">
    <h2 class="fw-800 mb-4">My Orders</h2>
    @if($orders->isEmpty())
    <div class="text-center py-5"><div style="font-size:72px">📦</div><p class="text-muted mt-3">No orders found.</p><a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Shop Now</a></div>
    @else
    <div class="d-flex flex-column gap-3">
      @foreach($orders as $order)
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <span class="fw-700">Order #{{ $order->order_number }}</span>
              <span class="text-muted small ms-2">{{ $order->created_at->format('d M Y') }}</span>
            </div>
            <div class="d-flex gap-2">
              <span class="badge bg-label-{{ $order->status_badge }} text-capitalize">{{ $order->status }}</span>
              <span class="badge bg-label-{{ $order->payment_status_badge }} text-capitalize">{{ $order->payment_status }}</span>
            </div>
          </div>
          <div class="d-flex gap-2 mb-3 flex-wrap">
            @foreach($order->items->take(3) as $item)
            <img src="{{ $item->thumbnail?asset('storage/'.$item->thumbnail):asset('images/placeholder-product.png') }}" class="rounded-2" style="width:50px;height:50px;object-fit:cover" alt="{{ $item->product_name }}">
            @endforeach
            @if($order->items->count() > 3)<span class="badge bg-secondary-subtle text-secondary align-self-center">+{{ $order->items->count()-3 }} more</span>@endif
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <span class="fw-700 fs-5" style="color:#6c3fe0">৳{{ number_format($order->total,0) }}</span>
            <a href="{{ route('account.orders.show',$order) }}" class="btn btn-outline-primary btn-sm">View Details</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $orders->links() }}</div>
    @endif
  </div>
</section>
@endsection
