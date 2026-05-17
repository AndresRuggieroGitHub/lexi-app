<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{ __('lexi.legal_terms.meta_description') }}">
  <meta name="robots" content="noindex">
  <title>{{ __('lexi.legal_terms.meta_title') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --violet: #7c3aed;
      --text: #1a1a2e;
      --muted: #6b7280;
      --line: #e5e7eb;
      --surface: #ffffff;
    }

    html, body {
      min-height: 100%;
      font-family: 'Inter', system-ui, sans-serif;
      background:
        radial-gradient(circle at top left, rgba(245, 158, 11, 0.16), transparent 24%),
        radial-gradient(circle at 85% 16%, rgba(216, 180, 254, 0.62), transparent 23%),
        linear-gradient(160deg, #dccdea 0%, #f8f3fe 54%, #e2e5f4 100%);
      color: var(--text);
    }

    .legal-page {
      min-height: 100vh;
      padding: 2rem;
      background: linear-gradient(180deg, rgba(210, 196, 229, 0.95) 0%, rgba(190, 172, 216, 0.72) 52%, rgba(156, 140, 186, 0.42) 100%);
    }
    .legal-card {
      width: min(100%, 980px);
      margin: 0 auto;
      background: rgba(255,255,255,0.94);
      border-radius: 28px;
      box-shadow: 0 28px 70px rgba(51, 24, 96, 0.16);
      overflow: hidden;
    }
    .legal-hero {
      padding: 2.75rem 3rem 2rem;
      background: radial-gradient(circle at top left, rgba(216, 180, 254, 0.45), transparent 34%), linear-gradient(145deg, #ffffff 0%, #f7f2ff 60%, #efe7ff 100%);
      border-bottom: 1px solid rgba(124, 58, 237, 0.08);
    }
    .legal-kicker {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      font-size: 0.84rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      color: var(--violet);
      margin-bottom: 1rem;
    }
    .legal-title {
      font-size: clamp(2rem, 4vw, 3rem);
      line-height: 1.04;
      letter-spacing: -0.05em;
      margin-bottom: 0.85rem;
    }
    .legal-subtitle {
      max-width: 42rem;
      color: var(--muted);
      line-height: 1.7;
      font-size: 1rem;
    }
    .legal-meta {
      margin-top: 1rem;
      color: var(--muted);
      font-size: 0.88rem;
    }
    .legal-content {
      padding: 2rem 3rem 3rem;
      display: grid;
      gap: 1rem;
    }
    .legal-section {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: 18px;
      padding: 1.4rem 1.45rem;
    }
    .legal-section h2 {
      font-size: 1.08rem;
      margin-bottom: 0.7rem;
      letter-spacing: -0.03em;
    }
    .legal-section p,
    .legal-section li {
      color: #4b5563;
      line-height: 1.7;
      font-size: 0.96rem;
    }
    .legal-section ul {
      padding-left: 1.2rem;
      display: grid;
      gap: 0.45rem;
    }
    .legal-actions-bar {
      display: flex;
      flex-wrap: wrap;
      gap: 0.85rem;
      padding: 0 3rem 3rem;
    }
    .legal-link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
      color: var(--violet);
      font-weight: 600;
    }
    .legal-link:hover { text-decoration: underline; }

    @media (max-width: 640px) {
      .legal-page { padding: 0.8rem; }
      .legal-hero { padding: 1.5rem 1.2rem 1.3rem; }
      .legal-content { padding: 1.2rem 1.1rem 1.4rem; }
      .legal-actions-bar { padding: 0 1.1rem 1.4rem; }
      .legal-title { font-size: 1.9rem; }
      .legal-subtitle,
      .legal-section p,
      .legal-section li { line-height: 1.6; }
    }
  </style>
</head>
<body>
  <main class="legal-page">
    <article class="legal-card" aria-label="{{ __('lexi.legal_terms.aria') }}">
      <header class="legal-hero">
        <p class="legal-kicker"><i class="bi bi-shield-check"></i> {{ __('lexi.legal_terms.kicker') }}</p>
        <h1 class="legal-title">{{ __('lexi.legal_terms.title') }}</h1>
        <p class="legal-subtitle">{{ __('lexi.legal_terms.subtitle') }}</p>
        <p class="legal-meta">{{ __('lexi.legal_terms.updated') }}</p>
      </header>

      <div class="legal-content">
        <section class="legal-section">
          <h2>{{ __('lexi.legal_terms.service_title') }}</h2>
          <p>{{ __('lexi.legal_terms.service_text') }}</p>
        </section>

        <section class="legal-section">
          <h2>{{ __('lexi.legal_terms.proper_use_title') }}</h2>
          <p>{{ __('lexi.legal_terms.proper_use_text') }}</p>
        </section>

        <section class="legal-section">
          <h2>{{ __('lexi.legal_terms.account_title') }}</h2>
          <p>{{ __('lexi.legal_terms.account_text') }}</p>
        </section>

        <section class="legal-section">
          <h2>{{ __('lexi.legal_terms.availability_title') }}</h2>
          <p>{{ __('lexi.legal_terms.availability_text') }}</p>
        </section>

        <section class="legal-section">
          <h2>{{ __('lexi.legal_terms.intellectual_property_title') }}</h2>
          <p>{{ __('lexi.legal_terms.intellectual_property_text') }}</p>
        </section>

        <section class="legal-section">
          <h2>{{ __('lexi.legal_terms.liability_title') }}</h2>
          <p>{{ __('lexi.legal_terms.liability_text') }}</p>
        </section>

        <section class="legal-section">
          <h2>{{ __('lexi.legal_terms.contact_title') }}</h2>
          <p>{{ __('lexi.legal_terms.contact_text') }}</p>
        </section>
      </div>
      <div class="legal-actions-bar">
        <a class="legal-link" href="{{ route('privacidad') }}"><i class="bi bi-arrow-right"></i> {{ __('lexi.nav.privacy') }}</a>
        <a class="legal-link" href="{{ route('login') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.auth.sign_in_link') }}</a>
      </div>
    </article>
  </main>
</body>
</html>