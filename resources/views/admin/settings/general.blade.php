@extends('layouts.admin.app')
@section('title','General Settings')
@section('page_title','General Settings')
@section('content')
<div class="row">
<div class="col-12 col-xl-8">
<div class="card">
  <div class="card-header"><h5 class="mb-0">General Settings</h5></div>
  <div class="card-body">
    <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Site Name</label>
          <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] ?? 'ShopBD' }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Tagline</label>
          <input type="text" name="site_tagline" class="form-control" value="{{ $settings['site_tagline'] ?? '' }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Contact Email</label>
          <input type="email" name="site_email" class="form-control" value="{{ $settings['site_email'] ?? '' }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Contact Phone</label>
          <input type="text" name="site_phone" class="form-control" value="{{ $settings['site_phone'] ?? '' }}">
        </div>
        <div class="col-12">
          <label class="form-label">Address</label>
          <input type="text" name="site_address" class="form-control" value="{{ $settings['site_address'] ?? '' }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Currency Code</label>
          <input type="text" name="site_currency" class="form-control" value="{{ $settings['site_currency'] ?? 'BDT' }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Currency Symbol</label>
          <input type="text" name="site_currency_symbol" class="form-control" value="{{ $settings['site_currency_symbol'] ?? '৳' }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Logo</label>
          <input type="file" name="site_logo" class="form-control" accept="image/*">
        </div>
      </div>
      <button type="submit" class="btn btn-primary mt-4"><i class="bx bx-save me-1"></i> Save Settings</button>
    </form>
  </div>
</div>
</div>
</div>
@endsection
