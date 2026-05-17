<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{ __('lexi.meta.register.description') }}">
  <meta name="robots" content="noindex">
  <title>{{ __('lexi.meta.register.title') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    :root { --brand:#f59e0b; --violet:#7c3aed; --violet-mid:#9d4edd; --text:#1a1a2e; --muted:#6b7280; --line:#e5e7eb; --surface:#f9fafb; }
    html, body { min-height:100%; font-family:'Inter', system-ui, sans-serif; background: radial-gradient(circle at top left, rgba(245, 158, 11, 0.16), transparent 24%), radial-gradient(circle at 85% 16%, rgba(216, 180, 254, 0.62), transparent 23%), linear-gradient(160deg, #dccdea 0%, #f8f3fe 54%, #e2e5f4 100%); color:var(--text); }
    .auth-shell{min-height:100vh;display:grid;place-items:center;padding:2rem;background:linear-gradient(180deg, rgba(210, 196, 229, 0.95) 0%, rgba(190, 172, 216, 0.72) 52%, rgba(156, 140, 186, 0.42) 100%);} .auth-card{width:min(100%,980px);display:grid;grid-template-columns:1.05fr 0.95fr;background:#fff;border:none;border-radius:28px;overflow:hidden;box-shadow:0 28px 70px rgba(51,24,96,.22);} .auth-aside{padding:3rem;background:radial-gradient(circle at top left, rgba(216,180,254,.8), transparent 38%), linear-gradient(145deg,#581c87 0%,#7c3aed 55%,#c084fc 100%);color:#fff;display:flex;flex-direction:column;justify-content:center;gap:1rem;} .auth-aside-content{display:flex;flex-direction:column;gap:1rem;} .auth-title{display:inline-flex;align-items:center;gap:.58rem;font-size:clamp(1.95rem,2.8vw,2.55rem);font-weight:800;color:#fff;line-height:1.08;letter-spacing:-.05em;} .auth-desc{max-width:20rem;color:rgba(255,255,255,.82);line-height:1.6;} .auth-form{padding:3rem;display:flex;flex-direction:column;justify-content:center;} .auth-form h1{font-size:1.8rem;letter-spacing:-.04em;margin-bottom:.45rem;} .auth-sub{color:var(--muted);margin-bottom:1.75rem;line-height:1.6;} .auth-sub a{color:var(--violet);font-weight:600;text-decoration:none;} .auth-grid{display:grid;gap:1rem;} .auth-field label{display:block;font-size:.86rem;font-weight:600;margin-bottom:.45rem;} .auth-input-wrap{position:relative;} .auth-input-wrap i{position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:#9ca3af;} .auth-input-wrap--select .auth-flag-icon{position:absolute;left:.9rem;top:50%;width:1.15rem;height:1.15rem;transform:translateY(-50%);border-radius:999px;object-fit:cover;opacity:0;pointer-events:none;} .auth-input-wrap--select.has-flag .auth-flag-icon{opacity:1;} .auth-input-wrap--select.has-flag>i{opacity:0;} .auth-input{appearance:none;width:100%;border:1.5px solid var(--line);border-radius:12px;background:var(--surface);padding:.85rem 1rem .85rem 2.6rem;font:inherit;outline:none;transition:border-color .15s, box-shadow .15s;} .auth-input-wrap--select .auth-input{padding-left:2.95rem;} .auth-input:focus{border-color:var(--violet);box-shadow:0 0 0 4px rgba(124,58,237,.1);background:#fff;} .auth-check{display:flex;align-items:flex-start;gap:.7rem;font-size:.9rem;color:var(--muted);line-height:1.5;} .auth-check input{margin-top:.15rem;} .auth-btn{border:none;border-radius:12px;padding:.95rem 1.2rem;font:inherit;font-weight:700;color:#fff;background:linear-gradient(135deg,var(--violet) 0%, var(--violet-mid) 100%);cursor:pointer;} .auth-alert{padding:.85rem 1rem;border-radius:12px;font-size:.9rem;font-weight:600;background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;} @media (max-width:860px){.auth-card{grid-template-columns:1fr}.auth-aside,.auth-form{padding:2rem}} @media (max-width:640px){.auth-shell{padding:.8rem;place-items:stretch}.auth-card{width:100%;min-height:calc(100vh - 1.6rem);border-radius:20px;box-shadow:0 18px 40px rgba(76,29,149,.12)}.auth-form{padding:1.35rem 1.1rem 2rem}.auth-aside{padding:1.1rem 1.1rem 1.35rem;min-height:132px}.auth-title{font-size:1.45rem}.auth-desc{max-width:none;font-size:.95rem;line-height:1.55}}
  </style>
</head>
<body>
<main class="auth-shell">
  <section class="auth-card" aria-label="{{ __('lexi.auth.register_aria') }}">
    <aside class="auth-aside"><div class="auth-aside-content"><h2 class="auth-title">{{ __('lexi.auth.register_brand_title') }} <i class="bi bi-person-plus" aria-hidden="true"></i></h2><p class="auth-desc">{{ __('lexi.auth.register_brand_text') }}</p></div></aside>
    <div class="auth-form">
      <h1>{{ __('lexi.auth.register_title') }}</h1>
      <p class="auth-sub">{{ __('lexi.auth.already_account') }} <a href="{{ route('login') }}">{{ __('lexi.auth.sign_in_link') }}</a></p>
      @if ($errors->any())
        <div class="auth-alert" role="alert">{{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('register.store') }}" class="auth-grid" novalidate>
        @csrf
        <div class="auth-field"><label for="registerName">{{ __('lexi.auth.name') }}</label><div class="auth-input-wrap"><i class="bi bi-person"></i><input class="auth-input" id="registerName" name="name" type="text" placeholder="{{ __('lexi.auth.name_placeholder') }}" maxlength="30" value="{{ old('name') }}" required></div></div>
        <div class="auth-field"><label for="registerEmail">{{ __('lexi.auth.email') }}</label><div class="auth-input-wrap"><i class="bi bi-envelope"></i><input class="auth-input" id="registerEmail" name="email" type="email" placeholder="{{ __('lexi.auth.email_placeholder') }}" value="{{ old('email') }}" required></div></div>
        <div class="auth-field"><label for="registerPassword">{{ __('lexi.auth.password') }}</label><div class="auth-input-wrap"><i class="bi bi-lock"></i><input class="auth-input" id="registerPassword" name="password" type="password" placeholder="{{ __('lexi.auth.password_minimum') }}" required></div></div>
        <div class="auth-field"><label for="registerPasswordConfirm">{{ __('lexi.auth.repeat_password') }}</label><div class="auth-input-wrap"><i class="bi bi-shield-lock"></i><input class="auth-input" id="registerPasswordConfirm" name="password_confirmation" type="password" placeholder="{{ __('lexi.auth.repeat_password_placeholder') }}" required></div></div>
        <div class="auth-field"><label for="registerBirthDate">{{ __('lexi.auth.birth_date') }}</label><div class="auth-input-wrap"><i class="bi bi-calendar-event"></i><input class="auth-input" id="registerBirthDate" name="birth_date" type="date" value="{{ old('birth_date') }}" required></div></div>
        <div class="auth-field"><label for="registerNativeLanguage">{{ __('lexi.auth.mother_tongue') }}</label><div class="auth-input-wrap auth-input-wrap--select" data-flag-select-wrap><i class="bi bi-translate"></i><img class="auth-flag-icon" data-flag-icon alt="" aria-hidden="true"><select class="auth-input" id="registerNativeLanguage" name="mother_tongue_code" required><option value="">{{ __('lexi.auth.select_your_language') }}</option>@foreach ($languages as $language)<option value="{{ $language->code }}" @selected(old('mother_tongue_code') === $language->code)>{{ $language->name }}</option>@endforeach</select></div></div>
        <div class="auth-field"><label for="registerTargetLanguage">{{ __('lexi.auth.target_language') }}</label><div class="auth-input-wrap auth-input-wrap--select" data-flag-select-wrap><i class="bi bi-globe-europe-africa"></i><img class="auth-flag-icon" data-flag-icon alt="" aria-hidden="true"><select class="auth-input" id="registerTargetLanguage" name="target_language_code" required><option value="">{{ __('lexi.auth.select_language') }}</option>@foreach ($languages as $language)<option value="{{ $language->code }}" @selected(old('target_language_code') === $language->code)>{{ $language->name }}</option>@endforeach</select></div></div>
        <label class="auth-check"><input type="checkbox" id="registerTerms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required><span>{!! str_replace([':terms', ':privacy'], ['<a href="' . route('terminos') . '">' . __('lexi.auth.terms_and_conditions') . '</a>', '<a href="' . route('privacidad') . '">' . __('lexi.auth.privacy_policy') . '</a>'], __('lexi.auth.terms_acceptance')) !!}</span></label>
        <button class="auth-btn" type="submit">{{ __('lexi.auth.create_account_button') }}</button>
      </form>
    </div>
  </section>
</main>
<script>
(() => {
  const flagByLanguage = { ar:'icons/flags/saudi_arabia_flag.svg', bg:'icons/flags/bulgaria_flag.svg', cs:'icons/flags/czech_republic_flag.svg', de:'icons/flags/germany_flag.svg', dk:'icons/flags/denmark_flag.svg', en:'icons/flags/united_kingdom_flag.svg', es:'icons/flags/spain_flag.svg', fi:'icons/flags/finland_flag.svg', fr:'icons/flags/france_flag.svg', gr:'icons/flags/greece_flag.svg', he:'icons/flags/israel_flag.svg', hi:'icons/flags/india_flag.svg', hu:'icons/flags/hungary_flag.svg', id:'icons/flags/indonesia_flag.svg', it:'icons/flags/italy_flag.svg', ja:'icons/flags/japan_flag.svg', ko:'icons/flags/south_korea_flag.svg', nl:'icons/flags/netherlands_flag.svg', no:'icons/flags/norway_flag.svg', pl:'icons/flags/poland_flag.svg', pt:'icons/flags/brazil_flag.svg', ro:'icons/flags/romania_flag.svg', ru:'icons/flags/russia_flag.svg', sk:'icons/flags/slovakia_flag.svg', sv:'icons/flags/sweden_flag.svg', th:'icons/flags/thailand_flag.svg', tr:'icons/flags/turkey_flag.svg', ua:'icons/flags/ukraine_flag.svg', vi:'icons/flags/vietnam_flag.svg', zh:'icons/flags/china_flag.svg' };
  document.querySelectorAll('[data-flag-select-wrap] select').forEach((select) => {
    const syncFlag = () => {
      const wrap = select.closest('[data-flag-select-wrap]');
      const flagIcon = wrap.querySelector('[data-flag-icon]');
      const flagSrc = flagByLanguage[select.value];
      if (!flagSrc) { wrap.classList.remove('has-flag'); flagIcon.removeAttribute('src'); return; }
      flagIcon.src = flagSrc; wrap.classList.add('has-flag');
    };
    syncFlag();
    select.addEventListener('change', syncFlag);
  });
})();
</script>
</body>
</html>