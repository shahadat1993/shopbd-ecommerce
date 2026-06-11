@extends('layouts.admin.app')
@section('title', isset($role)?'Edit Role':'Create Role')
@section('page_title', isset($role)?'Edit Role':'Create Role')
@section('content')
@php $action = isset($role)?route('admin.roles.update',$role):route('admin.roles.store'); @endphp
<form action="{{ $action }}" method="POST">
  @csrf
  @if(isset($role)) @method('PUT') @endif
  <div class="row g-4">
    <div class="col-12 col-xl-4">
      <div class="card">
        <div class="card-header"><h5 class="mb-0">Role Details</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Role Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name',$role->name??'') }}" placeholder="e.g. editor" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn btn-primary w-100"><i class="bx bx-save me-1"></i> Save Role</button>
          <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-8">
      <div class="card">
        <div class="card-header"><h5 class="mb-0">Permissions</h5></div>
        <div class="card-body">
          @foreach($permissions as $group => $perms)
          <div class="mb-4">
            <h6 class="text-uppercase text-muted small fw-bold mb-2 border-bottom pb-1">{{ ucfirst($group) }}</h6>
            <div class="row g-2">
              @foreach($perms as $perm)
              <div class="col-md-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="permissions[]"
                    value="{{ $perm->name }}" id="perm_{{ $perm->id }}"
                    {{ isset($rolePermissions) && in_array($perm->name,$rolePermissions)?'checked':'' }}>
                  <label class="form-check-label small" for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
