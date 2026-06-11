@extends('layouts.frontend.app')
@section('title','My Addresses')
@section('content')
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-800 mb-0">Saved Addresses</h2>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
        <i class="bi bi-plus me-1"></i> Add Address
      </button>
    </div>
    @if($addresses->isEmpty())
    <div class="text-center py-5"><div style="font-size:60px">📍</div><p class="text-muted mt-3">No addresses saved yet.</p></div>
    @else
    <div class="row g-3">
      @foreach($addresses as $address)
      <div class="col-12 col-md-6">
        <div class="card h-100 {{ $address->is_default?'border-primary':'' }}">
          <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
              <span class="badge bg-secondary-subtle text-secondary">{{ $address->label }}</span>
              @if($address->is_default)<span class="badge bg-primary">Default</span>@endif
            </div>
            <p class="fw-700 mb-1">{{ $address->name }}</p>
            <p class="text-muted mb-1">📞 {{ $address->phone }}</p>
            <p class="text-muted mb-0">{{ $address->address }}, {{ $address->city }}{{ $address->state?', '.$address->state:'' }}, {{ $address->country }}</p>
          </div>
          <div class="card-footer d-flex gap-2">
            <form action="{{ route('account.addresses.delete',$address) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>
</section>

<div class="modal fade" id="addAddressModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title fw-700">Add New Address</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="{{ route('account.addresses.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-600">Label</label>
              <input type="text" name="label" class="form-control" value="Home" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Full Name</label>
              <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required></div>
            <div class="col-12"><label class="form-label fw-600">Phone</label>
              <input type="text" name="phone" class="form-control" required></div>
            <div class="col-12"><label class="form-label fw-600">Address</label>
              <input type="text" name="address" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label fw-600">City</label>
              <input type="text" name="city" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label fw-600">State</label>
              <input type="text" name="state" class="form-control"></div>
            <div class="col-12"><div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_default" value="1" id="default_addr">
              <label class="form-check-label" for="default_addr">Set as default</label>
            </div></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Address</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
