@extends('layouts.admin.app')
@section('title','Coupons')
@section('page_title','Coupons')
@section('page_actions')
<a href="{{ route('admin.coupons.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Add Coupon</a>
@endsection
@section('content')
<div class="card">
  <div class="card-header"><h5 class="mb-0">All Coupons</h5></div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Usage</th><th>Expires</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($coupons as $coupon)
        <tr>
          <td><span class="badge bg-dark fs-6 font-monospace">{{ $coupon->code }}</span></td>
          <td><span class="badge bg-label-info">{{ ucfirst($coupon->type) }}</span></td>
          <td class="fw-semibold">{{ $coupon->type==='percentage'?$coupon->value.'%':'৳'.number_format($coupon->value,0) }}</td>
          <td>৳{{ number_format($coupon->min_order_amount,0) }}</td>
          <td>{{ $coupon->used_count }}/{{ $coupon->max_uses ?? '∞' }}</td>
          <td>{{ $coupon->expires_at?$coupon->expires_at->format('d M Y'):'Never' }}</td>
          <td>
            <span class="badge bg-label-{{ $coupon->is_active&&$coupon->isValid()?'success':'danger' }}">
              {{ $coupon->is_active&&$coupon->isValid()?'Active':'Inactive' }}
            </span>
          </td>
          <td>
            <a href="{{ route('admin.coupons.edit',$coupon) }}" class="btn btn-sm btn-icon btn-outline-primary"><i class="bx bx-edit"></i></a>
            <form action="{{ route('admin.coupons.toggle',$coupon) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-sm btn-icon btn-outline-{{ $coupon->is_active?'warning':'success' }}" title="{{ $coupon->is_active?'Disable':'Enable' }}">
                <i class="bx bx-{{ $coupon->is_active?'pause':'play' }}"></i>
              </button>
            </form>
            <form action="{{ route('admin.coupons.destroy',$coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete coupon?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-4">No coupons yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($coupons->hasPages())<div class="card-footer">{{ $coupons->links() }}</div>@endif
</div>
@endsection
