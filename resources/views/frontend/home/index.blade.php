@extends('layouts.frontend.app')
@section('title', 'ShopBD - Bangladesh\'s Best Online Store')

@section('content')

    {{-- Hero Section --}}
    <section class="py-3 py-md-4">
        <div class="container">
            <div class="row g-3">

                {{-- HERO BANNER SLIDER --}}
                <div class="col-12 col-lg-8">
                    @if ($heroBanners && $heroBanners->count() > 0)
                        <div class="swiper hero-swiper" style="border-radius:16px;overflow:hidden">
                            <div class="swiper-wrapper">
                                @foreach ($heroBanners as $banner)
                                    <div class="swiper-slide">
                                        <a href="{{ $banner->link ?? '#' }}">
                                            {{-- তোমার মডেলে যেহেতু getImageUrlAttribute() আছে, তাই সরাসরি $banner->image_url ব্যবহার করা সবচেয়ে নিরাপদ --}}
                                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                                style="width:100%;height:420px;object-fit:cover">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-prev" style="color:#6c3fe0"></div>
                            <div class="swiper-button-next" style="color:#6c3fe0"></div>
                        </div>
                    @else
                        {{-- কোনো ব্যানার না থাকলে ডিফল্ট কার্ড শো করবে --}}
                        <div class="rounded-4 d-flex align-items-center justify-content-center text-white"
                            style="height:420px;background:linear-gradient(135deg,#6c3fe0 0%,#ff6b35 100%)">
                            <div class="text-center px-4">
                                <p class="mb-2 opacity-75"
                                    style="font-size:.9rem;letter-spacing:2px;text-transform:uppercase">Welcome to</p>
                                <h1 class="fw-800 mb-3" style="font-size:3rem">ShopBD</h1>
                                <p class="mb-4 opacity-90 fs-5">Bangladesh's Best Online Shopping Destination</p>
                                <div class="d-flex gap-3 justify-content-center flex-wrap">
                                    <a href="{{ route('products.index') }}" class="btn btn-light btn-lg px-5 fw-700"
                                        style="color:#6c3fe0">Shop Now <i class="bi bi-arrow-right ms-1"></i></a>
                                    <a href="{{ route('products.index', ['sort' => 'popular']) }}"
                                        class="btn btn-outline-light btn-lg px-4">Top Deals</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- PROMO BANNERS --}}
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-column gap-3 h-100">
                        @if ($promoBanners && $promoBanners->count() >= 2)
                            @foreach ($promoBanners->take(2) as $promo)
                                <a href="{{ $promo->link ?? '#' }}" class="flex-fill rounded-4 overflow-hidden d-block"
                                    style="min-height:0">
                                    <img src="{{ $promo->image_url }}" alt="{{ $promo->title }}" class="w-100 h-100"
                                        style="object-fit:cover;max-height:200px">
                                </a>
                            @endforeach
                        @else
                            {{-- ডিফল্ট প্রোমো অপশন --}}
                            <div class="flex-fill rounded-4 p-4 d-flex align-items-center gap-3"
                                style="background:linear-gradient(135deg,#f8f0ff,#e8d8ff);border:1px solid #d8c0ff;min-height:0">
                                <div style="font-size:2.5rem">🔥</div>
                                <div>
                                    <h6 class="fw-700 mb-1" style="color:#6c3fe0">Flash Sales</h6>
                                    <p class="mb-2 text-muted small">Up to 50% OFF Today!</p>
                                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-primary">Shop Now</a>
                                </div>
                            </div>
                            <div class="flex-fill rounded-4 p-4 d-flex align-items-center gap-3"
                                style="background:linear-gradient(135deg,#f0fff8,#d8f5e8);border:1px solid #b8e6cc;min-height:0">
                                <div style="font-size:2.5rem">🚚</div>
                                <div>
                                    <h6 class="fw-700 mb-1" style="color:#00a86b">Free Delivery</h6>
                                    <p class="mb-2 text-muted small">On orders over ৳1,000</p>
                                    <a href="{{ route('products.index') }}" class="btn btn-sm"
                                        style="background:#00a86b;color:#fff">Order Now</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Trust Badges --}}
    <section class="py-3">
        <div class="container">
            <div class="row g-3">
                @foreach ([['bi-truck', 'Free Delivery', 'On orders over ৳1,000', '#6c3fe0'], ['bi-arrow-counterclockwise', 'Easy Returns', '7-day return policy', '#ff6b35'], ['bi-shield-check', 'Secure Payment', '100% safe checkout', '#00a86b'], ['bi-headset', '24/7 Support', 'Always here for you', '#0095ff']] as $b)
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 h-100"
                            style="background:#f8f9fc;border:1px solid #eef0f3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width:46px;height:46px;background:{{ $b[3] }}18">
                                <i class="bi {{ $b[0] }} fs-5" style="color:{{ $b[3] }}"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-700" style="font-size:.85rem">{{ $b[1] }}</p>
                                <p class="mb-0 text-muted" style="font-size:.75rem">{{ $b[2] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Categories --}}
    @if ($topCategories->count() > 0)
        <section class="py-5">
            <div class="container">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="fw-800 mb-1">Shop by <span style="color:#6c3fe0">Category</span></h2>
                        <div
                            style="width:50px;height:4px;background:linear-gradient(90deg,#6c3fe0,#ff6b35);border-radius:2px">
                        </div>
                    </div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">View All <i
                            class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <div class="row g-3">
                    @foreach ($topCategories as $cat)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <a href="{{ route('products.category', $cat->slug) }}" class="text-decoration-none">
                                <div class="text-center p-3 rounded-3 h-100 category-card-item"
                                    style="background:#fff;border:1px solid #e8ecef;transition:all .3s;cursor:pointer">
                                    @if ($cat->image)
                                        <img src="{{ asset('storage/' . $cat->image) }}" class="rounded-circle mb-2"
                                            style="width:64px;height:64px;object-fit:cover">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                                            style="width:64px;height:64px;background:rgba(108,63,224,.1);font-size:26px">
                                            {{ ['Electronics' => '📱', 'Clothing' => '👕', 'Home & Garden' => '🏠', 'Sports' => '⚽', 'Books' => '📚', 'Beauty' => '💄', 'Toys & Games' => '🧸', 'Food & Grocery' => '🛒'][$cat->name] ?? '🛍️' }}
                                        </div>
                                    @endif
                                    <p class="mb-0 fw-600" style="font-size:.85rem;color:#1a1a2e">{{ $cat->name }}</p>
                                    <p class="mb-0 text-muted" style="font-size:.75rem">{{ $cat->products_count }} items
                                    </p>
                                    {{-- Sub-categories --}}
                                    @if ($cat->children->count() > 0)
                                        <div class="d-flex flex-wrap justify-content-center gap-1 mt-2">
                                            @foreach ($cat->children->take(3) as $sub)
                                                <span class="badge"
                                                    style="background:#f0ecff;color:#6c3fe0;font-size:.65rem;font-weight:500">{{ $sub->name }}</span>
                                            @endforeach
                                            @if ($cat->children->count() > 3)
                                                <span class="badge"
                                                    style="background:#f0ecff;color:#6c3fe0;font-size:.65rem">+{{ $cat->children->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Featured Products --}}
    @if ($featuredProducts->count() > 0)
        <section class="py-5" style="background:#f8f9fc">
            <div class="container">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="fw-800 mb-1">Featured <span style="color:#6c3fe0">Products</span></h2>
                        <div
                            style="width:50px;height:4px;background:linear-gradient(90deg,#6c3fe0,#ff6b35);border-radius:2px">
                        </div>
                    </div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
                </div>
                <div class="swiper feat-swiper">
                    <div class="swiper-wrapper pb-4">
                        @foreach ($featuredProducts as $product)
                            <div class="swiper-slide h-auto">
                                @include('frontend.products._card', compact('product'))
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    {{-- Deals Banner --}}
    @if ($dealProducts->count() > 0)
        <section class="py-5">
            <div class="container">
                <div class="rounded-4 overflow-hidden"
                    style="background:linear-gradient(135deg,#6c3fe0 0%,#c040a0 50%,#ff6b35 100%)">
                    <div class="row g-0 align-items-center">
                        <div class="col-12 col-md-5 p-4 p-md-5 text-white">
                            <span class="badge mb-3 px-3 py-2" style="background:rgba(255,255,255,.2);font-size:.8rem">🔥
                                LIMITED TIME DEALS</span>
                            <h2 class="fw-800 mb-2" style="font-size:2.2rem">Special Offers<br>Just For You!</h2>
                            <p class="mb-4 opacity-90">Up to 50% off on selected products. Grab your deals before they
                                expire!</p>
                            <a href="{{ route('products.index') }}" class="btn btn-light fw-700 px-5"
                                style="color:#6c3fe0">
                                Shop All Deals <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="col-12 col-md-7 p-3 p-md-4">
                            <div class="row g-2">
                                @foreach ($dealProducts->take(4) as $product)
                                    <div class="col-6">
                                        <a href="{{ route('products.show', $product) }}"
                                            class="d-block rounded-3 p-2 text-decoration-none"
                                            style="background:rgba(255,255,255,.12);transition:background .2s"
                                            onmouseover="this.style.background='rgba(255,255,255,.22)'"
                                            onmouseout="this.style.background='rgba(255,255,255,.12)'">
                                            <img src="{{ $product->thumbnail_url }}" class="rounded-2 w-100 mb-2"
                                                style="height:90px;object-fit:cover">
                                            <p class="mb-0 fw-600 text-white text-truncate" style="font-size:.8rem">
                                                {{ $product->name }}</p>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="text-white fw-700"
                                                    style="font-size:.9rem">৳{{ number_format($product->current_price, 0) }}</span>
                                                @if ($product->discount_percent > 0)
                                                    <span class="badge bg-danger"
                                                        style="font-size:.65rem">-{{ $product->discount_percent }}%</span>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- New Arrivals --}}
    @if ($newArrivals->count() > 0)
        <section class="py-5">
            <div class="container">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="fw-800 mb-1">New <span style="color:#6c3fe0">Arrivals</span></h2>
                        <div
                            style="width:50px;height:4px;background:linear-gradient(90deg,#6c3fe0,#ff6b35);border-radius:2px">
                        </div>
                    </div>
                    <a href="{{ route('products.index', ['sort' => 'newest']) }}"
                        class="btn btn-outline-primary btn-sm">See More</a>
                </div>
                <div class="row g-3">
                    @foreach ($newArrivals as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            @include('frontend.products._card', compact('product'))
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Newsletter --}}
    <section class="py-5" style="background:linear-gradient(135deg,#6c3fe0,#5432c2)">
        <div class="container">
            <div class="row justify-content-center text-center text-white">
                <div class="col-12 col-md-6">
                    <h3 class="fw-800 mb-2">Stay in the Loop! 📬</h3>
                    <p class="mb-4 opacity-80">Subscribe to get exclusive deals, new arrivals, and special offers delivered
                        to your inbox.</p>
                    <form class="d-flex gap-2 justify-content-center flex-wrap">
                        <input type="email" class="form-control" style="max-width:280px;border-radius:25px;border:none"
                            placeholder="Enter your email address">
                        <button type="submit" class="btn btn-warning fw-700 px-4"
                            style="border-radius:25px">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        // Category card hover
        document.querySelectorAll('.category-card-item').forEach(el => {
            el.addEventListener('mouseenter', () => {
                el.style.transform = 'translateY(-4px)';
                el.style.boxShadow = '0 12px 30px rgba(0,0,0,.1)';
                el.style.borderColor = '#6c3fe0';
            });
            el.addEventListener('mouseleave', () => {
                el.style.transform = '';
                el.style.boxShadow = '';
                el.style.borderColor = '#e8ecef';
            });
        });
        // Hero Swiper
        new Swiper('.hero-swiper', {
            loop: true,
            autoplay: {
                delay: 4500,
                disableOnInteraction: false
            },
            pagination: {
                el: '.hero-swiper .swiper-pagination',
                clickable: true
            },
            navigation: {
                prevEl: '.hero-swiper .swiper-button-prev',
                nextEl: '.hero-swiper .swiper-button-next'
            }
        });
        // Featured Swiper
        new Swiper('.feat-swiper', {
            slidesPerView: 2,
            spaceBetween: 16,
            pagination: {
                el: '.feat-swiper .swiper-pagination',
                clickable: true
            },
            breakpoints: {
                576: {
                    slidesPerView: 3
                },
                768: {
                    slidesPerView: 4
                },
                1200: {
                    slidesPerView: 5
                }
            }
        });
    </script>
@endpush
