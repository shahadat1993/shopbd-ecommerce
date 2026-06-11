@extends('layouts.admin.app')
@section('title','Order #'.$order->order_number)
@section('page_title','Order Details')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb breadcrumb-style1 mb-0">
  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
  <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
</ol></nav>
@endsection
@section('page_actions')
  <a href="{{ route('admin.orders.invoice',$order) }}" class="btn btn-outline-secondary" target="_blank">
    <i class="bx bx-printer me-1"></i> Print Invoice
  </a>
@endsection

@section('content')
<div class="row g-4">

  {{-- Order Items --}}
  <div class="col-12 col-xl-8">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
        <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr><th>Product</th><th>Price</th><th>Qty</th><th class="text-end">Subtotal</th></tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  @if($item->thumbnail)
                    <img src="{{ asset('storage/'.$item->thumbnail) }}" alt="{{ $item->product_name }}" class="rounded" style="width:50px;height:50px;object-fit:cover">
                  @endif
                  <div>
                    <p class="mb-0 fw-semibold">{{ $item->product_name }}</p>
                    @if($item->variant)
                      <small class="text-muted">{{ implode(', ', array_map(fn($k,$v)=>"$k: $v", array_keys($item->variant), $item->variant)) }}</small>
                    @endif
                    <small class="text-muted d-block">SKU: {{ $item->product_sku ?? 'N/A' }}</small>
                  </div>
                </div>
              </td>
              <td>৳{{ number_format($item->price,0) }}</td>
              <td>{{ $item->qty }}</td>
              <td class="text-end fw-semibold">৳{{ number_format($item->subtotal,0) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot class="table-light">
            <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">৳{{ number_format($order->subtotal,0) }}</td></tr>
            @if($order->coupon_discount > 0)
            <tr class="text-success"><td colspan="3" class="text-end">Coupon ({{ $order->coupon_code }})</td><td class="text-end">-৳{{ number_format($order->coupon_discount,0) }}</td></tr>
            @endif
            <tr><td colspan="3" class="text-end">Shipping</td><td class="text-end">৳{{ number_format($order->shipping_charge,0) }}</td></tr>
            <tr class="fw-bold fs-6"><td colspan="3" class="text-end">Total</td><td class="text-end text-primary">৳{{ number_format($order->total,0) }}</td></tr>
          </tfoot>
        </table>
      </div>
    </div>

    {{-- Admin Notes --}}
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Admin Notes</h5></div>
      <div class="card-body">
        <form action="{{ route('admin.orders.update',$order) }}" method="POST">
          @csrf @method('PUT')
          <textarea name="admin_notes" class="form-control mb-3" rows="3" placeholder="Internal notes (not visible to customer)">{{ $order->admin_notes }}</textarea>
          <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-save me-1"></i> Save Notes</button>
        </form>
        @if($order->notes)
          <hr>
          <p class="mb-1 fw-semibold text-muted small">Customer Note:</p>
          <p class="mb-0">{{ $order->notes }}</p>
        @endif
      </div>
    </div>
  </div>

  {{-- Sidebar --}}
  <div class="col-12 col-xl-4">

    {{-- Update Status --}}
    <div class="card mb-4">
      <div class="card-header"><h5 class="mb-0">Update Status</h5></div>
      <div class="card-body">
        <div class="mb-3">
          <span class="badge bg-label-{{ $order->status_badge }} text-capitalize fs-6 mb-2">{{ $order->status }}</span>
          <span class="ms-2 badge bg-label-{{ $order->payment_status_badge }} text-capitalize">{{ $order->payment_status }}</span>
        </div>
        <form action="{{ route('admin.orders.status',$order) }}" method="POST">
          @csrf
          <select name="status" class="form-select mb-3">
            @foreach(['pending','confirmed','processing','shipped','delivered','cancelled','refunded'] as $s)
              <option value="{{ $s }}" {{ $order->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary w-100"><i class="bx bx-refresh me-1"></i> Update Status</button>
        </form>
      </div>
    </div>

    {{-- Shipping Info --}}
    <div class="card mb-4">
      <div class="card-header"><h5 class="mb-0"><i class="bx bx-map me-1"></i> Shipping Address</h5></div>
      <div class="card-body">
        <p class="fw-semibold mb-1">{{ $order->shipping_name }}</p>
        <p class="mb-1">{{ $order->shipping_phone }}</p>
        <p class="mb-1">{{ $order->shipping_email }}</p>
        <p class="mb-1">{{ $order->shipping_address }}</p>
        <p class="mb-0">{{ $order->shipping_city }}{{ $order->shipping_state ? ', '.$order->shipping_state : '' }}{{ $order->shipping_zip ? ' - '.$order->shipping_zip : '' }}</p>
        <p class="mb-0">{{ $order->shipping_country }}</p>
      </div>
    </div>

    {{-- Payment Info --}}
    <div class="card mb-4">
      <div class="card-header"><h5 class="mb-0"><i class="bx bx-credit-card me-1"></i> Payment Info</h5></div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Method</span>
          <span class="badge bg-label-info text-uppercase">{{ $order->payment_method }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Status</span>
          <span class="badge bg-label-{{ $order->payment_status_badge }} text-capitalize">{{ $order->payment_status }}</span>
        </div>
        @if($order->transaction_id)
        <div class="d-flex justify-content-between">
          <span class="text-muted">Transaction ID</span>
          <small class="text-break">{{ $order->transaction_id }}</small>
        </div>
        @endif
      </div>
    </div>

    {{-- Customer --}}
    @if($order->user)
    <div class="card">
      <div class="card-header"><h5 class="mb-0"><i class="bx bx-user me-1"></i> Customer</h5></div>
      <div class="card-body">
        <p class="fw-semibold mb-1">{{ $order->user->name }}</p>
        <p class="text-muted mb-2">{{ $order->user->email }}</p>
        <a href="{{ route('admin.users.show',$order->user) }}" class="btn btn-sm btn-outline-primary w-100">View Profile</a>
      </div>
    </div>
    @endif

  </div>
</div>
@endsection
