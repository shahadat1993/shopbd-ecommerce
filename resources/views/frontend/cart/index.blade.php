@extends('layouts.frontend.app')
@section('title', 'Shopping Cart')

@section('content')
<div class="breadcrumb-section">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Cart</li>
      </ol>
    </nav>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <h2 class="fw-800 mb-4">Shopping Cart
      @if($cartItems->count() > 0)
        <span class="badge bg-primary ms-2 fs-6">{{ $cartItems->sum('qty') }} items</span>
      @endif
    </h2>

    @if($cartItems->isEmpty())
    <div class="text-center py-5">
      <div style="font-size:80px;margin-bottom:20px">🛒</div>
      <h4 class="text-muted fw-600 mb-2">Your cart is empty</h4>
      <p class="text-muted mb-4">Looks like you haven't added anything yet.</p>
      <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg px-5">Start Shopping</a>
    </div>
    @else
    <div class="row g-4">

      {{-- Cart Items --}}
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-body p-0">
            @foreach($cartItems as $item)
            <div class="d-flex gap-3 p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
              {{-- Image --}}
              <a href="{{ route('products.show', $item->product) }}" class="flex-shrink-0">
                <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}"
                  class="rounded-3" style="width:90px;height:90px;object-fit:cover">
              </a>
              {{-- Details --}}
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <a href="{{ route('products.show', $item->product) }}" class="text-decoration-none text-dark fw-600">
                      {{ $item->product->name }}
                    </a>
                    @if($item->variant)
                      <p class="text-muted small mb-1">
                        {{ implode(', ', array_map(fn($k,$v)=>"$k: $v", array_keys($item->variant), $item->variant)) }}
                      </p>
                    @endif
                    <p class="text-muted small mb-0">Unit Price: ৳{{ number_format($item->price, 0) }}</p>
                  </div>
                  <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Remove">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </form>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                  {{-- Qty Updater --}}
                  <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center">
                    @csrf @method('PUT')
                    <div class="d-flex align-items-center border rounded-3 overflow-hidden">
                      <button type="submit" name="qty" value="{{ max(1, $item->qty - 1) }}" class="btn border-0 px-3 py-1">−</button>
                      <span class="px-3 fw-600">{{ $item->qty }}</span>
                      <button type="submit" name="qty" value="{{ $item->qty + 1 }}" class="btn border-0 px-3 py-1">+</button>
                    </div>
                  </form>
                  <span class="fw-700 fs-5" style="color:#6c3fe0">৳{{ number_format($item->subtotal, 0) }}</span>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-arrow-left me-1"></i> Continue Shopping
            </a>
            <span class="text-muted small">{{ $cartItems->count() }} item(s) in cart</span>
          </div>
        </div>
      </div>

      {{-- Order Summary --}}
      <div class="col-12 col-lg-4">
        <div class="card">
          <div class="card-header fw-700 fs-6">Order Summary</div>
          <div class="card-body">

            {{-- Coupon --}}
            <div class="mb-3">
              <label class="form-label fw-600 small">Have a coupon?</label>
              @if($couponCode)
              <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#e8f8ee;border:1px solid #b8e6cc">
                <i class="bi bi-tag-fill text-success"></i>
                <span class="fw-600 text-success">{{ $couponCode }}</span>
                <span class="ms-auto text-success small">-৳{{ number_format($couponDiscount, 0) }}</span>
                <form action="{{ route('cart.remove.coupon') }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-1">✕</button>
                </form>
              </div>
              @else
              <form action="{{ route('cart.apply.coupon') }}" method="POST">
                @csrf
                <div class="input-group">
                  <input type="text" name="coupon_code" class="form-control" placeholder="Enter coupon code" style="text-transform:uppercase">
                  <button type="submit" class="btn btn-outline-primary">Apply</button>
                </div>
              </form>
              @endif
            </div>

            <hr>

            {{-- Totals --}}
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Subtotal</span>
              <span class="fw-600">৳{{ number_format($subtotal, 0) }}</span>
            </div>
            @if($couponDiscount > 0)
            <div class="d-flex justify-content-between mb-2 text-success">
              <span>Coupon Discount</span>
              <span class="fw-600">-৳{{ number_format($couponDiscount, 0) }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Shipping</span>
              @if($shipping == 0)
                <span class="fw-600 text-success">FREE</span>
              @else
                <span class="fw-600">৳{{ number_format($shipping, 0) }}</span>
              @endif
            </div>
            @if($shipping > 0)
            <p class="text-muted" style="font-size:11px">Add ৳{{ number_format(1000 - $subtotal, 0) }} more for free shipping</p>
            @endif
            <hr>
            <div class="d-flex justify-content-between mb-4">
              <span class="fw-800 fs-5">Total</span>
              <span class="fw-800 fs-5" style="color:#6c3fe0">৳{{ number_format($total, 0) }}</span>
            </div>

            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg w-100 fw-600">
              Proceed to Checkout <i class="bi bi-arrow-right ms-1"></i>
            </a>

            {{-- Payment Icons --}}
            <div class="text-center mt-3">
              <p class="text-muted small mb-2">We accept</p>
              <div class="d-flex justify-content-center gap-2 flex-wrap">
                <span class="badge bg-light text-dark border">💳 Stripe</span>
                <span class="badge bg-light text-dark border">🏦 SSLCommerz</span>
                <span class="badge bg-light text-dark border">💵 COD</span>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
    @endif
  </div>
</section>
@endsection
