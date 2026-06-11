@extends('layouts.admin.app')
@section('title', 'Product Details')
@section('page_title', 'Product Details: ' . $product->name)

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-style1 mb-0">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
      <li class="breadcrumb-item active">Details</li>
    </ol>
  </nav>
@endsection

@section('content')
<div class="row g-4">
  {{-- LEFT: Image & Media View --}}
  <div class="col-12 col-xl-4">
    <div class="card mb-4">
      <div class="card-body text-center">
        <h6 class="text-muted text-start mb-3">Thumbnail Image</h6>
        @if($product->thumbnail)
          <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="img-fluid rounded border mb-3" style="max-height: 300px; object-fit: cover;">
        @else
          <div class="bg-light d-flex align-items-center justify-content-center rounded border mb-3" style="height: 250px;">
            <span class="text-muted">No Thumbnail Available</span>
          </div>
        @endif
      </div>
    </div>

    @if($product->images && $product->images->count() > 0)
    <div class="card">
      <div class="card-body">
        <h6 class="text-muted mb-3">Gallery Images</h6>
        <div class="row g-2">
          @foreach($product->images as $img)
            <div class="col-4">
              <img src="{{ asset('storage/' . $img->image) }}" alt="Gallery" class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;">
            </div>
          @endforeach
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- RIGHT: Specifications & Status --}}
  <div class="col-12 col-xl-8">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Product Specifications</h5>
        <div>
          <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-primary">
            <i class="bx bx-edit-alt me-1"></i> Edit Product
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered mb-0">
            <tbody>
              <tr>
                <th style="width: 30%;">Product Name</th>
                <td><strong>{{ $product->name }}</strong></td>
              </tr>
              <tr>
                <th>SKU</th>
                <td><span class="badge bg-label-secondary">{{ $product->sku ?? 'N/A' }}</span></td>
              </tr>
              <tr>
                <th>Category</th>
                <td>{{ $product->category?->name ?? 'Uncategorized' }}</td>
              </tr>
              <tr>
                <th>Brand</th>
                <td>{{ $product->brand ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Regular Price</th>
                <td>৳{{ number_format($product->price, 2) }}</td>
              </tr>
              <tr>
                <th>Sale Price</th>
                <td>
                  @if($product->sale_price)
                    <strong class="text-danger">৳{{ number_format($product->sale_price, 2) }}</strong>
                  @else
                    <span class="text-muted">No Discount</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>Cost Price (Internal)</th>
                <td>৳{{ number_format($product->cost_price ?? 0, 2) }}</td>
              </tr>
              <tr>
                <th>Stock Status</th>
                <td>
                  @if($product->stock > 0)
                    <span class="badge bg-success">In Stock ({{ $product->stock }})</span>
                  @else
                    <span class="badge bg-danger">Out of Stock</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>Order Limits</th>
                <td>Min: {{ $product->min_order_qty }} | Max: {{ $product->max_order_qty ?? 'No limit' }}</td>
              </tr>
              <tr>
                <th>Visibility / Status</th>
                <td>
                  <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-warning' }}">
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                  </span>
                  @if($product->is_featured)
                    <span class="badge bg-info ms-1">Featured</span>
                  @endif
                  @if($product->is_digital)
                    <span class="badge bg-primary ms-1">Digital</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>Tags</th>
                <td>
                  @if($product->tags)
                    @foreach($product->tags as $tag)
                      <span class="badge bg-secondary">{{ $tag }}</span>
                    @endforeach
                  @else
                    <span class="text-muted">No tags</span>
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        @if($product->short_description)
          <div class="mt-4">
            <h6>Short Description</h6>
            <div class="p-3 bg-light rounded border text-muted">
              {{ $product->short_description }}
            </div>
          </div>
        @endif

        @if($product->description)
          <div class="mt-4">
            <h6>Full Description</h6>
            <div class="p-3 bg-white rounded border">
              {!! nl2br(e($product->description)) !!}
            </div>
          </div>
        @endif
      </div>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back me-1"></i> Back to List
      </a>
    </div>
  </div>
</div>
@endsection
