@extends('layouts.frontend.app')
@section('title','Profile Settings')

@section('content')
<div class="breadcrumb-section">
  <div class="container">
    <nav><ol class="breadcrumb mb-0 small">
      <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('account.dashboard') }}" class="text-decoration-none">Account</a></li>
      <li class="breadcrumb-item active">Profile Settings</li>
    </ol></nav>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <div class="row g-4">

      {{-- Sidebar --}}
      <div class="col-12 col-md-3">
        <div class="card">
          <div class="card-body text-center py-4">
            <img src="{{ $user->avatar_url }}" class="rounded-circle border mb-3" width="80" height="80" alt="{{ $user->name }}" style="object-fit:cover">
            <h6 class="fw-700 mb-0">{{ $user->name }}</h6>
            <p class="text-muted small mb-0">{{ $user->email }}</p>
          </div>
          <div class="list-group list-group-flush rounded-bottom">
            <a href="{{ route('account.dashboard') }}" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action"><i class="bi bi-bag me-2"></i>My Orders</a>
            <a href="{{ route('wishlist.index') }}" class="list-group-item list-group-item-action"><i class="bi bi-heart me-2"></i>Wishlist</a>
            <a href="{{ route('account.addresses') }}" class="list-group-item list-group-item-action"><i class="bi bi-geo-alt me-2"></i>Addresses</a>
            <a href="{{ route('account.profile') }}" class="list-group-item list-group-item-action active"><i class="bi bi-person-gear me-2"></i>Profile Settings</a>
          </div>
        </div>
      </div>

      {{-- Content --}}
      <div class="col-12 col-md-9">

        {{-- Profile Info --}}
        <div class="card mb-4">
          <div class="card-header fw-700"><i class="bi bi-person me-2"></i>Personal Information</div>
          <div class="card-body">
            {{-- IMPORTANT: enctype must be multipart/form-data for file upload --}}
            <form action="{{ route('account.profile.update') }}" method="POST" enctype="multipart/form-data">
              @csrf @method('PUT')

              {{-- Avatar Upload --}}
              <div class="d-flex align-items-center gap-4 mb-4 p-3 rounded-3" style="background:#f8f9fc;border:1px dashed #dee2e6">
                <div class="position-relative">
                  <img id="avatarPreview" src="{{ $user->avatar_url }}" class="rounded-circle border-3 border border-primary" style="width:90px;height:90px;object-fit:cover" alt="{{ $user->name }}">
                  <label for="avatarFile" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle p-1" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer" title="Change photo">
                    <i class="bi bi-camera" style="font-size:13px"></i>
                  </label>
                </div>
                <div>
                  <p class="mb-1 fw-600">Profile Photo</p>
                  <p class="text-muted small mb-2">JPG, PNG, GIF or WEBP. Max size: 2MB</p>
                  <input type="file" name="avatar" id="avatarFile" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewAvatar(this)">
                  <label for="avatarFile" class="btn btn-outline-primary btn-sm" style="cursor:pointer">
                    <i class="bi bi-upload me-1"></i> Upload Photo
                  </label>
                </div>
              </div>

              @if($errors->has('avatar'))
                <div class="alert alert-danger alert-sm mb-3 py-2">{{ $errors->first('avatar') }}</div>
              @endif

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-600">Full Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}" required>
                  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-600">Email Address</label>
                  <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                  <small class="text-muted">Email cannot be changed.</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-600">Phone Number</label>
                  <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+8801XXXXXXXXX">
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4 fw-600">
                  <i class="bi bi-save me-2"></i>Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>

        {{-- Change Password --}}
        <div class="card">
          <div class="card-header fw-700"><i class="bi bi-lock me-2"></i>Change Password</div>
          <div class="card-body">
            <form action="{{ route('account.password.change') }}" method="POST">
              @csrf @method('PUT')
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-600">Current Password <span class="text-danger">*</span></label>
                  <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                  @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-600">New Password <span class="text-danger">*</span></label>
                  <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8" autocomplete="new-password">
                  @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-600">Confirm New Password <span class="text-danger">*</span></label>
                  <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                </div>
              </div>
              <div class="mt-4">
                <button type="submit" class="btn btn-warning px-4 fw-600">
                  <i class="bi bi-key me-2"></i>Update Password
                </button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    // Validate size
    if (file.size > 2 * 1024 * 1024) {
      alert('Image size must be less than 2MB');
      input.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('avatarPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}
</script>
@endpush
