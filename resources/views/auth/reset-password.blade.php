<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password — ShopBD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <h4 class="fw-700 mb-1">Set New Password</h4>
          </div>
          <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <div class="mb-3">
              <label class="form-label fw-600">New Password</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
              <label class="form-label fw-600">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
