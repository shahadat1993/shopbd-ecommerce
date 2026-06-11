@extends('layouts.admin.app')
@section('title', isset($category)?'Edit Category':'Add Category')
@section('page_title', isset($category)?'Edit Category':'Add Category')
@section('content')
@php $action = isset($category)?route('admin.categories.update',$category):route('admin.categories.store'); @endphp
<div class="row justify-content-center">
<div class="col-12 col-xl-7">
<div class="card">
  <div class="card-header"><h5 class="mb-0">{{ isset($category)?'Edit':'New' }} Category</h5></div>
  <div class="card-body">
    <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
      @csrf @if(isset($category)) @method('PUT') @endif
      <div class="mb-3">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$category->name??'') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Parent Category</label>
        <select name="parent_id" class="form-select">
          <option value="">— Root Category —</option>
          @foreach($parents as $p)
            <option value="{{ $p->id }}" {{ old('parent_id',$category->parent_id??'')==$p->id?'selected':'' }}>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description',$category->description??'') }}</textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Image</label>
        @if(isset($category) && $category->image)
          <div class="mb-2"><img src="{{ asset('storage/'.$category->image) }}" class="img-thumbnail" style="max-height:80px"></div>
        @endif
        <input type="file" name="image" class="form-control" accept="image/*">
      </div>
      <div class="mb-3">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order',$category->sort_order??0) }}" min="0">
      </div>
      <div class="form-check form-switch mb-4">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active',$category->is_active??true)?'checked':'' }}>
        <label class="form-check-label">Active</label>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
</div>
</div>
@endsection
