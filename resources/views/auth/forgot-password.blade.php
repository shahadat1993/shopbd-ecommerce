<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password — ShopBD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background:linear-gradient(135deg,#6c3fe0,#ff6b35); min-height:100vh; display:flex; align-items:center; font-family:'Segoe UI',sans-serif; }
    .auth-card { background:white; border-radius:20px; box-shadow:0 30px 60px rgba(0,0,0,.25); }
    .brand-text { font-size:2rem; font-weight:800; background:linear-gradient(135deg,#6c3fe0,#ff6b35); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
    .btn-primary { background:linear-gradient(135deg,#6c3fe0,#7c4ef0); border:none; border-radius:10px; padding:12px; font-weight:600; }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-8 col-md-6 col-lg-5">
        <div class="auth-card p-4 p-md-5">
          <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="text-decoration-none brand-text d-block mb-2">ShopBD</a>
            <h4 class="fw-700 mb-1">Reset Password 🔐</h4>
            <p class="text-muted small">We'll send a reset link to your email</p>
          </div>
          @if(session('status'))
            <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('status') }}</div>
          @endif
          <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-4">
              <label class="form-label fw-600">Email Address</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">
              <i class="bi bi-send me-2"></i>Send Reset Link
            </button>
          </form>
          <p class="text-center text-muted mb-0"><a href="{{ route('login') }}" class="text-primary fw-600 text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Back to login</a></p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
