<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — ShopBD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(135deg, #6c3fe0 0%, #ff6b35 100%); min-height: 100vh; display: flex; align-items: center; font-family: 'Segoe UI', sans-serif; }
    .auth-card { background: white; border-radius: 20px; box-shadow: 0 30px 60px rgba(0,0,0,.25); }
    .brand-text { font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, #6c3fe0, #ff6b35); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .form-control:focus { border-color: #6c3fe0; box-shadow: 0 0 0 .2rem rgba(108,63,224,.15); }
    .btn-primary { background: linear-gradient(135deg, #6c3fe0, #7c4ef0); border: none; border-radius: 10px; padding: 12px; font-weight: 600; }
    .btn-primary:hover { background: linear-gradient(135deg, #5432c2, #6c3fe0); }
    .divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; color: #adb5bd; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #dee2e6; }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-8 col-md-6 col-lg-5">
        <div class="auth-card p-4 p-md-5">
          <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="text-decoration-none brand-text d-block mb-2">ShopBD</a>
            <h4 class="fw-700 mb-1">Welcome back! 👋</h4>
            <p class="text-muted small">Sign in to your account</p>
          </div>

          @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
          @endif

          <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-600">Email Address</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                  value="{{ old('email') }}" placeholder="you@example.com" autofocus>
              </div>
              @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <label class="form-label fw-600">Password</label>
                <a href="{{ route('password.request') }}" class="text-primary small text-decoration-none">Forgot password?</a>
              </div>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePass()"><i class="bi bi-eye" id="eyeIcon"></i></button>
              </div>
              @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label" for="remember">Keep me signed in</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
              <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
          </form>

          <p class="text-center text-muted mb-0">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-primary fw-600 text-decoration-none">Create one</a>
          </p>

          <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="text-muted small text-decoration-none">
              <i class="bi bi-arrow-left me-1"></i>Back to store
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePass() {
      const p = document.getElementById('password');
      const i = document.getElementById('eyeIcon');
      if (p.type === 'password') { p.type = 'text'; i.className = 'bi bi-eye-slash'; }
      else { p.type = 'password'; i.className = 'bi bi-eye'; }
    }
  </script>
</body>
</html>
