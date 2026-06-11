<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="/sneat-assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Dashboard') — {{ config('app.name') }} Admin</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('sneat-assets/img/favicon/favicon.ico') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('sneat-assets/vendor/fonts/boxicons.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat-assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
  <link rel="stylesheet" href="{{ asset('sneat-assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="{{ asset('sneat-assets/css/demo.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat-assets/vendor/libs/apex-charts/apex-charts.css') }}" />
  <script src="{{ asset('sneat-assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('sneat-assets/js/config.js') }}"></script>
  @stack('styles')
</head>
<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <!-- Sidebar -->
      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
              <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg">
                <defs><path d="M13.7918663,0.358365126 L3.39788859,2.44797259 C3.02012081,2.52288986 2.74798053,2.86395117 2.74798053,3.36825709 L2.74798053,6.39162492 C2.74798053,6.77939116 3.07818794,7.06441206 3.36734835,6.95996772 L6.9671242,5.76026115 C7.13401813,5.70480386 7.27467951,5.68935422 7.38040497,5.74467049 C7.49809044,5.80666039 7.55486502,5.93547684 7.55486502,6.13568326 L7.55486502,35.9622126 C7.55486502,36.1932083 7.43321567,36.3028337 7.20995496,36.2317178 L5.1551511,35.5571062 C5.05154162,35.5232681 4.98577896,35.4523364 4.98577896,35.3560876 L4.98577896,10.6305829 C4.98577896,10.2327001 4.68466999,9.88282031 4.34897754,9.88282031 L2.49983969,9.88282031 C2.1641472,9.88282031 1.86303824,10.2327001 1.86303824,10.6305829 L1.86303824,37.2879181 C1.86303824,37.6857973 2.18465958,37.9730769 2.52228007,37.9730769 L22.7369986,37.9730769 C23.0746191,37.9730769 23.3957405,37.6857973 23.3957405,37.2879181 L23.3957405,10.6305829 C23.3957405,10.2327001 23.0946315,9.88282031 22.7589391,9.88282031 L20.9098012,9.88282031 C20.5741088,9.88282031 20.2729998,10.2327001 20.2729998,10.6305829 L20.2729998,35.3560876 C20.2729998,35.4523364 20.2072372,35.5232681 20.1036277,35.5571062 L18.0488238,36.2317178 C17.8255631,36.3028337 17.7039138,36.1932083 17.7039138,35.9622126 L17.7039138,6.13568326 C17.7039138,5.93547684 17.7606884,5.80666039 17.8783738,5.74467049 C17.9840993,5.68935422 18.1247607,5.70480386 18.2916546,5.76026115 L21.8914305,6.95996772 C22.1805909,7.06441206 22.5107983,6.77939116 22.5107983,6.39162492 L22.5107983,3.36825709 C22.5107983,2.86395117 22.238658,2.52288986 21.8608902,2.44797259 L11.4669125,0.358365126 C11.1738427,0.296456448 10.6876295,0.259678814 10.3445,0.259678814 C10.0013705,0.259678814 9.61020382,0.296456448 9.34136956,0.358365126" fill="#696CFF"></path>
              </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">ShopBD</span>
          </a>
          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
          </a>
        </div>
        <div class="menu-inner-shadow"></div>
        <ul class="menu-inner py-1">

          <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Analytics">Dashboard</div>
            </a>
          </li>

          <li class="menu-header small text-uppercase"><span class="menu-header-text">Catalog</span></li>

          @hasanyrole('super-admin|admin|manager')
          <li class="menu-item {{ request()->routeIs('admin.products.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-package"></i>
              <div>Products</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                <a href="{{ route('admin.products.index') }}" class="menu-link"><div>All Products</div></a>
              </li>
              <li class="menu-item {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                <a href="{{ route('admin.products.create') }}" class="menu-link"><div>Add Product</div></a>
              </li>
            </ul>
          </li>
          <li class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-category"></i>
              <div>Categories</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                <a href="{{ route('admin.categories.index') }}" class="menu-link"><div>All Categories</div></a>
              </li>
              <li class="menu-item {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}">
                <a href="{{ route('admin.categories.create') }}" class="menu-link"><div>Add Category</div></a>
              </li>
            </ul>
          </li>
          @endhasanyrole

          <li class="menu-header small text-uppercase"><span class="menu-header-text">Sales</span></li>

          <li class="menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <a href="{{ route('admin.orders.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-cart"></i>
              <div>Orders</div>
              @php try { $pendingOrders = \App\Models\Order::where('status','pending')->count(); } catch(\Exception $e) { $pendingOrders = 0; } @endphp
              @if($pendingOrders > 0)<div class="badge bg-danger rounded-pill ms-auto">{{ $pendingOrders }}</div>@endif
            </a>
          </li>

          @hasanyrole('super-admin|admin|manager')
          <li class="menu-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
            <a href="{{ route('admin.coupons.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-tag"></i>
              <div>Coupons</div>
            </a>
          </li>
          @endhasanyrole

          @hasanyrole('super-admin|admin')
          <li class="menu-header small text-uppercase"><span class="menu-header-text">Users</span></li>
          <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-user"></i>
              <div>Users</div>
            </a>
          </li>
          @hasanyrole('super-admin')
          <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <a href="{{ route('admin.roles.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-shield"></i>
              <div>Roles & Permissions</div>
            </a>
          </li>
          @endhasanyrole
          @endhasanyrole

          @hasanyrole('super-admin|admin|manager')
          <li class="menu-header small text-uppercase"><span class="menu-header-text">Content</span></li>
          <li class="menu-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
            <a href="{{ route('admin.banners.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-image"></i>
              <div>Banners</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reviews.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-star"></i>
              <div>Reviews</div>
              @php try { $pendingReviews = \App\Models\Review::where('is_approved',false)->count(); } catch(\Exception $e) { $pendingReviews = 0; } @endphp
              @if($pendingReviews > 0)<div class="badge bg-warning rounded-pill ms-auto">{{ $pendingReviews }}</div>@endif
            </a>
          </li>
          @endhasanyrole

          @hasanyrole('super-admin|admin')
          <li class="menu-header small text-uppercase"><span class="menu-header-text">Analytics</span></li>
          <li class="menu-item {{ request()->routeIs('admin.reports.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
              <div>Reports</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item"><a href="{{ route('admin.reports.sales') }}" class="menu-link"><div>Sales Report</div></a></li>
              <li class="menu-item"><a href="{{ route('admin.reports.products') }}" class="menu-link"><div>Product Report</div></a></li>
              <li class="menu-item"><a href="{{ route('admin.reports.customers') }}" class="menu-link"><div>Customer Report</div></a></li>
            </ul>
          </li>
          @endhasanyrole

          @hasanyrole('super-admin')
          <li class="menu-header small text-uppercase"><span class="menu-header-text">Configuration</span></li>
          <li class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-cog"></i>
              <div>Settings</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item"><a href="{{ route('admin.settings.general') }}" class="menu-link"><div>General</div></a></li>
              <li class="menu-item"><a href="{{ route('admin.settings.payment') }}" class="menu-link"><div>Payment</div></a></li>
              <li class="menu-item"><a href="{{ route('admin.settings.shipping') }}" class="menu-link"><div>Shipping</div></a></li>
              <li class="menu-item"><a href="{{ route('admin.settings.email') }}" class="menu-link"><div>Email</div></a></li>
            </ul>
          </li>
          @endhasanyrole

          <li class="menu-header small text-uppercase"><span class="menu-header-text">Account</span></li>
          <li class="menu-item">
            <a href="{{ route('home') }}" class="menu-link" target="_blank">
              <i class="menu-icon tf-icons bx bx-store"></i>
              <div>View Storefront</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="#" class="menu-link" onclick="event.preventDefault();document.getElementById('logout-sidebar').submit();">
              <i class="menu-icon tf-icons bx bx-power-off"></i>
              <div>Logout</div>
            </a>
            <form id="logout-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
          </li>

        </ul>
      </aside>
      <!-- /Sidebar -->

      <div class="layout-page">
        <!-- Navbar -->
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
              <i class="bx bx-menu bx-sm"></i>
            </a>
          </div>
          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

            <!-- Admin Live Search -->
            <div class="navbar-nav align-items-center w-100 me-4" style="max-width:380px;position:relative">
              <div class="nav-item w-100">
                <div class="input-group">
                  <span class="input-group-text bg-transparent border-end-0"><i class="bx bx-search"></i></span>
                  <input type="text" id="adminSearchInput" class="form-control border-start-0 ps-0" placeholder="Search products, orders..." autocomplete="off">
                </div>
                <div id="adminSearchResults" class="position-absolute bg-white border rounded-3 shadow-lg w-100 d-none" style="top:100%;left:0;z-index:9999;max-height:360px;overflow-y:auto"></div>
              </div>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <li class="nav-item me-2 me-xl-0">
                <a class="nav-link" href="{{ route('admin.orders.index') }}" title="Orders">
                  <i class="bx bx-cart bx-sm"></i>
                  @if(isset($pendingOrders) && $pendingOrders > 0)
                    <span class="badge bg-danger badge-notifications">{{ $pendingOrders }}</span>
                  @endif
                </a>
              </li>
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <img src="{{ auth()->user()->avatar_url ?? asset('sneat-assets/img/avatars/1.png') }}" class="w-px-40 h-auto rounded-circle" />
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="#">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <img src="{{ auth()->user()->avatar_url ?? asset('sneat-assets/img/avatars/1.png') }}" class="w-px-40 h-auto rounded-circle" />
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                          <small class="text-muted">{{ implode(', ', auth()->user()->getRoleNames()->toArray()) }}</small>
                        </div>
                      </div>
                    </a>
                  </li>
                  <li><div class="dropdown-divider"></div></li>
                  <li><a class="dropdown-item" href="{{ route('admin.settings.general') }}"><i class="bx bx-cog me-2"></i>Settings</a></li>
                  <li><div class="dropdown-divider"></div></li>
                  <li>
                    <a class="dropdown-item" href="#" onclick="event.preventDefault();document.getElementById('logout-nav').submit();">
                      <i class="bx bx-power-off me-2 text-danger"></i><span class="text-danger">Log Out</span>
                    </a>
                    <form id="logout-nav" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
        <!-- /Navbar -->

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">

            @hasSection('page_title')
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
              <div>
                <h4 class="fw-bold mb-0">@yield('page_title')</h4>
                @yield('breadcrumb')
              </div>
              <div>@yield('page_actions')</div>
            </div>
            @endif

            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show mb-4">
                <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            @if(session('error'))
              <div class="alert alert-danger alert-dismissible fade show mb-4">
                <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            @if($errors->any())
              <div class="alert alert-danger alert-dismissible fade show mb-4">
                <strong>Please fix the errors:</strong>
                <ul class="mb-0 mt-1">
                  @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            @yield('content')
          </div>

          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
              <div class="mb-2 mb-md-0">© {{ date('Y') }} <a href="{{ route('home') }}" class="footer-link fw-bolder">ShopBD</a></div>
              <div>Powered by <a href="#" class="footer-link">Laravel + Sneat</a></div>
            </div>
          </footer>
          <div class="content-backdrop fade"></div>
        </div>
      </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>

  <script src="{{ asset('sneat-assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('sneat-assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('sneat-assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('sneat-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('sneat-assets/vendor/js/menu.js') }}"></script>
  <script src="{{ asset('sneat-assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
  <script src="{{ asset('sneat-assets/js/main.js') }}"></script>

  <script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    setTimeout(() => { document.querySelectorAll('.alert').forEach(el => bootstrap.Alert.getOrCreateInstance(el).close()); }, 5000);

    // Admin Live Search
    let adminSearchTimer;
    const adminInput = document.getElementById('adminSearchInput');
    const adminResults = document.getElementById('adminSearchResults');

    if(adminInput) {
      adminInput.addEventListener('input', function() {
        clearTimeout(adminSearchTimer);
        const q = this.value.trim();
        if(q.length < 2) { adminResults.classList.add('d-none'); return; }
        adminSearchTimer = setTimeout(() => {
          fetch(`/admin/search?q=${encodeURIComponent(q)}`, { headers:{'X-Requested-With':'XMLHttpRequest'} })
            .then(r => r.json()).then(data => {
              if(!data.length) { adminResults.innerHTML = '<div class="p-3 text-muted text-center small">No results found</div>'; }
              else {
                adminResults.innerHTML = data.map(item => `
                  <a href="${item.url}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-dark border-bottom hover-bg" style="transition:.15s">
                    <img src="${item.image}" style="width:40px;height:40px;object-fit:cover;border-radius:6px" onerror="this.src='/images/placeholder-product.svg'">
                    <div class="flex-grow-1 overflow-hidden">
                      <p class="mb-0 fw-600 text-truncate" style="font-size:.85rem">${item.title}</p>
                      <small class="text-muted">${item.type} ${item.meta ? '· ' + item.meta : ''}</small>
                    </div>
                  </a>`).join('');
              }
              adminResults.classList.remove('d-none');
            }).catch(() => adminResults.classList.add('d-none'));
        }, 300);
      });
      document.addEventListener('click', e => { if(!adminInput.contains(e.target) && !adminResults.contains(e.target)) adminResults.classList.add('d-none'); });
    }
  </script>
  @stack('scripts')
</body>
</html>
