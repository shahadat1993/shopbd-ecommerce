@extends('layouts.admin.app')
@section('title', isset($user) ? 'Edit User' : 'Add User')
@section('page_title', isset($user) ? 'Edit User' : 'Add User')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb breadcrumb-style1 mb-0">
  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
  <li class="breadcrumb-item active">{{ isset($user) ? 'Edit' : 'Add' }}</li>
</ol></nav>
@endsection

@section('content')
@php
  $action = isset($user) ? route('admin.users.update', $user) : route('admin.users.store');
@endphp
<div class="row justify-content-center">
  <div class="col-12 col-xl-8">
    <div class="card">
      <div class="card-header"><h5 class="mb-0">{{ isset($user) ? 'Edit User' : 'Create New User' }}</h5></div>
      <div class="card-body">
        <form action="{{ $action }}" method="POST">
          @csrf
          @if(isset($user)) @method('PUT') @endif

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name ?? '') }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Email Address <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email ?? '') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+8801XXXXXXXXX">
            </div>
            <div class="col-md-6">
              <label class="form-label">Role <span class="text-danger">*</span></label>
              <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">— Select Role —</option>
                @foreach($roles as $role)
                  <option value="{{ $role->name }}" {{ old('role', isset($user) ? $user->roles->first()?->name : '') === $role->name ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                  </option>
                @endforeach
              </select>
              @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Password {{ isset($user) ? '(leave blank to keep)' : '' }} <span class="text-danger">{{ !isset($user)?'*':'' }}</span></label>
              <div class="input-group">
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                  {{ !isset($user)?'required':'' }} autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password')">
                  <i class="bx bx-show"></i>
                </button>
              </div>
              @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control" {{ !isset($user)?'required':'' }}>
            </div>
            @if(isset($user))
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Account Active</label>
              </div>
            </div>
            @endif
          </div>

          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-save me-1"></i> {{ isset($user) ? 'Update User' : 'Create User' }}
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function togglePass(id) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
