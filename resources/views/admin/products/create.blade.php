@extends('layouts.admin.app')
@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page_title', isset($product) ? 'Edit Product' : 'Add Product')

@section('breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-style1 mb-0">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
      <li class="breadcrumb-item active">{{ isset($product) ? 'Edit' : 'Add' }}</li>
    </ol>
  </nav>
@endsection

@section('content')

@php
  $action = isset($product) ? route('admin.products.update', $product) : route('admin.products.store');
  $method = isset($product) ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
  @csrf
  @if($method === 'PUT') @method('PUT') @endif

  <div class="row g-4">

    {{-- LEFT: Main Info --}}
    <div class="col-12 col-xl-8">

      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Product Information</h5></div>
        <div class="card-body">

          <div class="mb-3">
            <label class="form-label">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name', $product->name ?? '') }}" placeholder="Enter product name" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">SKU</label>
              <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                value="{{ old('sku', $product->sku ?? '') }}" placeholder="Auto-generate if empty">
              @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Brand</label>
              <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand ?? '') }}" placeholder="e.g. Samsung">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Short Description</label>
            <textarea name="short_description" class="form-control" rows="2" placeholder="Brief product summary (max 500 chars)">{{ old('short_description', $product->short_description ?? '') }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Full Description</label>
            <textarea name="description" id="description" class="form-control" rows="8" placeholder="Detailed product description">{{ old('description', $product->description ?? '') }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Tags <small class="text-muted">(comma separated)</small></label>
            <input type="text" name="tags" class="form-control" placeholder="e.g. electronics, new, sale"
              value="{{ old('tags', isset($product->tags) ? implode(', ', $product->tags) : '') }}">
          </div>

        </div>
      </div>

      {{-- Images --}}
      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Product Images</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Thumbnail Image</label>
            @if(isset($product) && $product->thumbnail)
              <div class="mb-2">
                <img src="{{ $product->thumbnail_url }}" alt="Thumbnail" class="img-thumbnail" style="max-height:120px">
              </div>
            @endif
            <input type="file" name="thumbnail" class="form-control" accept="image/*">
            <small class="text-muted">Max 2MB. Recommended: 800×800px</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Gallery Images <small class="text-muted">(multiple)</small></label>
            <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
          </div>
          @if(isset($product) && $product->images->count() > 0)
            <div class="row g-2">
              @foreach($product->images as $img)
              <div class="col-auto">
                <div class="position-relative">
                  <img src="{{ $img->url }}" alt="Gallery" class="img-thumbnail" style="width:80px;height:80px;object-fit:cover">
                  <form action="{{ route('admin.products.images.delete', $img) }}" method="POST" class="position-absolute top-0 end-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm p-0" style="width:20px;height:20px;font-size:10px" title="Delete">×</button>
                  </form>
                </div>
              </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>

    </div>

    {{-- RIGHT: Pricing, Stock, Settings --}}
    <div class="col-12 col-xl-4">

      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Pricing</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Regular Price <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">৳</span>
              <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                value="{{ old('price', $product->price ?? '') }}" placeholder="0.00" step="0.01" min="0" required>
            </div>
            @error('price')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Sale Price</label>
            <div class="input-group">
              <span class="input-group-text">৳</span>
              <input type="number" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror"
                value="{{ old('sale_price', $product->sale_price ?? '') }}" placeholder="0.00" step="0.01" min="0">
            </div>
            @error('sale_price')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Cost Price <small class="text-muted">(internal)</small></label>
            <div class="input-group">
              <span class="input-group-text">৳</span>
              <input type="number" name="cost_price" class="form-control"
                value="{{ old('cost_price', $product->cost_price ?? '') }}" placeholder="0.00" step="0.01" min="0">
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Inventory</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
              value="{{ old('stock', $product->stock ?? 0) }}" min="0" required>
            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Min Order Qty</label>
              <input type="number" name="min_order_qty" class="form-control" value="{{ old('min_order_qty', $product->min_order_qty ?? 1) }}" min="1">
            </div>
            <div class="col-6">
              <label class="form-label">Max Order Qty</label>
              <input type="number" name="max_order_qty" class="form-control" value="{{ old('max_order_qty', $product->max_order_qty ?? '') }}" min="1" placeholder="No limit">
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Category</h5></div>
        <div class="card-body">
          <select name="category_id" class="form-select">
            <option value="">— Select Category —</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Publish Settings</h5></div>
        <div class="card-body">
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
              {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active (Visible in store)</label>
          </div>
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
              {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_featured">Featured Product</label>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_digital" id="is_digital" value="1"
              {{ old('is_digital', $product->is_digital ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_digital">Digital Product</label>
          </div>
        </div>
      </div>

      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bx bx-save me-1"></i> {{ isset($product) ? 'Update Product' : 'Create Product' }}
        </button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>

    </div>
  </div>
</form>
@endsection
