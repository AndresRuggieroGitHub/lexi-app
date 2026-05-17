<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{ __('lexi.meta.login.description') }}">
  <meta name="robots" content="noindex">
  <title>{{ __('lexi.meta.login.title') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root { --brand:#f59e0b; --violet:#7c3aed; --violet-mid:#9d4edd; --violet-light:#d8b4fe; --text:#1a1a2e; --muted:#6b7280; --line:#e5e7eb; --surface:#f9fafb; --radius:14px; }
    html, body { min-height: 100%; font-family: 'Inter', system-ui, sans-serif; background: radial-gradient(circle at top left, rgba(245, 158, 11, 0.16), transparent 24%), radial-gradient(circle at 85% 16%, rgba(216, 180, 254, 0.62), transparent 23%), linear-gradient(160deg, #dccdea 0%, #f8f3fe 54%, #e2e5f4 100%); color: var(--text); }
    .login-page { min-height: 100vh; display: grid; place-items: center; padding: 2rem; background: linear-gradient(180deg, rgba(210, 196, 229, 0.95) 0%, rgba(190, 172, 216, 0.72) 52%, rgba(156, 140, 186, 0.42) 100%); }
    .login-card { width: min(100%, 980px); display: grid; grid-template-columns: 1.05fr 0.95fr; background: #fff; border-radius: 28px; overflow: hidden; box-shadow: 0 28px 70px rgba(51, 24, 96, 0.22); }
    .login-brand-panel { padding: 3rem; background: radial-gradient(circle at top left, rgba(216, 180, 254, 0.8), transparent 38%), linear-gradient(145deg, #581c87 0%, #7c3aed 55%, #c084fc 100%); display: flex; flex-direction: column; justify-content: center; gap: 1rem; position: relative; overflow: hidden; }
    .login-brand-content { display: flex; flex-direction: column; gap: 1rem; z-index: 1; }
    .login-brand-headline { display: inline-flex; align-items: center; gap: 0.58rem; font-size: clamp(1.95rem, 2.8vw, 2.55rem); font-weight: 800; color: #fff; line-height: 1.08; letter-spacing: -0.05em; margin: 0; }
    .login-brand-headline i { font-size: 0.94em; color: #fff; line-height: 1; }
    .login-brand-desc { font-size: 1rem; color: rgba(255,255,255,0.82); line-height: 1.6; max-width: 20rem; margin: 0; }
    .login-form-panel { display: flex; flex-direction: column; justify-content: center; padding: 3rem; background: #fff; }
    .login-form-wrap { width: 100%; max-width: 400px; margin: 0 auto; }
    .login-form-title { font-size: 1.8rem; font-weight: 800; color: var(--text); letter-spacing: -0.04em; margin-bottom: 0.4rem; }
    .login-form-sub { font-size: 0.9rem; color: var(--muted); margin-bottom: 2rem; }
    .login-form-sub a, .login-register-note a, .login-footer-note a, .login-forgot { color: var(--violet); text-decoration: none; }
    .login-field { margin-bottom: 1.1rem; }
    .login-field label { display:block; font-size:0.85rem; font-weight:600; color:var(--text); margin-bottom:0.45rem; }
    .login-input-wrap { position: relative; }
    .login-input-wrap > i { position:absolute; left:0.9rem; top:50%; transform:translateY(-50%); font-size:1.05rem; color:#9ca3af; pointer-events:none; }
    .login-input { width:100%; padding:0.75rem 1rem 0.75rem 2.5rem; border:1.5px solid var(--line); border-radius:10px; font-family:inherit; font-size:0.95rem; color:var(--text); background:var(--surface); transition:border-color .15s, box-shadow .15s; outline:none; }
    .login-input:focus { border-color: var(--violet); box-shadow: 0 0 0 3px rgba(124,58,237,0.1); background:#fff; }
    .login-forgot { display:block; text-align:right; font-size:0.8rem; margin-top:0.35rem; }
    .login-btn-primary { width:100%; padding:0.85rem 1.5rem; border:none; border-radius:10px; background:linear-gradient(135deg, var(--violet) 0%, var(--violet-mid) 100%); color:#fff; font-family:inherit; font-size:1rem; font-weight:700; cursor:pointer; margin-top:1.5rem; letter-spacing:-0.01em; }
    .login-register-note { margin-top:1.1rem; font-size:0.9rem; color:var(--muted); text-align:center; line-height:1.55; }
    .login-footer-note { font-size:0.78rem; color:var(--muted); text-align:center; margin-top:1.75rem; line-height:1.6; }
    .login-alert { padding:0.7rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:500; margin-bottom:1.1rem; }
    .login-alert--error { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
    .login-alert--success { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
    .login-input.is-invalid { border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,0.1); background:#fff; }
    .login-field-error { display:block; margin-top:0.4rem; font-size:0.8rem; color:#b91c1c; }
    @media (max-width: 860px) { .login-card { grid-template-columns:1fr; } .login-brand-panel,.login-form-panel { padding:2rem; } .login-brand-content { width:100%; max-width:400px; margin:0 auto; } }
    @media (max-width: 640px) { .login-page { padding:0.8rem; place-items:stretch; } .login-card { width:100%; min-height:calc(100vh - 1.6rem); border-radius:20px; box-shadow:0 18px 40px rgba(76, 29, 149, 0.12); } .login-brand-panel { padding:1.1rem 1.1rem 1.35rem; } .login-form-panel { padding:1.35rem 1.1rem 2rem; } .login-brand-content { max-width:none; } .login-brand-headline { font-size:1.45rem; line-height:1.15; } .login-brand-desc { max-width:none; font-size:0.95rem; line-height:1.55; } .login-form-title { font-size:1.6rem; } }
  </style>
</head>
<body>
<main class="login-page">
  <section class="login-card" aria-label="{{ __('lexi.auth.login_aria') }}">
    <aside class="login-brand-panel"><div class="login-brand-content"><h1 class="login-brand-headline">{{ __('lexi.auth.login_brand_title') }} <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i></h1><p class="login-brand-desc">{{ __('lexi.auth.login_brand_text') }}</p></div></aside>
    <div class="login-form-panel"><div class="login-form-wrap">
      <h2 class="login-form-title">{{ __('lexi.auth.login_title') }}</h2>
      <p class="login-form-sub">{{ __('lexi.auth.login_subtitle') }}</p>
      @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
        <div class="login-alert login-alert--error" role="alert">{{ $errors->first() }}</div>
      @endif
      @if (session('status'))
        <div class="login-alert login-alert--success" role="alert">{{ session('status') }}</div>
      @endif
      <form method="POST" action="{{ route('login.attempt') }}" novalidate>
        @csrf
        <div class="login-field"><label for="loginEmail">{{ __('lexi.auth.email') }}</label><div class="login-input-wrap"><i class="bi bi-envelope"></i><input type="email" id="loginEmail" name="email" class="login-input{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('lexi.auth.email_placeholder') }}" autocomplete="email" value="{{ old('email') }}" required></div>@error('email')<span class="login-field-error">{{ $message }}</span>@enderror</div>
        <div class="login-field"><label for="loginPassword">{{ __('lexi.auth.password') }}</label><div class="login-input-wrap"><i class="bi bi-lock"></i><input type="password" id="loginPassword" name="password" class="login-input{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('lexi.auth.password_placeholder') }}" autocomplete="current-password" required></div>@error('password')<span class="login-field-error">{{ $message }}</span>@enderror<a href="{{ route('password.request') }}" class="login-forgot">{{ __('lexi.auth.forgot_password') }}</a></div>
        <button type="submit" class="login-btn-primary">{{ __('lexi.auth.sign_in') }}</button>
      </form>
      <p class="login-register-note">{{ __('lexi.auth.no_account') }} <a href="{{ route('register') }}">{{ __('lexi.auth.create_account') }}</a></p>
      <p class="login-footer-note">{!! str_replace([':terms', ':privacy'], ['<a href="' . route('terminos') . '">' . __('lexi.nav.terms') . '</a>', '<a href="' . route('privacidad') . '">' . __('lexi.nav.privacy') . '</a>'], __('lexi.auth.legal_acceptance')) !!}</p>
    </div></div>
  </section>
</main>
</body>
</html>