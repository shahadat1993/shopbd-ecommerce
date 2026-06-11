@extends('layouts.admin.app')
@section('title','Categories')
@section('page_title','Categories')
@section('page_actions')
<a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Add Category</a>
@endsection
@section('content')
<div class="card">
  <div class="card-header"><h5 class="mb-0">All Categories <span class="badge bg-label-primary ms-1">{{ $categories->total() }}</span></h5></div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Image</th><th>Name</th><th>Parent</th><th>Products</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($categories as $cat)
        <tr>
          <td>
            @if($cat->image)
              <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}" class="rounded" style="width:44px;height:44px;object-fit:cover">
            @else
              <div class="bg-label-secondary rounded d-flex align-items-center justify-content-center" style="width:44px;height:44px;font-size:20px">📁</div>
            @endif
          </td>
          <td class="fw-semibold">{{ $cat->name }}</td>
          <td>{{ $cat->parent?->name ?? '— (Root)' }}</td>
          <td><span class="badge bg-label-primary">{{ $cat->products_count }}</span></td>
          <td><span class="badge bg-label-{{ $cat->is_active?'success':'danger' }}">{{ $cat->is_active?'Active':'Inactive' }}</span></td>
          <td>
            <a href="{{ route('admin.categories.edit',$cat) }}" class="btn btn-sm btn-icon btn-outline-primary"><i class="bx bx-edit"></i></a>
            <form action="{{ route('admin.categories.destroy',$cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No categories yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($categories->hasPages())
  <div class="card-footer">{{ $categories->links() }}</div>
  @endif
</div>
@endsection
