@extends('layouts.admin.app')
@section('title','Email Settings')
@section('page_title','Email Settings')
@section('content')
<div class="row">
<div class="col-12 col-xl-8">
<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.settings.email.update') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Mail Driver</label>
          <select name="mail_driver" class="form-select">
            @foreach(['smtp','mailgun','ses','postmark','log','array'] as $d)
              <option value="{{ $d }}" {{ ($settings['mail_driver']??'smtp')===$d?'selected':'' }}>{{ strtoupper($d) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">SMTP Host</label>
          <input type="text" name="mail_host" class="form-control" value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}">
        </div>
        <div class="col-md-2">
          <label class="form-label">Port</label>
          <input type="number" name="mail_port" class="form-control" value="{{ $settings['mail_port'] ?? 587 }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Username</label>
          <input type="text" name="mail_username" class="form-control" value="{{ $settings['mail_username'] ?? '' }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Password</label>
          <input type="password" name="mail_password" class="form-control" value="{{ $settings['mail_password'] ?? '' }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Encryption</label>
          <select name="mail_encryption" class="form-select">
            @foreach(['tls','ssl','null'] as $e)
              <option value="{{ $e }}" {{ ($settings['mail_encryption']??'tls')===$e?'selected':'' }}>{{ strtoupper($e) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">From Address</label>
          <input type="email" name="mail_from_address" class="form-control" value="{{ $settings['mail_from_address'] ?? '' }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">From Name</label>
          <input type="text" name="mail_from_name" class="form-control" value="{{ $settings['mail_from_name'] ?? 'ShopBD' }}">
        </div>
      </div>
      <button type="submit" class="btn btn-primary mt-4"><i class="bx bx-save me-1"></i> Save Email Settings</button>
    </form>
  </div>
</div>
</div>
</div>
@endsection
