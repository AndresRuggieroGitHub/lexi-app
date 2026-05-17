@extends('layouts.site', ['title' => __('lexi.meta.info.title'), 'description' => __('lexi.meta.info.description'), 'robots' => 'index,follow'])

@section('content')
<main id="mainContent" class="info page-main">
  <h1>{{ __('lexi.info.title') }}</h1>
  <p>{{ __('lexi.info.subtitle') }}</p>

  <section class="info-grid">
    <article class="info-card">
      <h2 class="h5">{{ __('lexi.info.step_1_title') }}</h2>
      <p>{{ __('lexi.info.step_1_text') }}</p>
    </article>
    <article class="info-card">
      <h2 class="h5">{{ __('lexi.info.step_2_title') }}</h2>
      <p>{{ __('lexi.info.step_2_text') }}</p>
    </article>
    <article class="info-card">
      <h2 class="h5">{{ __('lexi.info.step_3_title') }}</h2>
      <p>{{ __('lexi.info.step_3_text') }}</p>
    </article>
  </section>

  <section class="container mt-4">
    <h2 class="h4">{{ __('lexi.info.access_title') }}</h2>
    <p>{{ __('lexi.info.access_text') }}</p>
  </section>
</main>
@endsection