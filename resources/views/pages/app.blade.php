@extends('layouts.site', ['title' => __('lexi.meta.app.title'), 'description' => __('lexi.meta.app.description'), 'robots' => 'noindex,follow', 'activeNav' => 'home', 'afterFooter' => '<button id="btnSubir" type="button" aria-label="' . e(__('lexi.common.back_to_top')) . '">&uarr;</button>'])

@section('content')
<main id="mainContent" class="hero">
  <div class="hero-text scroll-animado">
    <h1>{{ __('lexi.app.hero_title') }}</h1>
    <p class="hero-sub">{{ __('lexi.app.hero_subtitle') }}</p>
  </div>
</main>

<div class="hero-divider" aria-hidden="true"></div>

<section class="container section-space home-cards">
  <h2>{{ __('lexi.app.what_can_you_do') }}</h2>
  <div class="home-card-grid">
    <article class="home-card">
      <div class="home-card-icon"><i class="bi bi-journals"></i></div>
      <h3 class="h5">{{ __('lexi.app.vocabulary_title') }}</h3>
      <p>{{ __('lexi.app.vocabulary_text') }}</p>
      <a class="btn btn-primary" href="{{ route('biblioteca') }}">{{ __('lexi.app.go_to_library') }}</a>
    </article>
    <article class="home-card">
      <div class="home-card-icon"><i class="bi bi-lightning-charge"></i></div>
      <h3 class="h5">{{ __('lexi.app.exercises_title') }}</h3>
      <p>{{ __('lexi.app.exercises_text') }}</p>
      <a class="btn btn-primary" href="{{ route('ejercicios') }}">{{ __('lexi.app.choose_exercise') }}</a>
    </article>
    <article class="home-card">
      <div class="home-card-icon"><i class="bi bi-graph-up-arrow"></i></div>
      <h3 class="h5">{{ __('lexi.app.progress_title') }}</h3>
      <p>{{ __('lexi.app.progress_text') }}</p>
      <a class="btn btn-primary" href="{{ route('progreso') }}">{{ __('lexi.app.view_progress') }}</a>
    </article>
  </div>
</section>

<section class="container section-space how-it-works">
  <h2>{{ __('lexi.app.how_it_works') }}</h2>
  <div class="how-steps">
    <div class="how-step">
      <div class="how-step-num">1</div>
      <h3>{{ __('lexi.app.step_search_title') }}</h3>
      <p>{{ __('lexi.app.step_search_text') }}</p>
    </div>
    <div class="how-step-divider" aria-hidden="true"></div>
    <div class="how-step">
      <div class="how-step-num">2</div>
      <h3>{{ __('lexi.app.step_save_title') }}</h3>
      <p>{{ __('lexi.app.step_save_text') }}</p>
    </div>
    <div class="how-step-divider" aria-hidden="true"></div>
    <div class="how-step">
      <div class="how-step-num">3</div>
      <h3>{{ __('lexi.app.step_practice_title') }}</h3>
      <p>{{ __('lexi.app.step_practice_text') }}</p>
    </div>
  </div>
</section>

<section class="container home-premium-section">
  <article class="home-card home-premium-card">
    <div class="home-card-icon"><i class="bi bi-gem"></i></div>
    <h3 class="h5">{{ __('lexi.app.premium_title') }}</h3>
    <p>{{ __('lexi.app.premium_text') }}</p>
    <a class="btn btn-success" href="{{ route('producto') }}">{{ __('lexi.app.view_premium_plans') }}</a>
  </article>
  <div class="home-premium-image">
    <img src="images/premium_illustration.png" alt="{{ __('lexi.app.premium_image_alt') }}">
  </div>
</section>

<div class="home-cta-banner">
  <p>{{ __('lexi.app.cta_title') }}</p>
  <a class="btn btn-warning" href="{{ route('biblioteca') }}">{{ __('lexi.app.explore_catalog') }}</a>
</div>
@endsection