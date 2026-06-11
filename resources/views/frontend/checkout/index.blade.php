@extends('layouts.frontend.app')
@section('title', 'Checkout')

@section('content')
    <div class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Cart</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <h2 class="fw-800 mb-4">Checkout</h2>

            <form action="{{ route('checkout.place.order') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="row g-4">

                    {{-- ── Left: Shipping & Payment ── --}}
                    <div class="col-12 col-lg-7">

                        {{-- Saved Addresses --}}
                        @if ($addresses->count() > 0)
                            <div class="card mb-4">
                                <div class="card-header fw-700">Saved Addresses</div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        @foreach ($addresses as $address)
                                            <div class="col-12">
                                                <div class="border rounded-3 p-3 cursor-pointer address-card {{ $address->is_default ? 'border-primary' : '' }}"
                                                    data-name="{{ $address->name }}" data-phone="{{ $address->phone }}"
                                                    data-email="{{ auth()->user()->email }}"
                                                    data-address="{{ $address->address }}" data-city="{{ $address->city }}"
                                                    data-state="{{ $address->state }}" data-zip="{{ $address->zip }}"
                                                    onclick="fillAddress(this)">
                                                    <div class="d-flex justify-content-between">
                                                        <span
                                                            class="badge bg-secondary-subtle text-secondary">{{ $address->label }}</span>
                                                        @if ($address->is_default)
                                                            <span class="badge bg-primary">Default</span>
                                                        @endif
                                                    </div>
                                                    <p class="mb-0 mt-1 fw-600">{{ $address->name }}</p>
                                                    <p class="mb-0 text-muted small">{{ $address->address }},
                                                        {{ $address->city }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Shipping Form --}}
                        <div class="card mb-4">
                            <div class="card-header fw-700"><i class="bi bi-geo-alt me-2"></i>Shipping Information</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-600">Full Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="name" id="f_name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $defaultAddress?->name ?? auth()->user()->name) }}"
                                            required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-600">Phone Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="phone" id="f_phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone', $defaultAddress?->phone ?? auth()->user()->phone) }}"
                                            required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-600">Email Address <span
                                                class="text-danger">*</span></label>
                                        <input type="email" name="email" id="f_email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', auth()->user()->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-600">Street Address <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="address" id="f_address"
                                            class="form-control @error('address') is-invalid @enderror"
                                            value="{{ old('address', $defaultAddress?->address) }}"
                                            placeholder="House No., Road, Area" required>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-600">City <span class="text-danger">*</span></label>
                                        <input type="text" name="city" id="f_city"
                                            class="form-control @error('city') is-invalid @enderror"
                                            value="{{ old('city', $defaultAddress?->city) }}" required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-600">District/State</label>
                                        <input type="text" name="state" id="f_state" class="form-control"
                                            value="{{ old('state', $defaultAddress?->state) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-600">ZIP Code</label>
                                        <input type="text" name="zip" id="f_zip" class="form-control"
                                            value="{{ old('zip', $defaultAddress?->zip) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-600">Order Notes</label>
                                        <textarea name="notes" class="form-control" rows="2" placeholder="Any special instructions..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="card">
                            <div class="card-header fw-700"><i class="bi bi-credit-card me-2"></i>Payment Method</div>
                            <div class="card-body">

                                @php
                                    $codEnabled = \App\Models\Setting::get('cod_enabled', '1') === '1';
                                    $stripeEnabled = \App\Models\Setting::get('stripe_enabled', '1') === '1';
                                    $sslEnabled = \App\Models\Setting::get('sslcommerz_enabled', '1') === '1';
                                @endphp

                                @if ($codEnabled)
                                    <div class="form-check p-3 mb-2 border rounded-3 {{ !$stripeEnabled && !$sslEnabled ? 'border-primary bg-primary-subtle' : '' }}"
                                        style="cursor:pointer" onclick="selectPayment(this,'cod')">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            value="cod" id="pay_cod" @checked((!$stripeEnabled && !$sslEnabled) || old('payment_method') === 'cod')>
                                        <label class="form-check-label d-flex align-items-center gap-2 cursor-pointer"
                                            for="pay_cod" style="cursor:pointer">
                                            <span style="font-size:22px">💵</span>
                                            <div>
                                                <span class="fw-600">Cash on Delivery</span>
                                                <p class="mb-0 text-muted small">Pay when your order arrives</p>
                                            </div>
                                        </label>
                                    </div>
                                @endif

                                @if ($stripeEnabled)
                                    <div class="form-check p-3 mb-2 border rounded-3" style="cursor:pointer"
                                        onclick="selectPayment(this,'stripe')">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            value="stripe" id="pay_stripe"
                                            {{ old('payment_method') === 'stripe' ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center gap-2 cursor-pointer"
                                            for="pay_stripe" style="cursor:pointer">
                                            <span style="font-size:22px">💳</span>
                                            <div>
                                                <span class="fw-600">Credit / Debit Card (Stripe)</span>
                                                <p class="mb-0 text-muted small">Visa, Mastercard, American Express</p>
                                            </div>
                                        </label>
                                    </div>
                                @endif

                                @if ($sslEnabled)
                                    <div class="form-check p-3 border rounded-3" style="cursor:pointer"
                                        onclick="selectPayment(this,'sslcommerz')">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            value="sslcommerz" id="pay_ssl"
                                            {{ old('payment_method') === 'sslcommerz' ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center gap-2 cursor-pointer"
                                            for="pay_ssl" style="cursor:pointer">
                                            <span style="font-size:22px">🏦</span>
                                            <div>
                                                <span class="fw-600">SSLCommerz (Bangladesh)</span>
                                                <p class="mb-0 text-muted small">bKash, Nagad, Rocket, VISA, MasterCard</p>
                                            </div>
                                        </label>
                                    </div>
                                @endif

                                @error('payment_method')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>

                    {{-- ── Right: Order Summary ── --}}
                    <div class="col-12 col-lg-5">
                        <div class="card position-sticky" style="top:80px">
                            <div class="card-header fw-700">Order Summary</div>
                            <div class="card-body">
                                {{-- Items --}}
                                @foreach ($cartItems as $item)
                                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                        <div class="position-relative flex-shrink-0">
                                            <img src="{{ $item->product->thumbnail_url }}" class="rounded-2"
                                                style="width:56px;height:56px;object-fit:cover"
                                                alt="{{ $item->product->name }}">
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">{{ $item->qty }}</span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0 fw-600 small">{{ Str::limit($item->product->name, 35) }}</p>
                                            <p class="mb-0 text-muted small">৳{{ number_format($item->price, 0) }} ×
                                                {{ $item->qty }}</p>
                                        </div>
                                        <span class="fw-600 small">৳{{ number_format($item->subtotal, 0) }}</span>
                                    </div>
                                @endforeach

                                {{-- Totals --}}
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span class="fw-600">৳{{ number_format($subtotal, 0) }}</span>
                                </div>
                                @if ($couponDiscount > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>Coupon</span>
                                        <span class="fw-600">-৳{{ number_format($couponDiscount, 0) }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Shipping</span>
                                    @if ($shipping == 0)
                                        <span class="fw-600 text-success">FREE</span>
                                    @else
                                        <span class="fw-600">৳{{ number_format($shipping, 0) }}</span>
                                    @endif
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-4">
                                    <span class="fw-800 fs-5">Total</span>
                                    <span class="fw-800 fs-5"
                                        style="color:#6c3fe0">৳{{ number_format($total, 0) }}</span>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-600">
                                    <i class="bi bi-bag-check me-2"></i>Place Order
                                </button>

                                <p class="text-center text-muted mt-3 mb-0" style="font-size:12px">
                                    <i class="bi bi-shield-lock me-1"></i>Your payment info is secure & encrypted
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function fillAddress(el) {
            document.querySelectorAll('.address-card').forEach(c => c.classList.remove('border-primary'));
            el.classList.add('border-primary');
            document.getElementById('f_name').value = el.dataset.name;
            document.getElementById('f_phone').value = el.dataset.phone;
            document.getElementById('f_email').value = el.dataset.email;
            document.getElementById('f_address').value = el.dataset.address;
            document.getElementById('f_city').value = el.dataset.city;
            document.getElementById('f_state').value = el.dataset.state || '';
            document.getElementById('f_zip').value = el.dataset.zip || '';
        }

        function selectPayment(el, val) {
            document.querySelectorAll('.form-check[onclick]').forEach(c => {
                c.classList.remove('border-primary', 'bg-primary-subtle');
            });
            el.classList.add('border-primary', 'bg-primary-subtle');
            document.getElementById('pay_' + val).checked = true;
        }
    </script>
@endpush
