@extends('layouts.site', ['title' => __('lexi.meta.cart.title'), 'description' => __('lexi.meta.cart.description'), 'robots' => 'index,follow'])

@section('content')
<main id="mainContent" class="page-main container section-space" data-cart-page>
  <h1 class="mb-4">{{ __('lexi.cart.title') }}</h1>

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>{{ __('lexi.cart.product') }}</th>
          <th>{{ __('lexi.cart.quantity') }}</th>
          <th>{{ __('lexi.cart.price') }}</th>
          <th>{{ __('lexi.cart.subtotal') }}</th>
          <th>{{ __('lexi.cart.actions') }}</th>
        </tr>
      </thead>
      <tbody id="cartItems"></tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="text-end">{{ __('lexi.cart.total') }}</th>
          <th id="cartTotal">0 EUR</th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </div>

  <button id="clearCart" class="btn btn-outline-danger" type="button">{{ __('lexi.cart.clear') }}</button>
</main>

<div class="modal fade" id="confirmRemoveModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="h5 mb-0">{{ __('lexi.cart.confirm_title') }}</h2>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="{{ __('lexi.cart.close') }}"></button>
      </div>
      <div class="modal-body">{{ __('lexi.cart.confirm_body') }}</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{ __('lexi.common.cancel') }}</button>
        <button class="btn btn-danger" id="confirmRemoveBtn" type="button">{{ __('lexi.cart.remove') }}</button>
      </div>
    </div>
  </div>
</div>
    @endsection