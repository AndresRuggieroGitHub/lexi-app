@extends('layouts.admin', ['title' => __('lexi.admin.billing.meta_title'), 'description' => __('lexi.admin.billing.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.billing.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.billing.sidebar_text')])

@section('content')
@php
	$subscriptionStatusLabels = [
		'active' => __('lexi.admin.billing.status_active'),
		'trialing' => __('lexi.admin.billing.status_trialing'),
		'canceled' => __('lexi.admin.billing.status_canceled'),
		'cancelled' => __('lexi.admin.billing.status_canceled'),
		'pending' => __('lexi.admin.billing.status_pending'),
	];
	$paymentStatusLabels = [
		'paid' => __('lexi.admin.billing.status_paid'),
		'pending' => __('lexi.admin.billing.status_pending'),
		'failed' => __('lexi.admin.billing.status_failed'),
		'canceled' => __('lexi.admin.billing.status_canceled'),
		'cancelled' => __('lexi.admin.billing.status_canceled'),
		'refunded' => __('lexi.admin.billing.status_refunded'),
	];
	$intervalLabels = [
		'monthly' => __('lexi.admin.billing.interval_monthly'),
		'annual' => __('lexi.admin.billing.interval_annual'),
	];
@endphp
<section class="admin-page-head"><div><h1>{{ __('lexi.admin.billing.heading') }}</h1><p>{{ __('lexi.admin.billing.intro') }}</p></div><div class="admin-page-actions"><a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.billing.back_panel') }}</a></div></section>

<section class="admin-stats">
	<article class="admin-card admin-stat"><div class="admin-stat__row"><span class="admin-chip admin-chip--green">{{ __('lexi.admin.billing.plans_chip') }}</span><div class="admin-stat__icon"><i class="bi bi-gem"></i></div></div><p class="admin-stat__value">{{ $stats['plans'] }}</p><p class="admin-stat__label">{{ __('lexi.admin.billing.plans_active') }}</p><span class="admin-stat__meta"><i class="bi bi-check2-circle"></i> {{ __('lexi.admin.billing.base_catalog') }}</span></article>
	<article class="admin-card admin-stat"><div class="admin-stat__row"><span class="admin-chip admin-chip--violet">{{ __('lexi.admin.billing.subscriptions_chip') }}</span><div class="admin-stat__icon"><i class="bi bi-people"></i></div></div><p class="admin-stat__value">{{ $stats['active_subscriptions'] }}</p><p class="admin-stat__label">{{ __('lexi.admin.billing.subscriptions_active') }}</p><span class="admin-stat__meta"><i class="bi bi-stars"></i> {{ __('lexi.admin.billing.paid_count', ['count' => $stats['paid_subscriptions']]) }}</span></article>
	<article class="admin-card admin-stat"><div class="admin-stat__row"><span class="admin-chip admin-chip--amber">{{ __('lexi.admin.billing.mrr_chip') }}</span><div class="admin-stat__icon"><i class="bi bi-cash-stack"></i></div></div><p class="admin-stat__value">{{ number_format($stats['mrr_cents'] / 100, 2, ',', '.') }} EUR</p><p class="admin-stat__label">{{ __('lexi.admin.billing.estimated_monthly_income') }}</p><span class="admin-stat__meta"><i class="bi bi-lightning-charge"></i> {{ __('lexi.admin.billing.trialing_count', ['count' => $stats['trialing_subscriptions']]) }}</span></article>
	<article class="admin-card admin-stat"><div class="admin-stat__row"><span class="admin-chip admin-chip--violet">{{ __('lexi.admin.billing.ledger_chip') }}</span><div class="admin-stat__icon"><i class="bi bi-cash-coin"></i></div></div><p class="admin-stat__value">{{ $stats['payments'] }}</p><p class="admin-stat__label">{{ __('lexi.admin.billing.payments_registered') }}</p><span class="admin-stat__meta"><i class="bi bi-speedometer2"></i> {{ __('lexi.admin.billing.usage_periods', ['count' => $stats['usage_rows']]) }}</span></article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head"><div><h2>{{ __('lexi.admin.billing.plans_title') }}</h2><p>{{ __('lexi.admin.billing.plans_text') }}</p></div><span class="admin-chip admin-chip--green">{{ __('lexi.admin.billing.real_chip') }}</span></div>
		<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>{{ __('lexi.admin.billing.table_plan') }}</th><th>{{ __('lexi.admin.billing.table_code') }}</th><th>{{ __('lexi.admin.billing.table_price') }}</th><th>{{ __('lexi.admin.billing.table_interval') }}</th><th>{{ __('lexi.admin.billing.table_subscribers') }}</th><th>{{ __('lexi.admin.billing.table_status') }}</th></tr></thead><tbody>@forelse ($plans as $plan)<tr><td>{{ $plan->name }}</td><td>{{ $plan->code }}</td><td>{{ number_format($plan->price_cents / 100, 2, ',', '.') }} {{ $plan->currency }}</td><td>{{ $intervalLabels[$plan->billing_interval] ?? $plan->billing_interval }}</td><td>{{ $plan->subscribers }}</td><td><span class="admin-status {{ $plan->is_active ? 'admin-status--active' : 'admin-status--draft' }}">{{ $plan->is_active ? __('lexi.admin.billing.status_active') : __('lexi.admin.billing.status_inactive') }}</span></td></tr>@empty<tr><td colspan="6">{{ __('lexi.admin.billing.no_plans') }}</td></tr>@endforelse</tbody></table></div>
	</div>
</section>

<section class="admin-card" id="features">
	<div class="admin-card__inner">
		<div class="admin-card__head"><div><h2>{{ __('lexi.admin.billing.features_title') }}</h2><p>{{ __('lexi.admin.billing.features_text') }}</p></div><span class="admin-chip admin-chip--violet">{{ __('lexi.admin.billing.configuration_chip') }}</span></div>
		<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>{{ __('lexi.admin.billing.table_plan') }}</th><th>{{ __('lexi.admin.billing.table_code') }}</th><th>{{ __('lexi.admin.billing.table_feature') }}</th><th>{{ __('lexi.admin.billing.table_value') }}</th></tr></thead><tbody>@forelse ($planFeatures as $feature)<tr><td>{{ $feature->plan_name }}</td><td>{{ $feature->plan_code }}</td><td>{{ $feature->feature_key }}</td><td>{{ $feature->feature_value ?: __('lexi.admin.billing.na') }}</td></tr>@empty<tr><td colspan="4">{{ __('lexi.admin.billing.no_features') }}</td></tr>@endforelse</tbody></table></div>
	</div>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head"><div><h2>{{ __('lexi.admin.billing.recent_subscriptions_title') }}</h2><p>{{ __('lexi.admin.billing.recent_subscriptions_text') }}</p></div><span class="admin-chip admin-chip--violet">{{ __('lexi.admin.billing.persistence_chip') }}</span></div>
		<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>{{ __('lexi.admin.billing.table_user') }}</th><th>{{ __('lexi.admin.billing.table_email') }}</th><th>{{ __('lexi.admin.billing.table_plan') }}</th><th>{{ __('lexi.admin.billing.table_status') }}</th><th>{{ __('lexi.admin.billing.table_renewal') }}</th><th>{{ __('lexi.admin.billing.table_provider') }}</th></tr></thead><tbody>@forelse ($subscriptions as $subscription)<tr><td>{{ trim(($subscription->name ?? '') . ' ' . ($subscription->surname ?? '')) ?: __('lexi.admin.billing.no_name') }}</td><td>{{ $subscription->email }}</td><td>{{ $subscription->plan_name }}</td><td><span class="admin-status {{ in_array($subscription->status, ['active', 'trialing'], true) ? 'admin-status--active' : 'admin-status--draft' }}">{{ $subscriptionStatusLabels[$subscription->status] ?? $subscription->status }}</span></td><td>{{ $subscription->renews_at ? \Illuminate\Support\Carbon::parse($subscription->renews_at)->format('d/m/Y') : __('lexi.admin.billing.no_date') }}</td><td>{{ $subscription->provider ?: __('lexi.admin.billing.manual_provider') }}</td></tr>@empty<tr><td colspan="6">{{ __('lexi.admin.billing.no_subscriptions') }}</td></tr>@endforelse</tbody></table></div>
	</div>
</section>

<section class="admin-card" id="payments">
	<div class="admin-card__inner">
		<div class="admin-card__head"><div><h2>{{ __('lexi.admin.billing.recent_payments_title') }}</h2><p>{{ __('lexi.admin.billing.recent_payments_text') }}</p></div><span class="admin-chip admin-chip--green">{{ __('lexi.admin.billing.ledger_chip_label') }}</span></div>
		<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>ID</th><th>{{ __('lexi.admin.billing.table_user') }}</th><th>{{ __('lexi.admin.billing.table_plan') }}</th><th>{{ __('lexi.admin.billing.table_amount') }}</th><th>{{ __('lexi.admin.billing.table_status') }}</th><th>{{ __('lexi.admin.billing.table_provider') }}</th><th>{{ __('lexi.admin.billing.table_date') }}</th></tr></thead><tbody>@forelse ($payments as $payment)<tr><td>{{ $payment->id }}</td><td>{{ trim(($payment->name ?? '') . ' ' . ($payment->surname ?? '')) ?: $payment->email }}</td><td>{{ $payment->plan_name }}</td><td>{{ number_format($payment->amount_cents / 100, 2, ',', '.') }} {{ $payment->currency }}</td><td><span class="admin-status {{ $payment->status === 'paid' ? 'admin-status--active' : 'admin-status--review' }}">{{ $paymentStatusLabels[$payment->status] ?? $payment->status }}</span></td><td>{{ $payment->provider ?: __('lexi.admin.billing.manual_provider') }}</td><td>{{ $payment->paid_at ? \Illuminate\Support\Carbon::parse($payment->paid_at)->format('d/m/Y') : __('lexi.admin.billing.no_date') }}</td></tr>@empty<tr><td colspan="7">{{ __('lexi.admin.billing.no_payments') }}</td></tr>@endforelse</tbody></table></div>
	</div>
</section>

<section class="admin-card" id="usage">
	<div class="admin-card__inner">
		<div class="admin-card__head"><div><h2>{{ __('lexi.admin.billing.usage_title') }}</h2><p>{{ __('lexi.admin.billing.usage_text') }}</p></div><span class="admin-chip admin-chip--amber">{{ __('lexi.admin.billing.metric_chip') }}</span></div>
		<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>{{ __('lexi.admin.billing.table_user') }}</th><th>{{ __('lexi.admin.billing.table_period') }}</th><th>{{ __('lexi.admin.billing.table_ai') }}</th><th>{{ __('lexi.admin.billing.table_exercises_generated') }}</th><th>{{ __('lexi.admin.billing.table_attempts') }}</th><th>{{ __('lexi.admin.billing.table_saved_words') }}</th></tr></thead><tbody>@forelse ($usageRows as $usage)<tr><td>{{ trim(($usage->name ?? '') . ' ' . ($usage->surname ?? '')) ?: $usage->email }}</td><td>{{ \Illuminate\Support\Carbon::parse($usage->period_start)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($usage->period_end)->format('d/m/Y') }}</td><td>{{ $usage->ai_generations_count }}</td><td>{{ $usage->exercises_generated_count }}</td><td>{{ $usage->exercise_attempts_count }}</td><td>{{ $usage->saved_words_count }}</td></tr>@empty<tr><td colspan="6">{{ __('lexi.admin.billing.no_usage') }}</td></tr>@endforelse</tbody></table></div>
	</div>
</section>

<section class="admin-card"><div class="admin-card__inner"><div class="admin-card__head"><div><h2>{{ __('lexi.admin.billing.debt_title') }}</h2><p>{{ __('lexi.admin.billing.debt_text') }}</p></div><span class="admin-chip admin-chip--amber">{{ __('lexi.admin.billing.pending_chip') }}</span></div><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>{{ __('lexi.admin.billing.table_area') }}</th><th>{{ __('lexi.admin.billing.table_needed') }}</th><th>{{ __('lexi.admin.billing.table_status') }}</th></tr></thead><tbody><tr><td>{{ __('lexi.admin.billing.checkout') }}</td><td>{{ __('lexi.admin.billing.checkout_needed') }}</td><td><span class="admin-status admin-status--draft">{{ __('lexi.admin.billing.status_pending') }}</span></td></tr><tr><td>{{ __('lexi.admin.billing.webhooks') }}</td><td>{{ __('lexi.admin.billing.webhooks_needed') }}</td><td><span class="admin-status admin-status--draft">{{ __('lexi.admin.billing.status_pending') }}</span></td></tr><tr><td>{{ __('lexi.admin.billing.history') }}</td><td>{{ __('lexi.admin.billing.history_needed') }}</td><td><span class="admin-status admin-status--draft">{{ __('lexi.admin.billing.status_pending') }}</span></td></tr></tbody></table></div></div></section>
@endsection