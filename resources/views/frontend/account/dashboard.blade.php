@extends('layouts.frontend.app')
@section('title', 'My Account')

@section('content')
<div class="breadcrumb-section">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">My Account</li>
      </ol>
    </nav>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <div class="row g-4">

      {{-- Sidebar --}}
      <div class="col-12 col-md-3">
        <div class="card">
          <div class="card-body text-center py-4">
            <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="80" height="80" alt="{{ $user->name }}">
            <h6 class="fw-700 mb-0">{{ $user->name }}</h6>
            <p class="text-muted small mb-0">{{ $user->email }}</p>
          </div>
          <div class="list-group list-group-flush rounded-bottom">
            <a href="{{ route('account.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
              <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action {{ request()->routeIs('account.orders*') ? 'active' : '' }}">
              <i class="bi bi-bag me-2"></i>My Orders
            </a>
            <a href="{{ route('wishlist.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
              <i class="bi bi-heart me-2"></i>Wishlist
            </a>
            <a href="{{ route('account.addresses') }}" class="list-group-item list-group-item-action {{ request()->routeIs('account.addresses*') ? 'active' : '' }}">
              <i class="bi bi-geo-alt me-2"></i>Addresses
            </a>
            <a href="{{ route('account.profile') }}" class="list-group-item list-group-item-action {{ request()->routeIs('account.profile*') ? 'active' : '' }}">
              <i class="bi bi-person-gear me-2"></i>Profile Settings
            </a>
            <a href="#" class="list-group-item list-group-item-action text-danger"
              onclick="event.preventDefault();document.getElementById('account-logout').submit()">
              <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
            <form id="account-logout" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
          </div>
        </div>
      </div>

      {{-- Main Content --}}
      <div class="col-12 col-md-9">
        {{-- Stats --}}
        <div class="row g-3 mb-4">
          @foreach([
            ['icon'=>'bi-bag','label'=>'Total Orders','val'=>$stats['total_orders'],'color'=>'primary'],
            ['icon'=>'bi-clock','label'=>'Pending','val'=>$stats['pending_orders'],'color'=>'warning'],
            ['icon'=>'bi-check-circle','label'=>'Delivered','val'=>$stats['delivered_orders'],'color'=>'success'],
            ['icon'=>'bi-heart','label'=>'Wishlist','val'=>$stats['wishlist_count'],'color'=>'danger'],
          ] as $s)
          <div class="col-6 col-md-3">
            <div class="card text-center">
              <div class="card-body py-3">
                <i class="bi {{ $s['icon'] }} fs-3 text-{{ $s['color'] }} mb-2 d-block"></i>
                <h4 class="fw-800 mb-0">{{ $s['val'] }}</h4>
                <small class="text-muted">{{ $s['label'] }}</small>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        {{-- Recent Orders --}}
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <span class="fw-700">Recent Orders</span>
            <a href="{{ route('account.orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th>Action</th></tr>
              </thead>
              <tbody>
                @forelse($orders as $order)
                <tr>
                  <td class="fw-600">#{{ $order->order_number }}</td>
                  <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                  <td class="fw-700" style="color:#6c3fe0">৳{{ number_format($order->total,0) }}</td>
                  <td><span class="badge bg-label-{{ $order->status_badge }} text-capitalize">{{ $order->status }}</span></td>
                  <td><a href="{{ route('account.orders.show',$order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No orders yet. <a href="{{ route('products.index') }}">Start shopping!</a></td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
