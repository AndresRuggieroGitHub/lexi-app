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

  <section class="cart-checkout-panel mt-3" id="cartCheckoutPanel" aria-label="{{ __('lexi.cart.checkout_actions_aria') }}" hidden>
    <div class="cart-checkout-panel__summary" aria-live="polite">
      <span>{{ __('lexi.cart.order_total') }}</span>
      <strong id="checkoutTotal">0 EUR</strong>
    </div>
    <div class="cart-checkout-panel__actions">
      <button id="clearCart" class="btn btn-outline-danger" type="button">{{ __('lexi.cart.clear') }}</button>
      <button id="checkoutCart" class="btn btn-success" type="button" data-checkout-label="{{ __('lexi.js.cart.pay_now') }}">{{ __('lexi.js.cart.pay_now') }}</button>
    </div>
    <p class="cart-checkout-panel__status d-none" id="checkoutStatus" role="status" aria-live="polite"></p>
  </section>
  <p class="cart-empty-tip text-muted mt-2 mb-0" id="cartEmptyTip">{{ __('lexi.cart.empty') }}</p>
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

<div class="modal fade" id="checkoutPaymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content payment-modal">
      <div class="modal-header">
        <h2 class="h5 mb-0">{{ __('lexi.cart.payment_confirm_title') }}</h2>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="{{ __('lexi.cart.close') }}"></button>
      </div>
      <div class="modal-body">
        <div class="payment-summary mb-3">
          <p class="payment-summary__title mb-2">{{ __('lexi.cart.order_summary') }}</p>
          <div id="paymentSummaryItems"></div>
          <div class="payment-summary__total mt-2">
            <span>{{ __('lexi.cart.total') }}</span>
            <strong id="paymentSummaryTotal">0 EUR</strong>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="paymentFullName">{{ __('lexi.cart.holder') }}</label>
          <input class="form-control" id="paymentFullName" type="text" placeholder="{{ __('lexi.cart.holder_placeholder') }}" autocomplete="name">
        </div>

        <fieldset class="mb-3">
          <legend class="form-label mb-2">{{ __('lexi.cart.payment_method') }}</legend>
          <div class="payment-methods">
            <label class="payment-method-option">
              <input type="radio" name="paymentMethod" value="card" checked>
              <span>{{ __('lexi.cart.method_card') }}</span>
            </label>
            <label class="payment-method-option">
              <input type="radio" name="paymentMethod" value="paypal">
              <span>{{ __('lexi.cart.method_paypal') }}</span>
            </label>
          </div>
        </fieldset>

        <div id="cardFields" class="row g-2">
          <div class="col-12 col-sm-8">
            <label class="form-label" for="paymentCardLast4">{{ __('lexi.cart.card_last4') }}</label>
            <input class="form-control" id="paymentCardLast4" type="text" inputmode="numeric" maxlength="4" placeholder="1234">
          </div>
          <div class="col-12 col-sm-4">
            <label class="form-label" for="paymentCardExp">{{ __('lexi.cart.card_expiry') }}</label>
            <input class="form-control" id="paymentCardExp" type="text" placeholder="MM/AA" maxlength="5">
          </div>
        </div>

        <p id="paymentFormError" class="payment-form-error d-none mt-3 mb-0" role="alert"></p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{ __('lexi.common.cancel') }}</button>
        <button class="btn btn-success" id="confirmCheckoutPayment" type="button" data-default-label="{{ __('lexi.cart.confirm_and_pay') }}">{{ __('lexi.cart.confirm_and_pay') }}</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="checkoutSuccessModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content payment-success-modal">
      <div class="modal-header">
        <h2 class="h5 mb-0">{{ __('lexi.cart.payment_success_title') }}</h2>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="{{ __('lexi.cart.close') }}"></button>
      </div>
      <div class="modal-body">
        <p class="payment-success-modal__title mb-2">{{ __('lexi.cart.payment_success_body') }}</p>
        <div class="payment-success-ticket">
          <div class="payment-success-ticket__row">
            <span>{{ __('lexi.cart.order') }}</span>
            <strong id="successOrderCode">-</strong>
          </div>
          <div class="payment-success-ticket__row">
            <span>{{ __('lexi.cart.method') }}</span>
            <strong id="successPaymentMethod">-</strong>
          </div>
          <div class="payment-success-ticket__row">
            <span>{{ __('lexi.cart.total') }}</span>
            <strong id="successOrderTotal">0 EUR</strong>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="button" data-bs-dismiss="modal">{{ __('lexi.cart.continue') }}</button>
      </div>
    </div>
  </div>
</div>
    @endsection