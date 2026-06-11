<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'ShopBD') — Bangladesh's Best Online Store</title>
  <meta name="description" content="@yield('meta_description', 'ShopBD - Shop the best products at the best prices in Bangladesh.')">
  <link rel="icon" href="{{ asset('sneat-assets/img/favicon/favicon.ico') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
  @stack('styles')
  <style>
    :root {
      --primary:#6c3fe0; --primary-dark:#5432c2; --secondary:#ff6b35;
      --accent:#00d4aa; --text:#1a1a2e; --muted:#6c757d;
      --border:#e8ecef; --bg-light:#f8f9fc;
    }
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Inter',sans-serif;color:var(--text);background:#fff}
    /* Navbar */
    .main-navbar{background:#fff;box-shadow:0 2px 20px rgba(0,0,0,.08);position:sticky;top:0;z-index:1000}
    .navbar-brand-text{font-weight:800;font-size:1.5rem;background:linear-gradient(135deg,var(--primary),var(--secondary));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
    .nav-link-custom{color:var(--text)!important;font-weight:500;padding:.5rem .9rem!important;border-radius:6px;transition:all .2s}
    .nav-link-custom:hover{color:var(--primary)!important;background:rgba(108,63,224,.06)}
    .btn-cart{position:relative}
    .cart-badge{position:absolute;top:-6px;right:-6px;background:var(--secondary);color:#fff;border-radius:50%;width:18px;height:18px;font-size:10px;display:flex;align-items:center;justify-content:center;font-weight:700}
    .top-bar{background:var(--primary);color:white;font-size:12px;padding:6px 0}
    /* Search */
    .search-wrap{position:relative}
    .search-wrap input{border-radius:25px 0 0 25px;border:2px solid var(--border);border-right:none;padding-left:20px;transition:border-color .2s}
    .search-wrap input:focus{border-color:var(--primary);outline:none;box-shadow:none}
    .search-wrap button{border-radius:0 25px 25px 0;background:var(--primary);border:2px solid var(--primary);padding:0 20px;color:#fff}
    .search-suggestions{position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 12px 40px rgba(0,0,0,.12);z-index:9999;max-height:400px;overflow-y:auto;margin-top:4px}
    .search-suggestion-item{display:flex;align-items:center;gap:12px;padding:10px 14px;text-decoration:none;color:var(--text);border-bottom:1px solid #f5f5f5;transition:background .15s}
    .search-suggestion-item:hover{background:#f8f0ff}
    .search-suggestion-item:last-child{border-bottom:none}
    .search-suggestion-img{width:44px;height:44px;object-fit:cover;border-radius:8px;flex-shrink:0}
    /* Product Cards */
    .product-card{border:1px solid var(--border);border-radius:12px;overflow:hidden;transition:all .3s;position:relative;background:#fff;height:100%}
    .product-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.12)}
    .product-card .img-wrap{position:relative;overflow:hidden;aspect-ratio:1;background:var(--bg-light)}
    .product-card .img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .4s}
    .product-card:hover .img-wrap img{transform:scale(1.06)}
    .badge-discount{position:absolute;top:10px;left:10px;background:var(--secondary);color:white;font-size:11px;font-weight:700;padding:3px 8px;border-radius:4px}
    .badge-new-tag{position:absolute;top:10px;right:10px;background:var(--accent);color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:4px}
    .actions-overlay{position:absolute;top:10px;right:10px;display:flex;flex-direction:column;gap:6px;opacity:0;transform:translateX(10px);transition:all .3s}
    .product-card:hover .actions-overlay{opacity:1;transform:translateX(0)}
    .action-btn{width:36px;height:36px;border-radius:50%;background:white;border:none;box-shadow:0 2px 8px rgba(0,0,0,.15);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;font-size:14px;text-decoration:none;color:var(--text)}
    .action-btn:hover{background:var(--primary);color:white}
    .card-body{padding:14px}
    .product-title{font-weight:600;font-size:.9rem;color:var(--text);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:6px}
    .price-current{font-size:1.1rem;font-weight:700;color:var(--primary)}
    .price-original{font-size:.85rem;color:var(--muted);text-decoration:line-through}
    .btn-add-cart{background:var(--primary);color:white;border:none;border-radius:8px;padding:8px 16px;font-size:.85rem;font-weight:600;width:100%;transition:all .2s;cursor:pointer}
    .btn-add-cart:hover{background:var(--primary-dark)}
    /* Hero */
    .hero-swiper{border-radius:16px;overflow:hidden}
    .hero-swiper .swiper-slide img{width:100%;height:420px;object-fit:cover}
    /* Categories */
    .category-card{border-radius:12px;overflow:hidden;text-align:center;transition:all .3s;cursor:pointer;background:#fff;border:1px solid var(--border);padding:24px 12px}
    .category-card:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(0,0,0,.1);border-color:var(--primary)}
    .category-icon-wrap{width:70px;height:70px;border-radius:50%;background:rgba(108,63,224,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:28px}
    .sub-cat-badge{display:inline-block;background:var(--bg-light);border:1px solid var(--border);border-radius:20px;padding:4px 12px;font-size:12px;color:var(--muted);text-decoration:none;transition:all .2s;margin:2px}
    .sub-cat-badge:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
    /* Section */
    .section-title{font-size:1.6rem;font-weight:800}
    .section-title span{color:var(--primary)}
    .section-divider{width:50px;height:4px;background:linear-gradient(90deg,var(--primary),var(--secondary));border-radius:2px;margin:8px 0}
    /* Footer */
    .site-footer{background:#0f0f23;color:#b0b8c8;padding:60px 0 0}
    .footer-brand{font-size:1.8rem;font-weight:800;color:white}
    .footer-heading{color:white;font-weight:700;margin-bottom:20px;font-size:1rem;text-transform:uppercase;letter-spacing:.5px}
    .footer-link{color:#b0b8c8;text-decoration:none;display:block;padding:4px 0;transition:color .2s;font-size:.9rem}
    .footer-link:hover{color:var(--accent)}
    .footer-bottom{background:#07071a;padding:18px 0;margin-top:40px}
    .social-btn{width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.08);display:inline-flex;align-items:center;justify-content:center;color:white;text-decoration:none;transition:all .2s;font-size:16px}
    .social-btn:hover{background:var(--primary);color:white}
    /* Cart Offcanvas */
    .qty-btn{width:28px;height:28px;border-radius:4px;border:1px solid var(--border);background:white;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center}
    .qty-btn:hover{background:var(--primary);color:white;border-color:var(--primary)}
    /* Star */
    .star-rating{color:#ffc107;font-size:13px}
    /* Breadcrumb */
    .breadcrumb-section{background:var(--bg-light);padding:12px 0;border-bottom:1px solid var(--border)}
    /* Misc */
    .fw-600{font-weight:600!important}
    .fw-700{font-weight:700!important}
    .fw-800{font-weight:800!important}
    @media(max-width:768px){.hero-swiper .swiper-slide img{height:220px}.section-title{font-size:1.3rem}}
  </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar d-none d-md-block">
  <div class="container d-flex justify-content-between align-items-center">
    <span><i class="bi bi-telephone-fill me-1"></i>+880-1700-000000 &nbsp;|&nbsp; <i class="bi bi-envelope-fill me-1"></i>info@shopbd.com</span>
    <span>
      <i class="bi bi-truck me-1"></i> Free Delivery on orders over ৳1000
      @guest &nbsp;|&nbsp; <a href="{{ route('login') }}" class="text-white text-decoration-none">Login</a> &nbsp;|&nbsp; <a href="{{ route('register') }}" class="text-white text-decoration-none">Register</a>@endguest
    </span>
  </div>
</div>

<!-- Navbar -->
<nav class="main-navbar navbar navbar-expand-lg py-2">
  <div class="container">
    <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center gap-2">
      <svg width="32" height="32" viewBox="0 0 32 32"><circle cx="16" cy="16" r="16" fill="#6c3fe0"/><text x="16" y="21" text-anchor="middle" fill="white" font-size="14" font-weight="800" font-family="Inter">S</text></svg>
      <span class="navbar-brand-text">ShopBD</span>
    </a>

    <!-- Live Search Desktop -->
    <form class="d-none d-lg-flex mx-4 flex-grow-1 search-wrap" style="max-width:440px" action="{{ route('products.search') }}" method="GET" autocomplete="off">
      <div class="input-group w-100">
        <input type="text" name="q" id="frontendSearchDesktop" class="form-control" placeholder="Search products, brands..." value="{{ request('q') }}">
        <button type="submit" class="btn"><i class="bi bi-search"></i></button>
      </div>
      <div id="searchSuggestDesktop" class="search-suggestions d-none"></div>
    </form>

    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <i class="bi bi-list fs-4"></i>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item"><a href="{{ route('home') }}" class="nav-link nav-link-custom">Home</a></li>
        <li class="nav-item"><a href="{{ route('products.index') }}" class="nav-link nav-link-custom">Products</a></li>
        <li class="nav-item dropdown">
          <a href="#" class="nav-link nav-link-custom dropdown-toggle" data-bs-toggle="dropdown">Categories</a>
          <ul class="dropdown-menu border-0 shadow-lg" style="min-width:220px">
            @php try { $navCats = \App\Models\Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(12)->get(); } catch(\Exception $e){ $navCats = collect(); } @endphp
            @foreach($navCats as $cat)
              <li>
                <a href="{{ route('products.category', $cat->slug) }}" class="dropdown-item py-2 d-flex align-items-center gap-2">
                  <i class="bi bi-grid-3x3-gap text-primary"></i> {{ $cat->name }}
                  @if($cat->children->count() > 0)<i class="bi bi-chevron-right ms-auto text-muted small"></i>@endif
                </a>
              </li>
            @endforeach
          </ul>
        </li>

        @auth
        <li class="nav-item">
          <a href="{{ route('wishlist.index') }}" class="btn btn-outline-secondary btn-sm btn-cart" title="Wishlist">
            <i class="bi bi-heart"></i>
            @php try{ $wCount = auth()->user()->wishlist()->count(); }catch(\Exception $e){ $wCount=0; } @endphp
            @if($wCount > 0)<span class="cart-badge">{{ $wCount }}</span>@endif
          </a>
        </li>
        <li class="nav-item dropdown">
          <a href="#" class="nav-link nav-link-custom dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
            <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" width="28" height="28">
            <span class="d-none d-lg-inline">{{ \Str::words(auth()->user()->name,1,'') }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
            <li><h6 class="dropdown-header">{{ auth()->user()->name }}</h6></li>
            <li><a href="{{ route('account.dashboard') }}" class="dropdown-item"><i class="bi bi-person me-2"></i>My Account</a></li>
            <li><a href="{{ route('account.orders') }}" class="dropdown-item"><i class="bi bi-bag me-2"></i>My Orders</a></li>
            <li><a href="{{ route('wishlist.index') }}" class="dropdown-item"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
            @hasanyrole('super-admin|admin|manager|staff')
            <li><hr class="dropdown-divider"></li>
            <li><a href="{{ route('admin.dashboard') }}" class="dropdown-item text-primary"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
            @endhasanyrole
            <li><hr class="dropdown-divider"></li>
            <li>
              <a href="#" class="dropdown-item text-danger" onclick="event.preventDefault();document.getElementById('nav-logout').submit()">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
              </a>
              <form id="nav-logout" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
          </ul>
        </li>
        @else
        <li class="nav-item"><a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login</a></li>
        <li class="nav-item"><a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a></li>
        @endauth

        <li class="nav-item">
          <button class="btn btn-primary btn-sm btn-cart" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" title="Cart">
            <i class="bi bi-cart3"></i>
            @php
              try {
                $cKey = auth()->check() ? 'user_id' : 'session_id';
                $cVal = auth()->check() ? auth()->id() : session()->getId();
                $cartCount = \App\Models\Cart::where($cKey, $cVal)->sum('qty');
              } catch(\Exception $e){ $cartCount = 0; }
            @endphp
            @if($cartCount > 0)<span class="cart-badge">{{ $cartCount }}</span>@endif
          </button>
        </li>
      </ul>

      <!-- Mobile Search -->
      <div class="d-flex d-lg-none mt-3 search-wrap">
        <div class="input-group w-100">
          <input type="text" id="frontendSearchMobile" class="form-control" placeholder="Search products...">
          <button class="btn" style="background:var(--primary);color:#fff;border:none"><i class="bi bi-search"></i></button>
        </div>
        <div id="searchSuggestMobile" class="search-suggestions d-none"></div>
      </div>
    </div>
  </div>
</nav>

<!-- Cart Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" style="width:420px;max-width:100vw">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title fw-bold"><i class="bi bi-cart3 me-2"></i>Shopping Cart
      @if($cartCount > 0)<span class="badge bg-primary ms-1">{{ $cartCount }}</span>@endif
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-0">
    @php
      try {
        $sideCartItems = \App\Models\Cart::where($cKey ?? 'session_id', $cVal ?? session()->getId())->with('product')->get();
        $sideCartTotal = $sideCartItems->sum('subtotal');
      } catch(\Exception $e){ $sideCartItems = collect(); $sideCartTotal = 0; }
    @endphp
    @if($sideCartItems->isEmpty())
      <div class="text-center py-5 px-3">
        <i class="bi bi-cart-x" style="font-size:64px;color:#dee2e6"></i>
        <p class="mt-3 text-muted fw-600">Your cart is empty</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm" data-bs-dismiss="offcanvas">Browse Products</a>
      </div>
    @else
      <div class="p-3">
        @foreach($sideCartItems as $item)
        <div class="d-flex gap-3 mb-3 pb-3 border-bottom align-items-start">
          <img src="{{ $item->product->thumbnail_url }}" class="rounded-3 flex-shrink-0" style="width:64px;height:64px;object-fit:cover" alt="{{ $item->product->name }}">
          <div class="flex-grow-1 min-width-0">
            <p class="mb-1 fw-600" style="font-size:.85rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $item->product->name }}</p>
            <p class="mb-2 text-primary fw-bold small">৳{{ number_format($item->price,0) }}</p>
            <div class="d-flex align-items-center gap-2">
              <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center gap-1">
                @csrf @method('PUT')
                <button type="submit" name="qty" value="{{ max(1,$item->qty-1) }}" class="qty-btn">−</button>
                <span class="fw-bold px-2 small">{{ $item->qty }}</span>
                <button type="submit" name="qty" value="{{ $item->qty+1 }}" class="qty-btn">+</button>
              </form>
              <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="ms-auto">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash3"></i></button>
              </form>
            </div>
          </div>
          <span class="fw-bold text-primary small flex-shrink-0">৳{{ number_format($item->subtotal,0) }}</span>
        </div>
        @endforeach
      </div>
    @endif
  </div>
  @if(!$sideCartItems->isEmpty())
  <div class="border-top p-3">
    <div class="d-flex justify-content-between fw-bold mb-3">
      <span>Subtotal</span>
      <span class="text-primary fs-5">৳{{ number_format($sideCartTotal,0) }}</span>
    </div>
    <div class="d-grid gap-2">
      <a href="{{ route('cart.index') }}" class="btn btn-outline-primary" data-bs-dismiss="offcanvas">View Cart</a>
      <a href="{{ route('checkout.index') }}" class="btn btn-primary">Checkout <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
  </div>
  @endif
</div>

<!-- Flash Messages -->
@if(session('success') || session('error') || $errors->any())
<div class="container mt-3" id="flashMessages">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
</div>
@endif

<main>@yield('content')</main>

<!-- Footer -->
<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-12 col-md-4">
        <div class="footer-brand mb-3">ShopBD</div>
        <p style="font-size:.9rem;line-height:1.9">Bangladesh's trusted online shopping destination. Quality products, fast delivery, excellent customer service.</p>
        <div class="d-flex gap-2 mt-3">
          <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
          <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
          <a href="#" class="social-btn"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="social-btn"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
      <div class="col-6 col-md-2">
        <h6 class="footer-heading">Shop</h6>
        <a href="{{ route('products.index') }}" class="footer-link">All Products</a>
        @php try{ $footerCats = \App\Models\Category::active()->take(5)->get(); }catch(\Exception $e){ $footerCats=collect(); } @endphp
        @foreach($footerCats as $c)<a href="{{ route('products.category',$c->slug) }}" class="footer-link">{{ $c->name }}</a>@endforeach
      </div>
      <div class="col-6 col-md-2">
        <h6 class="footer-heading">Account</h6>
        @auth
          <a href="{{ route('account.dashboard') }}" class="footer-link">My Account</a>
          <a href="{{ route('account.orders') }}" class="footer-link">My Orders</a>
          <a href="{{ route('wishlist.index') }}" class="footer-link">Wishlist</a>
        @else
          <a href="{{ route('login') }}" class="footer-link">Login</a>
          <a href="{{ route('register') }}" class="footer-link">Register</a>
        @endauth
      </div>
      <div class="col-md-4">
        <h6 class="footer-heading">Contact Us</h6>
        <p style="font-size:.9rem"><i class="bi bi-geo-alt me-2"></i>Dhaka, Bangladesh</p>
        <p style="font-size:.9rem"><i class="bi bi-telephone me-2"></i>+880-1700-000000</p>
        <p style="font-size:.9rem"><i class="bi bi-envelope me-2"></i>info@shopbd.com</p>
        <div class="mt-3 d-flex gap-2 align-items-center flex-wrap">
          <span class="badge bg-secondary-subtle text-secondary border py-2 px-3">💳 Stripe</span>
          <span class="badge bg-secondary-subtle text-secondary border py-2 px-3">🏦 SSLCommerz</span>
          <span class="badge bg-secondary-subtle text-secondary border py-2 px-3">💵 COD</span>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom mt-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
      <p class="mb-0 small">© {{ date('Y') }} ShopBD. All rights reserved.</p>
      <p class="mb-0 small">Made with ❤️ in Bangladesh</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
// Auto dismiss alerts
setTimeout(() => { document.querySelectorAll('#flashMessages .alert').forEach(el => { try{ bootstrap.Alert.getOrCreateInstance(el).close(); }catch(e){} }); }, 5000);

// ── Frontend Live Search ──
function setupLiveSearch(inputId, suggestId) {
  const input = document.getElementById(inputId);
  const box   = document.getElementById(suggestId);
  if(!input || !box) return;
  let timer;
  input.addEventListener('input', function() {
    clearTimeout(timer);
    const q = this.value.trim();
    if(q.length < 2) { box.classList.add('d-none'); return; }
    timer = setTimeout(() => {
      fetch(`/search/suggest?q=${encodeURIComponent(q)}`)
        .then(r => r.json()).then(items => {
          if(!items.length) {
            box.innerHTML = `<div class="p-3 text-center text-muted small">No results for "${q}"</div>`;
          } else {
            box.innerHTML = items.map(it => `
              <a href="${it.url}" class="search-suggestion-item">
                <img src="${it.image}" class="search-suggestion-img" onerror="this.src='/images/placeholder-product.svg'">
                <div class="flex-grow-1 overflow-hidden">
                  <p class="mb-0 fw-600" style="font-size:.85rem;text-overflow:ellipsis;overflow:hidden;white-space:nowrap">${it.name}</p>
                  <small class="text-muted">${it.category} · <span style="color:#6c3fe0;font-weight:700">৳${it.price}</span></small>
                </div>
                ${it.badge ? `<span class="badge bg-danger" style="font-size:10px">-${it.badge}%</span>` : ''}
              </a>`).join('') +
              `<a href="/products?search=${encodeURIComponent(q)}" class="d-block text-center py-2 text-primary small border-top text-decoration-none fw-600">
                View all results for "${q}" <i class="bi bi-arrow-right"></i>
              </a>`;
          }
          box.classList.remove('d-none');
        }).catch(() => box.classList.add('d-none'));
    }, 280);
  });
  document.addEventListener('click', e => {
    if(!input.contains(e.target) && !box.contains(e.target)) box.classList.add('d-none');
  });
  input.addEventListener('keydown', e => { if(e.key==='Escape') box.classList.add('d-none'); });
}
setupLiveSearch('frontendSearchDesktop', 'searchSuggestDesktop');
setupLiveSearch('frontendSearchMobile',  'searchSuggestMobile');
</script>
@stack('scripts')
</body>
</html>
