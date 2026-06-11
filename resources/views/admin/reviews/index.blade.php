@extends('layouts.admin.app')
@section('title','Reviews')
@section('page_title','Customer Reviews')
@section('content')
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-3">
      <div class="col-md-4">
        <select name="status" class="form-select">
          <option value="">All Reviews</option>
          <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
          <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Approved</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-header"><h5 class="mb-0">Reviews <span class="badge bg-label-primary ms-1">{{ $reviews->total() }}</span></h5></div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Customer</th><th>Product</th><th>Rating</th><th>Comment</th><th>Status</th><th>Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($reviews as $review)
        <tr>
          <td class="fw-semibold">{{ $review->user->name }}</td>
          <td class="text-truncate" style="max-width:150px">{{ $review->product->name }}</td>
          <td>
            @for($i=1;$i<=5;$i++)
              <i class="bx bx{{ $i<=$review->rating?'s':'' }}-star {{ $i<=$review->rating?'text-warning':'text-muted' }}"></i>
            @endfor
          </td>
          <td class="text-truncate" style="max-width:200px">{{ $review->comment }}</td>
          <td>
            <span class="badge bg-label-{{ $review->is_approved?'success':'warning' }}">
              {{ $review->is_approved?'Approved':'Pending' }}
            </span>
          </td>
          <td class="text-muted small">{{ $review->created_at->format('d M Y') }}</td>
          <td>
            <form action="{{ route('admin.reviews.approve',$review) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-sm btn-icon btn-outline-{{ $review->is_approved?'warning':'success' }}" title="{{ $review->is_approved?'Unapprove':'Approve' }}">
                <i class="bx bx-{{ $review->is_approved?'x':'check' }}"></i>
              </button>
            </form>
            <form action="{{ route('admin.reviews.destroy',$review) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete review?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">No reviews found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($reviews->hasPages())<div class="card-footer">{{ $reviews->withQueryString()->links() }}</div>@endif
</div>
@endsection
