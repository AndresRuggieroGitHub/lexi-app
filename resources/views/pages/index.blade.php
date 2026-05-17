<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{ __('lexi.meta.welcome.description') }}">
  <meta name="robots" content="index,follow">
  <title>{{ __('lexi.meta.welcome.title') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    html, body { min-height: 100%; }
    body {
      margin: 0;
      font-family: 'Inter', system-ui, sans-serif;
      color: #111827;
      background:
        radial-gradient(circle at top left, rgba(245, 158, 11, 0.16), transparent 24%),
        radial-gradient(circle at 85% 16%, rgba(216, 180, 254, 0.62), transparent 23%),
        linear-gradient(160deg, #dccdea 0%, #f8f3fe 54%, #e2e5f4 100%);
    }
    .welcome-shell {
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 2rem;
      background: linear-gradient(180deg, rgba(210, 196, 229, 0.95) 0%, rgba(190, 172, 216, 0.72) 52%, rgba(156, 140, 186, 0.42) 100%);
    }
    .welcome-card {
      width: min(100%, 1120px);
      display: grid;
      grid-template-columns: 1.1fr 0.9fr;
      gap: 0;
      border-radius: 32px;
      overflow: hidden;
      background: rgba(255, 255, 255, 0.84);
      backdrop-filter: blur(18px);
      box-shadow: 0 30px 80px rgba(76, 29, 149, 0.14);
    }
    .welcome-hero {
      padding: 3rem;
      background:
        radial-gradient(circle at 22% 20%, rgba(255,255,255,0.2), transparent 26%),
        linear-gradient(145deg, #4c1d95 0%, #7c3aed 46%, #c084fc 100%);
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 2rem;
      position: relative;
    }
    .welcome-title {
      margin: 1.25rem 0 0;
      font-size: clamp(2rem, 3.4vw, 3.1rem);
      line-height: 1.05;
      letter-spacing: -0.05em;
      max-width: 16ch;
    }
    .welcome-title span {
      color: #f59e0b;
    }
    .welcome-copy {
      margin: 1rem 0 0;
      max-width: 34rem;
      color: rgba(255,255,255,0.84);
      line-height: 1.7;
      font-size: 1rem;
    }
    .welcome-points {
      display: grid;
      gap: 0.8rem;
      list-style: none;
      margin: 1.15rem 0 0;
      padding: 0;
    }
    .welcome-points li {
      display: flex;
      gap: 0.75rem;
      align-items: flex-start;
      color: rgba(255,255,255,0.92);
      line-height: 1.55;
    }
    .welcome-points i {
      color: #fde68a;
      margin-top: 0.15rem;
    }
    .welcome-metrics {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }
    .welcome-metric {
      min-width: 140px;
      padding: 0.95rem 1rem;
      border-radius: 18px;
      background: rgba(255,255,255,0.1);
    }
    .welcome-metric strong {
      display: block;
      font-size: 1.5rem;
      letter-spacing: -0.05em;
      margin-bottom: 0.2rem;
    }
    .welcome-metric span {
      color: rgba(255,255,255,0.76);
      font-size: 0.86rem;
    }
    .welcome-panel {
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 1.25rem;
    }
    .welcome-panel h1 {
      margin: 0;
      font-size: 2.35rem;
      letter-spacing: -0.05em;
    }
    .welcome-panel-title {
      display: inline-flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 0.55rem;
    }
    .welcome-panel-title-main {
      display: inline-block;
    }
    .welcome-panel-title-lockup {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      white-space: nowrap;
    }
    .welcome-panel-title i {
      font-size: 1.78rem;
      color: #f59e0b;
      line-height: 1;
      flex: 0 0 auto;
    }
    .welcome-panel p {
      margin: 0;
      color: #6b7280;
      line-height: 1.7;
    }
    .welcome-actions {
      display: grid;
      gap: 0.9rem;
      margin-top: 0.4rem;
    }
    .welcome-action {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 1rem 1.1rem;
      border-radius: 18px;
      text-decoration: none;
      border: 1px solid #e5e7eb;
      color: #111827;
      background: #fff;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
      transition: transform 0.15s, border-color 0.15s, box-shadow 0.15s;
    }
    .welcome-action:hover {
      transform: translateY(-1px);
      border-color: rgba(124, 58, 237, 0.3);
      box-shadow: 0 16px 32px rgba(76, 29, 149, 0.08);
    }
    .welcome-action--primary {
      background: linear-gradient(135deg, #7c3aed 0%, #9d4edd 100%);
      color: #fff;
      border-color: transparent;
    }
    .welcome-action__meta strong {
      display: block;
      margin-bottom: 0.2rem;
      font-size: 1rem;
    }
    .welcome-action__meta span {
      display: block;
      color: inherit;
      opacity: 0.8;
      font-size: 0.9rem;
      line-height: 1.55;
    }
    .welcome-note {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      margin-top: 0.35rem;
      padding: 1rem 1.05rem;
      border-radius: 18px;
      background: #f8fafc;
      color: #475569;
      font-size: 0.92rem;
      line-height: 1.6;
    }
    .welcome-note i { color: #7c3aed; margin-top: 0.1rem; }
    @media (max-width: 900px) {
      .welcome-card { grid-template-columns: 1fr; }
      .welcome-hero, .welcome-panel { padding: 2rem; }
      .welcome-title { max-width: none; }
      .welcome-panel h1 { font-size: 2.05rem; }
    }
    @media (max-width: 640px) {
      .welcome-shell {
        padding: 1rem;
        place-items: stretch;
      }
      .welcome-card {
        width: 100%;
        border-radius: 24px;
      }
      .welcome-hero, .welcome-panel {
        padding: 1.4rem;
      }
      .welcome-title {
        font-size: 2rem;
        line-height: 1.04;
      }
      .welcome-copy,
      .welcome-panel p,
      .welcome-action__meta span,
      .welcome-note {
        line-height: 1.55;
      }
      .welcome-metrics {
        display: grid;
        grid-template-columns: 1fr;
      }
      .welcome-metric {
        min-width: 0;
      }
      .welcome-action {
        align-items: flex-start;
      }
      .welcome-action i {
        margin-top: 0.15rem;
      }
    }
  </style>
</head>
<body>
  <main class="welcome-shell">
    <section class="welcome-card" aria-label="{{ __('lexi.welcome.aria') }}">
      <div class="welcome-hero">
        <div>
          <h2 class="welcome-title">{{ __('lexi.welcome.hero_title_prefix') }} <span>{{ __('lexi.welcome.hero_title_highlight') }}</span></h2>
          <p class="welcome-copy">{{ __('lexi.welcome.hero_copy') }}</p>
          <ul class="welcome-points">
            <li><i class="bi bi-check2-circle"></i><span>{{ __('lexi.welcome.point_languages') }}</span></li>
            <li><i class="bi bi-check2-circle"></i><span>{{ __('lexi.welcome.point_vocabulary') }}</span></li>
            <li><i class="bi bi-check2-circle"></i><span>{{ __('lexi.welcome.point_exercises') }}</span></li>
            <li><i class="bi bi-check2-circle"></i><span>{{ __('lexi.welcome.point_progress') }}</span></li>
          </ul>
        </div>
        <div class="welcome-metrics">
          <div class="welcome-metric"><strong>30</strong><span>{{ __('lexi.welcome.metric_languages') }}</span></div>
          <div class="welcome-metric"><strong>2</strong><span>{{ __('lexi.welcome.metric_functional_languages') }}</span></div>
          <div class="welcome-metric"><strong>5</strong><span>{{ __('lexi.welcome.metric_exercise_modes') }}</span></div>
        </div>
      </div>
      <div class="welcome-panel">
        <h1 class="welcome-panel-title"><span class="welcome-panel-title-main">{{ __('lexi.welcome.panel_title_prefix') }}</span><span class="welcome-panel-title-lockup">Lexi!<i class="bi bi-stars" aria-hidden="true"></i></span></h1>
        <p>{{ __('lexi.welcome.panel_copy') }}</p>

        <div class="welcome-actions">
          <a class="welcome-action welcome-action--primary" href="{{ route('login') }}">
            <div class="welcome-action__meta">
              <strong>{{ __('lexi.welcome.login_title') }}</strong>
              <span>{{ __('lexi.welcome.login_copy') }}</span>
            </div>
            <i class="bi bi-arrow-right-circle-fill"></i>
          </a>
          <a class="welcome-action" href="{{ route('register') }}">
            <div class="welcome-action__meta">
              <strong>{{ __('lexi.welcome.register_title') }}</strong>
              <span>{{ __('lexi.welcome.register_copy') }}</span>
            </div>
            <i class="bi bi-person-plus"></i>
          </a>
        </div>

        <div class="welcome-note">
          <i class="bi bi-info-circle-fill"></i>
          <div>{{ __('lexi.welcome.note') }}</div>
        </div>
      </div>
    </section>
  </main>
</body>
</html>