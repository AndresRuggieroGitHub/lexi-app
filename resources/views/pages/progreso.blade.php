@extends('layouts.site', ['title' => __('lexi.meta.progress.title'), 'description' => __('lexi.meta.progress.description'), 'robots' => 'noindex', 'extraHead' => '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>'])

@section('content')
<main id="mainContent" class="profile-main">

  <div class="progress-page-hero">
    <div>
      <h1 class="progress-page-title">{{ __('lexi.progress.title') }}</h1>
    </div>
  </div>

  <div class="progress-streak-card">
    <div class="progress-streak-left">
      <span class="progress-streak-fire" id="streakFire">🔥🌱</span>
      <div>
        <div class="progress-streak-num" id="streakNum">0</div>
        <div class="progress-streak-label">{{ __('lexi.progress.streak_days') }}</div>
      </div>
    </div>
    <div class="progress-streak-right">
      <p class="progress-streak-msg" id="streakMsg">{{ __('lexi.progress.streak_message') }}</p>
    </div>
  </div>

  <div class="profile-stats-row">
    <div class="profile-stat-card">
      <span class="profile-stat-icon" style="--sc:#4f8ef7"><i class="bi bi-book-half"></i></span>
      <span class="profile-stat-num" id="statWords">0</span>
      <span class="profile-stat-label">{{ __('lexi.progress.saved_words') }}</span>
    </div>
    <div class="profile-stat-card">
      <span class="profile-stat-icon" style="--sc:#f9b233"><i class="bi bi-trophy-fill"></i></span>
      <span class="profile-stat-num" id="statExDone">0</span>
      <span class="profile-stat-label">{{ __('lexi.progress.completed_exercises') }}</span>
    </div>
    <div class="profile-stat-card">
      <span class="profile-stat-icon" style="--sc:#2dc98b"><i class="bi bi-translate"></i></span>
      <span class="profile-stat-num" id="statLang">?</span>
      <span class="profile-stat-label">{{ __('lexi.progress.active_language') }}</span>
    </div>
  </div>

  <section class="profile-section">
    <h2 class="profile-section-title">{{ __('lexi.progress.estimated_level') }}</h2>
    <div class="profile-level-card">
      <div class="profile-level-badge" id="levelBadge">A1</div>
      <div class="profile-level-info">
        <div class="profile-level-bar-wrap">
          <div class="profile-level-bar" id="levelBar" style="width:0%"></div>
        </div>
        <p class="profile-level-next" id="levelNext">{{ __('lexi.progress.level_next_example') }}</p>
      </div>
    </div>
    <p class="progress-cefr-note">{{ __('lexi.progress.level_note') }}</p>
  </section>

  <section class="profile-section">
    <h2 class="profile-section-title">{{ __('lexi.progress.vocabulary_sessions') }}</h2>
    <div class="progress-charts-grid">
      <div class="progress-chart-card">
        <p class="progress-chart-title"><i class="bi bi-pie-chart-fill"></i> {{ __('lexi.progress.words_by_language') }}</p>
        <div class="progress-chart-wrap">
          <canvas id="langDonutChart"></canvas>
        </div>
        <p class="progress-chart-empty" id="donutEmpty" hidden>{{ __('lexi.progress.no_saved_words') }}</p>
      </div>
      <div class="progress-chart-card">
        <p class="progress-chart-title"><i class="bi bi-bar-chart-fill"></i> {{ __('lexi.progress.exercise_sessions') }}</p>
        <div class="progress-chart-wrap">
          <canvas id="modeBarChart"></canvas>
        </div>
        <p class="progress-chart-empty" id="barEmpty" hidden>{{ __('lexi.progress.no_sessions') }}</p>
      </div>
    </div>
  </section>

  <section class="profile-section">
    <div class="profile-section-header">
      <h2 class="profile-section-title">{{ __('lexi.progress.exercise_sessions') }}</h2>
      <a href="{{ route('ejercicios') }}" class="profile-section-link">{{ __('lexi.progress.practice_link') }}</a>
    </div>
    <div class="profile-modes-grid">
      <div class="profile-mode-item">
        <span class="profile-mode-icon" style="--mc:#4f8ef7"><i class="bi bi-book-half"></i></span>
        <span class="profile-mode-count" id="modeReading">0</span>
        <span class="profile-mode-label">{{ __('lexi.progress.mode_reading') }}</span>
      </div>
      <div class="profile-mode-item">
        <span class="profile-mode-icon" style="--mc:#f76b4f"><i class="bi bi-headphones"></i></span>
        <span class="profile-mode-count" id="modeListening">0</span>
        <span class="profile-mode-label">{{ __('lexi.progress.mode_listening') }}</span>
      </div>
      <div class="profile-mode-item">
        <span class="profile-mode-icon" style="--mc:#2dc98b"><i class="bi bi-mic-fill"></i></span>
        <span class="profile-mode-count" id="modeSpeaking">0</span>
        <span class="profile-mode-label">{{ __('lexi.progress.mode_speaking') }}</span>
      </div>
      <div class="profile-mode-item">
        <span class="profile-mode-icon" style="--mc:#a855f7"><i class="bi bi-pencil-fill"></i></span>
        <span class="profile-mode-count" id="modeWriting">0</span>
        <span class="profile-mode-label">{{ __('lexi.progress.mode_writing') }}</span>
      </div>
      <div class="profile-mode-item">
        <span class="profile-mode-icon" style="--mc:#f9b233"><i class="bi bi-shuffle"></i></span>
        <span class="profile-mode-count" id="modeMix">0</span>
        <span class="profile-mode-label">{{ __('lexi.progress.mode_mix') }}</span>
      </div>
    </div>
  </section>

  <section class="profile-section">
    <div class="profile-section-header">
      <h2 class="profile-section-title">{{ __('lexi.progress.latest_saved_words') }}</h2>
      <a href="{{ route('biblioteca') }}" class="profile-section-link">{{ __('lexi.progress.view_all_link') }}</a>
    </div>
    <ul class="profile-words-list" id="recentWordsList">
      <li class="profile-words-empty">{{ __('lexi.progress.no_saved_words_long') }} <a href="{{ route('biblioteca') }}">{{ __('lexi.progress.explore_library') }}</a></li>
    </ul>
  </section>

</main>
@endsection