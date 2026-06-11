@extends('layouts.admin.app')
@section('title','Payment Settings')
@section('page_title','Payment Settings')
@section('content')
<div class="row">
<div class="col-12 col-xl-8">
<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.settings.payment.update') }}" method="POST">
      @csrf
      <h6 class="text-uppercase text-muted small fw-bold mb-3">Cash on Delivery</h6>
      <div class="form-check form-switch mb-4">
        <input class="form-check-input" type="checkbox" name="cod_enabled" value="1" {{ ($settings['cod_enabled']??'1')==='1'?'checked':'' }}>
        <label class="form-check-label">Enable COD</label>
      </div>
      <hr>
      <h6 class="text-uppercase text-muted small fw-bold mb-3">Stripe (International)</h6>
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="stripe_enabled" value="1" {{ ($settings['stripe_enabled']??'1')==='1'?'checked':'' }}>
        <label class="form-check-label">Enable Stripe</label>
      </div>
      <div class="mb-3">
        <label class="form-label">Stripe Publishable Key</label>
        <input type="text" name="stripe_key" class="form-control font-monospace" value="{{ $settings['stripe_key'] ?? '' }}" placeholder="pk_test_...">
      </div>
      <div class="mb-4">
        <label class="form-label">Stripe Secret Key</label>
        <input type="password" name="stripe_secret" class="form-control font-monospace" value="{{ $settings['stripe_secret'] ?? '' }}" placeholder="sk_test_...">
      </div>
      <hr>
      <h6 class="text-uppercase text-muted small fw-bold mb-3">SSLCommerz (Bangladesh)</h6>
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="sslcommerz_enabled" value="1" {{ ($settings['sslcommerz_enabled']??'1')==='1'?'checked':'' }}>
        <label class="form-check-label">Enable SSLCommerz</label>
      </div>
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="sslcommerz_sandbox" value="1" {{ ($settings['sslcommerz_sandbox']??'1')==='1'?'checked':'' }}>
        <label class="form-check-label">Sandbox Mode</label>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Store ID</label>
          <input type="text" name="sslcommerz_store_id" class="form-control" value="{{ $settings['sslcommerz_store_id'] ?? '' }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Store Password</label>
          <input type="password" name="sslcommerz_store_password" class="form-control" value="{{ $settings['sslcommerz_store_password'] ?? '' }}">
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save Payment Settings</button>
    </form>
  </div>
</div>
</div>
</div>
@endsection
