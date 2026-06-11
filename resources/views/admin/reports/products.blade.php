@extends('layouts.admin.app')
@section('title','Sales Report')
@section('page_title','Sales Report')
@section('content')
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label small">From</label>
        <input type="date" name="from" class="form-control" value="{{ request('from',$from->format('Y-m-d')) }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small">To</label>
        <input type="date" name="to" class="form-control" value="{{ request('to',$to->format('Y-m-d')) }}">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary">Apply</button>
      </div>
      <div class="col-md-4 text-end">
        <a href="{{ route('admin.reports.export','orders') }}" class="btn btn-outline-success">
          <i class="bx bx-export me-1"></i> Export CSV
        </a>
      </div>
    </form>
  </div>
</div>
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card"><div class="card-body text-center">
      <h6 class="text-muted small mb-1">Total Orders</h6>
      <h3 class="fw-bold">{{ $summary['total_orders'] }}</h3>
    </div></div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card"><div class="card-body text-center">
      <h6 class="text-muted small mb-1">Total Revenue</h6>
      <h3 class="fw-bold text-success">৳{{ number_format($summary['total_revenue'],0) }}</h3>
    </div></div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card"><div class="card-body text-center">
      <h6 class="text-muted small mb-1">Avg Order Value</h6>
      <h3 class="fw-bold">৳{{ number_format($summary['avg_order_value'],0) }}</h3>
    </div></div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card"><div class="card-body text-center">
      <h6 class="text-muted small mb-1">Items Sold</h6>
      <h3 class="fw-bold">{{ $summary['total_items'] }}</h3>
    </div></div>
  </div>
</div>
<div class="card">
  <div class="card-header"><h5 class="mb-0">Daily Revenue</h5></div>
  <div class="card-body"><div id="salesChart"></div></div>
</div>
@endsection
@push('scripts')
<script>
new ApexCharts(document.querySelector("#salesChart"), {
  series: [{ name: 'Revenue (৳)', data: {!! json_encode($daily->pluck('revenue')) !!} }],
  chart: { height: 300, type: 'area', toolbar: {show:false} },
  colors: ['#696cff'],
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth', width: 2 },
  fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
  xaxis: { categories: {!! json_encode($daily->pluck('date')) !!} },
  yaxis: { labels: { formatter: v => '৳'+Number(v).toLocaleString() } },
  tooltip: { y: { formatter: v => '৳'+Number(v).toLocaleString() } }
}).render();
</script>
@endpush
