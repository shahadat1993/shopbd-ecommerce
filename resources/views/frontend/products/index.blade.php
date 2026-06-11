@extends('layouts.frontend.app')
@section('title', isset($category) ? $category->name . ' - Products' : 'All Products')

@section('content')
    <div class="breadcrumb-section">
        <div class="container">
            <nav>
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    @isset($category)
                        <li class="breadcrumb-item active">{{ $category->name }}</li>
                    @else
                        <li class="breadcrumb-item active">All Products</li>
                    @endisset
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-4">
        <div class="container">

            @isset($category)
                {{-- Category Header with Sub-categories --}}
                <div class="mb-4 p-4 rounded-3" style="background:linear-gradient(135deg,#6c3fe0,#8b60f0);color:white">
                    <h2 class="fw-800 mb-1">{{ $category->name }}</h2>
                    @if ($category->description)
                        <p class="mb-3 opacity-80 small">{{ $category->description }}</p>
                    @endif
                    @if ($category->children->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('products.category', $category->slug) }}"
                                class="badge px-3 py-2 text-decoration-none fw-600"
                                style="background:rgba(255,255,255,.25);color:white;font-size:.8rem">All
                                {{ $category->name }}</a>
                            @foreach ($category->children as $sub)
                                <a href="{{ route('products.category', $sub->slug) }}"
                                    class="badge px-3 py-2 text-decoration-none"
                                    style="background:rgba(255,255,255,.15);color:white;font-size:.8rem;transition:background .2s"
                                    onmouseover="this.style.background='rgba(255,255,255,.35)'"
                                    onmouseout="this.style.background='rgba(255,255,255,.15)'">{{ $sub->name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endisset

            <div class="row g-4">

                {{-- Sidebar Filters --}}
                <div class="col-12 col-lg-3">
                    {{-- z-index কমিয়ে ১০ করা হয়েছে এবং top পজিশন পারফেক্ট করা হয়েছে --}}
                    <div class="card sticky-top shadow-sm mb-4"
                        style="top: 130px; max-height: calc(100vh - 160px); overflow-y: auto; z-index: 10;">
                        <div
                            class="card-header d-flex justify-content-between align-items-center fw-700 bg-white border-bottom py-3">
                            <span><i class="bi bi-funnel me-1 text-primary"></i>Filters</span>
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary px-3"
                                style="font-size: 0.75rem;">Clear All</a>
                        </div>
                        <div class="card-body p-0">
                            <form method="GET" id="filterForm">
                                @if (request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif

                                {{-- Categories --}}
                                {{-- Categories Filter Section --}}
                                <div class="p-3 border-bottom">
                                    <p class="fw-600 mb-2 small text-uppercase text-muted">Categories</p>
                                    <a href="{{ route('products.index') }}"
                                        class="d-flex justify-content-between align-items-center py-1 text-decoration-none {{ !request('category') && !isset($category) ? 'fw-700 text-primary' : 'text-dark' }}"
                                        style="font-size:.9rem">
                                        <span>All Products</span>
                                        <span
                                            class="badge bg-light text-muted">{{ \App\Models\Product::active()->count() }}</span>
                                    </a>

                                    @foreach ($categories as $cat)
                                        <div class="mb-1">
                                            {{-- প্যারেন্ট ক্যাটাগরি এবং তার টোটাল কাউন্ট (কাস্টম অ্যাক্সেসর) --}}
                                            <a href="{{ route('products.category', $cat->slug) }}"
                                                class="d-flex justify-content-between align-items-center py-1 text-decoration-none {{ isset($category) && $category->id == $cat->id ? 'fw-700 text-primary' : 'text-dark' }}"
                                                style="font-size:.9rem">
                                                <span>{{ $cat->name }}</span>
                                                <span
                                                    class="badge bg-light text-muted">{{ $cat->total_products_count }}</span>
                                            </a>

                                            {{-- Sub-categories (চাইল্ড ক্যাটাগরি সমূহ) --}}
                                            @if ($cat->children->count() > 0)
                                                <div class="ms-3">
                                                    @foreach ($cat->children as $sub)
                                                        <a href="{{ route('products.category', $sub->slug) }}"
                                                            class="d-flex justify-content-between align-items-center py-1 text-decoration-none {{ isset($category) && $category->id == $sub->id ? 'fw-700 text-primary' : 'text-muted' }}"
                                                            style="font-size:.82rem">
                                                            <span>↳ {{ $sub->name }}</span>
                                                            <span class="badge bg-light text-muted"
                                                                style="font-size: 9px;">{{ $sub->total_products_count }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Price Range --}}
                                <div class="p-3 border-bottom">
                                    <p class="fw-600 mb-2 small text-uppercase text-muted">Price Range</p>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small text-muted mb-1">Min ৳</label>
                                            <input type="number" name="min_price" class="form-control form-control-sm"
                                                value="{{ request('min_price') }}" min="0" placeholder="0">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small text-muted mb-1">Max ৳</label>
                                            <input type="number" name="max_price" class="form-control form-control-sm"
                                                value="{{ request('max_price') }}" min="0" placeholder="Any">
                                        </div>
                                    </div>
                                </div>

                                {{-- Brands --}}
                                @if (isset($brands) && $brands->count() > 0)
                                    <div class="p-3 border-bottom">
                                        <p class="fw-600 mb-2 small text-uppercase text-muted">Brand</p>
                                        @foreach ($brands->take(8) as $brand)
                                            <div class="form-check mb-1">
                                                <input class="form-check-input" type="radio" name="brand"
                                                    value="{{ $brand }}" id="brand_{{ Str::slug($brand) }}"
                                                    {{ request('brand') === $brand ? 'checked' : '' }}>
                                                <label class="form-check-label small"
                                                    for="brand_{{ Str::slug($brand) }}">{{ $brand }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Stock & Other --}}
                                <div class="p-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="in_stock" value="1"
                                            id="inStockFilter" {{ request('in_stock') ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-600" for="inStockFilter">In Stock
                                            Only</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="on_sale" value="1"
                                            id="onSaleFilter" {{ request('on_sale') ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-600" for="onSaleFilter">On Sale</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100 mt-3 fw-600">Apply
                                        Filters</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                {{-- Products Grid --}}
                <div class="col-12 col-lg-9">

                    {{-- Toolbar --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0 fw-700">
                                {{ isset($category) ? $category->name : (request('search') ? 'Search Results' : 'All Products') }}
                                <span class="text-muted fw-400 fs-6 ms-2">({{ $products->total() }})</span>
                            </h5>
                            @if (request('search'))
                                <p class="text-muted small mb-0">for "{{ request('search') }}"</p>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small d-none d-md-inline">Sort:</span>
                            <select name="sort" class="form-select form-select-sm" style="width:auto"
                                onchange="this.form.submit()" form="filterForm">
                                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>
                                    Newest
                                    First</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price:
                                    Low →
                                    High</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price:
                                    High
                                    → Low</option>
                                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular
                                </option>
                            </select>
                        </div>
                    </div>

                    @if ($products->count() > 0)
                        <div class="row g-3">
                            @foreach ($products as $product)
                                <div class="col-6 col-md-4 col-xl-3">
                                    @include('frontend.products._card', compact('product'))
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-search"
                                style="font-size:60px;color:#dee2e6;display:block;margin-bottom:16px"></i>
                            <h5 class="text-muted fw-600 mb-2">No products found</h5>
                            <p class="text-muted mb-4">Try adjusting your filters or search terms.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary px-4">Browse All Products</a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
@endsection
