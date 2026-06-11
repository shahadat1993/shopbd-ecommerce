@extends('layouts.admin.app')
@section('title','Dashboard')
@section('page_title','Analytics Dashboard')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb breadcrumb-style1 mb-0">
  <li class="breadcrumb-item active">Dashboard</li>
</ol></nav>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-start p-4">
        <div>
          <span class="d-block text-muted small fw-semibold mb-1">Total Revenue</span>
          <h3 class="fw-bold mb-1">৳{{ number_format($stats['total_revenue'],0) }}</h3>
          <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> ৳{{ number_format($stats['today_revenue'],0) }} today</small>
        </div>
        <div class="rounded-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(105,108,255,.1)">
          <i class="bx bx-dollar text-primary fs-3"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-start p-4">
        <div>
          <span class="d-block text-muted small fw-semibold mb-1">Total Orders</span>
          <h3 class="fw-bold mb-1">{{ number_format($stats['total_orders']) }}</h3>
          <small class="text-warning fw-semibold"><i class="bx bx-time"></i> {{ $stats['pending_orders'] }} pending</small>
        </div>
        <div class="rounded-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(255,171,0,.1)">
          <i class="bx bx-cart text-warning fs-3"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-start p-4">
        <div>
          <span class="d-block text-muted small fw-semibold mb-1">Products</span>
          <h3 class="fw-bold mb-1">{{ number_format($stats['total_products']) }}</h3>
          <small class="text-danger fw-semibold"><i class="bx bx-error"></i> {{ $stats['out_of_stock'] }} out of stock</small>
        </div>
        <div class="rounded-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(113,221,55,.1)">
          <i class="bx bx-package text-success fs-3"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-start p-4">
        <div>
          <span class="d-block text-muted small fw-semibold mb-1">Customers</span>
          <h3 class="fw-bold mb-1">{{ number_format($stats['total_customers']) }}</h3>
          <small class="text-success fw-semibold"><i class="bx bx-user-plus"></i> +{{ $stats['new_customers'] }} today</small>
        </div>
        <div class="rounded-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(3,195,236,.1)">
          <i class="bx bx-user text-info fs-3"></i>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Charts --}}
<div class="row g-4 mb-4">
  <div class="col-12 col-xl-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div><h5 class="card-title mb-0">Revenue & Orders</h5><small class="text-muted">Last 6 months</small></div>
      </div>
      <div class="card-body pt-2">
        <div id="revenueChart" style="min-height:280px"></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-4">
    <div class="card h-100">
      <div class="card-header"><h5 class="card-title mb-0">Order Status</h5></div>
      <div class="card-body">
        <div id="orderStatusChart" style="min-height:200px"></div>
        <div class="mt-3">
          @foreach($orderStatusData as $status => $count)
            @if($count > 0)
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge bg-label-{{ ['pending'=>'warning','confirmed'=>'info','processing'=>'primary','shipped'=>'secondary','delivered'=>'success','cancelled'=>'danger','refunded'=>'dark'][$status]??'secondary' }} text-capitalize" style="font-size:.8rem">{{ $status }}</span>
              <span class="fw-semibold">{{ $count }}</span>
            </div>
            @endif
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Bottom Row --}}
<div class="row g-4">
  <div class="col-12 col-xl-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Recent Orders</h5>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th></tr>
          </thead>
          <tbody>
            @forelse($recentOrders as $order)
            <tr>
              <td><a href="{{ route('admin.orders.show',$order) }}" class="fw-semibold text-primary">#{{ $order->order_number }}</a></td>
              <td><div class="fw-semibold" style="font-size:.9rem">{{ $order->shipping_name }}</div><small class="text-muted">{{ $order->shipping_phone }}</small></td>
              <td class="fw-semibold">৳{{ number_format($order->total,0) }}</td>
              <td><span class="badge bg-label-{{ $order->status_badge }} text-capitalize">{{ $order->status }}</span></td>
              <td><span class="badge bg-label-{{ $order->payment_status_badge }} text-capitalize">{{ $order->payment_status }}</span></td>
              <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4"><i class="bx bx-cart fs-2 d-block mb-2 opacity-25"></i>No orders yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Top Selling</h5>
        <a href="{{ route('admin.reports.products') }}" class="btn btn-sm btn-outline-secondary">Report</a>
      </div>
      <div class="card-body p-0">
        @forelse($topProducts as $i => $product)
        <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last?'border-bottom':'' }}">
          <span class="badge bg-label-primary fw-bold" style="min-width:24px">{{ $i+1 }}</span>
          <img src="{{ $product->thumbnail_url }}" class="rounded-2 flex-shrink-0" style="width:36px;height:36px;object-fit:cover">
          <div class="flex-grow-1 overflow-hidden">
            <p class="mb-0 fw-semibold text-truncate" style="font-size:.85rem">{{ $product->name }}</p>
            <small class="text-muted">{{ $product->order_items_count }} sold · Stock: {{ $product->stock }}</small>
          </div>
        </div>
        @empty
        <p class="text-center text-muted py-4 mb-0">No sales data yet.</p>
        @endforelse
      </div>
    </div>

    @if($stats['low_stock'] > 0 || $stats['pending_reviews'] > 0)
    <div class="card border-warning">
      <div class="card-header" style="background:rgba(255,171,0,.1)">
        <h5 class="mb-0 text-warning"><i class="bx bx-error me-1"></i>Alerts</h5>
      </div>
      <div class="card-body">
        @if($stats['low_stock'] > 0)
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted small">Low stock products</span>
          <a href="{{ route('admin.products.index',['stock'=>'low']) }}" class="badge bg-warning text-dark">{{ $stats['low_stock'] }}</a>
        </div>
        @endif
        @if($stats['pending_reviews'] > 0)
        <div class="d-flex justify-content-between align-items-center">
          <span class="text-muted small">Pending reviews</span>
          <a href="{{ route('admin.reviews.index',['status'=>'pending']) }}" class="badge bg-info">{{ $stats['pending_reviews'] }}</a>
        </div>
        @endif
      </div>
    </div>
    @endif
  </div>
</div>

@endsection

@push('scripts')
<script>
var revenueOpts = {
  series:[
    {name:'Revenue (৳)',data:{!! json_encode(array_column($monthlyRevenue,'revenue')) !!}},
    {name:'Orders',data:{!! json_encode(array_column($monthlyOrders,'count')) !!}}
  ],
  chart:{height:280,type:'bar',toolbar:{show:false}},
  colors:['#696cff','#03c3ec'],
  plotOptions:{bar:{borderRadius:4,columnWidth:'55%',dataLabels:{position:'top'}}},
  dataLabels:{enabled:false},
  xaxis:{categories:{!! json_encode(array_column($monthlyRevenue,'month')) !!},labels:{style:{fontSize:'12px'}}},
  yaxis:[{title:{text:'Revenue'},labels:{formatter:v=>'৳'+Number(v).toLocaleString()}},{opposite:true,title:{text:'Orders'}}],
  tooltip:{y:{formatter:(val,{seriesIndex})=>seriesIndex===0?'৳'+Number(val).toLocaleString():val+' orders'}},
  legend:{position:'top'},grid:{borderColor:'#e7e7e7'}
};
if(document.querySelector('#revenueChart')) new ApexCharts(document.querySelector('#revenueChart'),revenueOpts).render();

var statusData={!! json_encode($orderStatusData) !!};
var nonZero=Object.entries(statusData).filter(([,v])=>v>0);
var statusOpts={
  series:nonZero.map(([,v])=>v),
  labels:nonZero.map(([k])=>k.charAt(0).toUpperCase()+k.slice(1)),
  chart:{height:200,type:'donut'},
  colors:['#ffab00','#03c3ec','#696cff','#8592a3','#71dd37','#ff3e1d','#495057'],
  dataLabels:{enabled:false},
  legend:{show:false},
  plotOptions:{pie:{donut:{size:'70%',labels:{show:true,total:{show:true,label:'Total',formatter:()=>nonZero.reduce((a,[,v])=>a+v,0)}}}}}
};
if(document.querySelector('#orderStatusChart')&&nonZero.length) new ApexCharts(document.querySelector('#orderStatusChart'),statusOpts).render();
</script>
@endpush
