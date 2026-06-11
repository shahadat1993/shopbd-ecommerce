<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — ShopBD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(135deg,#6c3fe0 0%,#ff6b35 100%); min-height:100vh; display:flex; align-items:center; font-family:'Segoe UI',sans-serif; padding:30px 0; }
    .auth-card { background:white; border-radius:20px; box-shadow:0 30px 60px rgba(0,0,0,.25); }
    .brand-text { font-size:2rem; font-weight:800; background:linear-gradient(135deg,#6c3fe0,#ff6b35); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .form-control:focus { border-color:#6c3fe0; box-shadow:0 0 0 .2rem rgba(108,63,224,.15); }
    .btn-primary { background:linear-gradient(135deg,#6c3fe0,#7c4ef0); border:none; border-radius:10px; padding:12px; font-weight:600; }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-9 col-md-7 col-lg-5">
        <div class="auth-card p-4 p-md-5">
          <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="text-decoration-none brand-text d-block mb-2">ShopBD</a>
            <h4 class="fw-700 mb-1">Create your account 🚀</h4>
            <p class="text-muted small">Join thousands of happy shoppers</p>
          </div>
          <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-600">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Your full name" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-600">Email Address <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-600">Phone Number</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+8801XXXXXXXXX">
            </div>
            <div class="mb-3">
              <label class="form-label fw-600">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required minlength="8">
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
              <label class="form-label fw-600">Confirm Password <span class="text-danger">*</span></label>
              <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">
              <i class="bi bi-person-plus me-2"></i>Create Account
            </button>
          </form>
          <p class="text-center text-muted mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary fw-600 text-decoration-none">Sign in</a></p>
          <div class="text-center mt-3"><a href="{{ route('home') }}" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Back to store</a></div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
