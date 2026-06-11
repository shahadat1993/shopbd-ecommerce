@extends('layouts.admin.app')
@section('title','Orders')
@section('page_title','Orders')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb breadcrumb-style1 mb-0">
  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
  <li class="breadcrumb-item active">Orders</li>
</ol></nav>
@endsection

@section('content')
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-3">
        <input type="text" name="search" class="form-control" placeholder="Order # or Customer..." value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select">
          <option value="">All Status</option>
          @foreach(['pending','confirmed','processing','shipped','delivered','cancelled','refunded'] as $s)
            <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <select name="payment_status" class="form-select">
          <option value="">Payment Status</option>
          @foreach(['pending','paid','failed','refunded'] as $s)
            <option value="{{ $s }}" {{ request('payment_status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
      <div class="col-md-2"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
      <div class="col-md-1 d-flex gap-1">
        <button type="submit" class="btn btn-primary flex-fill"><i class="bx bx-filter"></i></button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Orders <span class="badge bg-label-primary ms-1">{{ $orders->total() }}</span></h5>
    <a href="{{ route('admin.reports.export','orders') }}" class="btn btn-sm btn-outline-success">
      <i class="bx bx-export me-1"></i>Export CSV
    </a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Order #</th><th>Customer</th><th>Items</th><th>Total</th>
          <th>Method</th><th>Status</th><th>Payment</th><th>Date</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
        <tr>
          <td><a href="{{ route('admin.orders.show',$order) }}" class="fw-semibold text-primary">#{{ $order->order_number }}</a></td>
          <td>
            <div class="fw-semibold">{{ $order->shipping_name }}</div>
            <small class="text-muted">{{ $order->shipping_phone }}</small>
          </td>
          <td><span class="badge bg-label-secondary">{{ $order->items->count() }} items</span></td>
          <td class="fw-bold">৳{{ number_format($order->total,0) }}</td>
          <td><span class="badge bg-label-info text-uppercase">{{ $order->payment_method }}</span></td>
          <td><span class="badge bg-label-{{ $order->status_badge }} text-capitalize">{{ $order->status }}</span></td>
          <td><span class="badge bg-label-{{ $order->payment_status_badge }} text-capitalize">{{ $order->payment_status }}</span></td>
          <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
          <td>
            <a href="{{ route('admin.orders.show',$order) }}" class="btn btn-sm btn-icon btn-outline-primary" title="View">
              <i class="bx bx-show"></i>
            </a>
            <a href="{{ route('admin.orders.invoice',$order) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Invoice" target="_blank">
              <i class="bx bx-printer"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted py-5">
          <i class="bx bx-cart fs-1 d-block mb-2 opacity-25"></i>No orders found.
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($orders->hasPages())
  <div class="card-footer d-flex justify-content-between align-items-center">
    <span class="text-muted small">Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}</span>
    {{ $orders->withQueryString()->links() }}
  </div>
  @endif
</div>
@endsection
