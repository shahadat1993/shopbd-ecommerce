@extends('layouts.admin.app')
@section('title', isset($banner)?'Edit Banner':'New Banner')
@section('page_title', isset($banner)?'Edit Banner':'New Banner')
@section('content')
@php $action = isset($banner)?route('admin.banners.update',$banner):route('admin.banners.store'); @endphp
<div class="row justify-content-center">
<div class="col-12 col-xl-7">
<div class="card">
  <div class="card-body">
    <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
      @csrf @if(isset($banner)) @method('PUT') @endif
      <div class="mb-3">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="{{ old('title',$banner->title??'') }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Subtitle</label>
        <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle',$banner->subtitle??'') }}">
      </div>
      <div class="mb-3">
        <label class="form-label">Link URL</label>
        <input type="text" name="link" class="form-control" value="{{ old('link',$banner->link??'') }}" placeholder="https://...">
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Position <span class="text-danger">*</span></label>
          <select name="position" class="form-select">
            <option value="hero" {{ old('position',$banner->position??'')==='hero'?'selected':'' }}>Hero (Main Slider)</option>
            <option value="sidebar" {{ old('position',$banner->position??'')==='sidebar'?'selected':'' }}>Sidebar</option>
            <option value="promo" {{ old('position',$banner->position??'')==='promo'?'selected':'' }}>Promo Bar</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order',$banner->sort_order??0) }}" min="0">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Banner Image {{ isset($banner)?'(leave empty to keep)':'' }} <span class="text-danger">{{ !isset($banner)?'*':'' }}</span></label>
        @if(isset($banner) && $banner->image)
          <div class="mb-2"><img src="{{ asset('storage/'.$banner->image) }}" class="img-thumbnail" style="max-height:100px"></div>
        @endif
        <input type="file" name="image" class="form-control" accept="image/*" {{ !isset($banner)?'required':'' }}>
        <small class="text-muted">Recommended: 1920×600px for hero banners</small>
      </div>
      <div class="form-check form-switch mb-4">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active',$banner->is_active??true)?'checked':'' }}>
        <label class="form-check-label">Active</label>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save Banner</button>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
</div>
</div>
@endsection
