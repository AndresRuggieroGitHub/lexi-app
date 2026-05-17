@extends('layouts.site', ['title' => __('lexi.meta.contact.title'), 'description' => __('lexi.meta.contact.description'), 'robots' => 'index,follow'])

@section('content')
<main id="mainContent" class="page-main container section-space">
  <h1 class="mb-3">{{ __('lexi.contact.title') }}</h1>
  <p class="text-muted mb-4">{{ __('lexi.contact.subtitle') }}</p>

  <div class="contact-grid">
    <section>
      <h2 class="h4">{{ __('lexi.contact.send_query') }}</h2>
      <form class="contact-form" action="#" method="post">
        <div class="mb-3">
          <label for="nombre" class="form-label">{{ __('lexi.contact.name') }}</label>
          <input id="nombre" name="nombre" class="form-control" type="text" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">{{ __('lexi.contact.email') }}</label>
          <input id="email" name="email" class="form-control" type="email" required>
        </div>
        <div class="mb-3">
          <label for="motivo" class="form-label">{{ __('lexi.contact.reason') }}</label>
          <select id="motivo" name="motivo" class="form-select" required>
            <option value="">{{ __('lexi.contact.reason_placeholder') }}</option>
            <option value="tecnico">{{ __('lexi.contact.reason_technical') }}</option>
            <option value="premium">{{ __('lexi.contact.reason_billing') }}</option>
            <option value="profesor">{{ __('lexi.contact.reason_teacher') }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="mensaje" class="form-label">{{ __('lexi.contact.message') }}</label>
          <textarea id="mensaje" name="mensaje" class="form-control" rows="4" required></textarea>
        </div>
        <button class="btn btn-primary" type="submit">{{ __('lexi.contact.send') }}</button>
      </form>
    </section>

    <section>
      <h2 class="h4">{{ __('lexi.contact.channels') }}</h2>
      <div class="support-list">
        <article class="support-item">
          <h3 class="h6 mb-1">{{ __('lexi.contact.technical_title') }}</h3>
          <p class="mb-1">{{ __('lexi.contact.technical_text') }}</p>
          <p class="mb-0">soporte@lexi.app</p>
        </article>
        <article class="support-item">
          <h3 class="h6 mb-1">{{ __('lexi.contact.billing_title') }}</h3>
          <p class="mb-1">{{ __('lexi.contact.billing_text') }}</p>
          <p class="mb-0">billing@lexi.app</p>
        </article>
        <article class="support-item">
          <h3 class="h6 mb-1">{{ __('lexi.contact.teachers_title') }}</h3>
          <p class="mb-1">{{ __('lexi.contact.teachers_text') }}</p>
          <p class="mb-0">teachers@lexi.app</p>
        </article>
      </div>
    </section>
  </div>
</main>
@endsection