@extends('layouts.site', ['title' => 'Lexi | ' . __('lexi.nav.premium'), 'description' => __('lexi.product.subtitle'), 'robots' => 'index,follow'])

@section('content')
<main id="mainContent" class="page-main container section-space">
  <h1 class="mb-4">{{ __('lexi.nav.premium') }}</h1>
  <p class="text-muted mb-4">{{ __('lexi.product.subtitle') }}</p>

  <section class="plan-grid">
    <article class="product-info">
      <h2 class="h4">{{ __('lexi.product.monthly_title') }}</h2>
      <p class="product-price">{{ __('lexi.product.monthly_price') }}</p>
      <ul>
        <li>{{ __('lexi.product.monthly_feature_1') }}</li>
        <li>{{ __('lexi.product.monthly_feature_2') }}</li>
        <li>{{ __('lexi.product.monthly_feature_3') }}</li>
      </ul>
      <button class="btn btn-success mt-3" type="button" data-add-cart data-id="plan-premium-mensual" data-name="{{ __('lexi.product.monthly_cart_name') }}" data-price="9">{{ __('lexi.js.cart.go_to_cart') }}</button>
    </article>

    <article class="product-info">
      <h2 class="h4">{{ __('lexi.product.annual_title') }}</h2>
      <p class="product-price">{{ __('lexi.product.annual_price') }}</p>
      <ul>
        <li>{{ __('lexi.product.annual_feature_1') }}</li>
        <li>{{ __('lexi.product.annual_feature_2') }}</li>
        <li>{{ __('lexi.product.annual_feature_3') }}</li>
      </ul>
      <button class="btn btn-success mt-3" type="button" data-add-cart data-id="plan-premium-anual" data-name="{{ __('lexi.product.annual_cart_name') }}" data-price="79">{{ __('lexi.js.cart.go_to_cart') }}</button>
    </article>
  </section>
</main>
@endsection