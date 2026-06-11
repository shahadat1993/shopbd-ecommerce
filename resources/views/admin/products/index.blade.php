@extends('layouts.admin.app')
@section('title', 'Products')
@section('page_title', 'Products')

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-style1 mb-0">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Products</li>
    </ol>
  </nav>
@endsection

@section('page_actions')
  <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
    <i class="bx bx-plus me-1"></i> Add Product
  </a>
@endsection

@section('content')

{{-- Filters --}}
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search by name or SKU..." value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <select name="category" class="form-select">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select">
          <option value="">All Status</option>
          <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
          <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
      <div class="col-md-2">
        <select name="stock" class="form-select">
          <option value="">All Stock</option>
          <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Low Stock (≤5)</option>
          <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>Out of Stock</option>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">Filter</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

{{-- Products Table --}}
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">All Products <span class="badge bg-label-primary ms-1">{{ $products->total() }}</span></h5>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.reports.export', 'products') }}" class="btn btn-sm btn-outline-success">
        <i class="bx bx-export me-1"></i> Export CSV
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width:60px">#</th>
          <th>Product</th>
          <th>Category</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Status</th>
          <th>Featured</th>
          <th style="width:120px">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
        <tr>
          <td>{{ $product->id }}</td>
          <td>
            <div class="d-flex align-items-center gap-3">
              <div class="avatar avatar-sm flex-shrink-0">
                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="rounded object-fit-cover" style="width:40px;height:40px;">
              </div>
              <div>
                <p class="mb-0 fw-semibold">{{ Str::limit($product->name, 40) }}</p>
                <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
              </div>
            </div>
          </td>
          <td>{{ $product->category?->name ?? '—' }}</td>
          <td>
            <div class="fw-semibold">৳{{ number_format($product->price, 0) }}</div>
            @if($product->sale_price)
              <small class="text-success">Sale: ৳{{ number_format($product->sale_price, 0) }}</small>
            @endif
          </td>
          <td>
            @if($product->stock == 0)
              <span class="badge bg-danger">Out of Stock</span>
            @elseif($product->stock <= 5)
              <span class="badge bg-warning">Low: {{ $product->stock }}</span>
            @else
              <span class="badge bg-success">{{ $product->stock }}</span>
            @endif
          </td>
          <td>
            @if($product->is_active)
              <span class="badge bg-label-success">Active</span>
            @else
              <span class="badge bg-label-danger">Inactive</span>
            @endif
          </td>
          <td>
            <form action="{{ route('admin.products.toggle.featured', $product) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Toggle Featured">
                @if($product->is_featured)
                  <i class="bx bxs-star text-warning fs-4"></i>
                @else
                  <i class="bx bx-star text-muted fs-4"></i>
                @endif
              </button>
            </form>
          </td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-icon btn-outline-info" title="View">
                <i class="bx bx-show"></i>
              </a>
              <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                <i class="bx bx-edit"></i>
              </a>
              <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete">
                  <i class="bx bx-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-5">
          <i class="bx bx-package fs-1 d-block mb-2 opacity-25"></i>
          No products found.
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($products->hasPages())
  <div class="card-footer d-flex justify-content-between align-items-center">
    <span class="text-muted small">Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}</span>
    {{ $products->withQueryString()->links() }}
  </div>
  @endif
</div>
@endsection
