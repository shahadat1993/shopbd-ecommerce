@extends('layouts.admin.app')
@section('title','Shipping Settings')
@section('page_title','Shipping Settings')
@section('content')
<div class="row">
<div class="col-12 col-xl-7">
<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.settings.shipping.update') }}" method="POST">
      @csrf
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="free_shipping_enabled" value="1" {{ ($settings['free_shipping_enabled']??'1')==='1'?'checked':'' }}>
        <label class="form-check-label fw-semibold">Free Shipping</label>
      </div>
      <div class="mb-4">
        <label class="form-label">Free Shipping Minimum Order</label>
        <div class="input-group"><span class="input-group-text">৳</span>
        <input type="number" name="free_shipping_min" class="form-control" value="{{ $settings['free_shipping_min'] ?? 1000 }}" min="0">
        </div>
      </div>
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="flat_rate_enabled" value="1" {{ ($settings['flat_rate_enabled']??'1')==='1'?'checked':'' }}>
        <label class="form-check-label fw-semibold">Flat Rate Shipping</label>
      </div>
      <div class="mb-4">
        <label class="form-label">Flat Rate Amount</label>
        <div class="input-group"><span class="input-group-text">৳</span>
        <input type="number" name="flat_rate_amount" class="form-control" value="{{ $settings['flat_rate_amount'] ?? 80 }}" min="0">
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save Shipping Settings</button>
    </form>
  </div>
</div>
</div>
</div>
@endsection
