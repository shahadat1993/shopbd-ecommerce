@extends('layouts.admin.app')
@section('title','Banners')
@section('page_title','Banners')
@section('page_actions')
<a href="{{ route('admin.banners.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Add Banner</a>
@endsection
@section('content')
<div class="card">
  <div class="card-header"><h5 class="mb-0">All Banners</h5></div>
  <div class="row g-3 p-3">
    @forelse($banners as $banner)
    <div class="col-md-6 col-xl-4">
      <div class="card border h-100">
        <img src="{{ asset('storage/'.$banner->image) }}" class="card-img-top" alt="{{ $banner->title }}" style="height:160px;object-fit:cover">
        <div class="card-body">
          <h6 class="card-title mb-1">{{ $banner->title }}</h6>
          @if($banner->subtitle)<p class="text-muted small mb-1">{{ $banner->subtitle }}</p>@endif
          <div class="d-flex justify-content-between align-items-center mt-2">
            <span class="badge bg-label-info">{{ ucfirst($banner->position) }}</span>
            <span class="badge bg-label-{{ $banner->is_active?'success':'danger' }}">{{ $banner->is_active?'Active':'Inactive' }}</span>
          </div>
        </div>
        <div class="card-footer d-flex gap-2">
          <a href="{{ route('admin.banners.edit',$banner) }}" class="btn btn-sm btn-outline-primary flex-fill">Edit</a>
          <form action="{{ route('admin.banners.destroy',$banner) }}" method="POST" class="flex-fill" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
          </form>
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">No banners yet.</div>
    @endforelse
  </div>
  @if($banners->hasPages())<div class="px-3 pb-3">{{ $banners->links() }}</div>@endif
</div>
@endsection
