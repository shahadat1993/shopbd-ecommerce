@extends('layouts.admin.app')
@section('title', isset($coupon)?'Edit Coupon':'New Coupon')
@section('page_title', isset($coupon)?'Edit Coupon':'New Coupon')
@section('content')
@php $action = isset($coupon)?route('admin.coupons.update',$coupon):route('admin.coupons.store'); @endphp
<div class="row justify-content-center">
<div class="col-12 col-xl-7">
<div class="card">
  <div class="card-body">
    <form action="{{ $action }}" method="POST">
      @csrf @if(isset($coupon)) @method('PUT') @endif
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
          <input type="text" name="code" class="form-control font-monospace text-uppercase @error('code') is-invalid @enderror"
            value="{{ old('code',$coupon->code??'') }}" placeholder="e.g. SAVE20" required>
          @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Discount Type <span class="text-danger">*</span></label>
          <select name="type" class="form-select">
            <option value="percentage" {{ old('type',$coupon->type??'percentage')==='percentage'?'selected':'' }}>Percentage (%)</option>
            <option value="fixed" {{ old('type',$coupon->type??'')==='fixed'?'selected':'' }}>Fixed Amount (৳)</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Value <span class="text-danger">*</span></label>
          <input type="number" name="value" class="form-control" value="{{ old('value',$coupon->value??'') }}" step="0.01" min="0" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Min Order Amount</label>
          <div class="input-group"><span class="input-group-text">৳</span>
          <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount',$coupon->min_order_amount??0) }}" min="0">
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label">Max Uses</label>
          <input type="number" name="max_uses" class="form-control" value="{{ old('max_uses',$coupon->max_uses??'') }}" min="1" placeholder="Unlimited">
        </div>
        <div class="col-md-6">
          <label class="form-label">Start Date</label>
          <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at',$coupon->starts_at?$coupon->starts_at->format('Y-m-d\TH:i'):'') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Expiry Date</label>
          <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at',$coupon->expires_at?$coupon->expires_at->format('Y-m-d\TH:i'):'') }}">
        </div>
        <div class="col-12">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active',$coupon->is_active??true)?'checked':'' }}>
            <label class="form-check-label">Active</label>
          </div>
        </div>
      </div>
      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
</div>
</div>
@endsection
