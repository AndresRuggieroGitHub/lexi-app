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
	$intervalLabels = [
		'monthly' => __('lexi.admin.billing.interval_monthly'),
		'annual' => __('lexi.admin.billing.interval_annual'),
	];
	$currency = $stats['mrr_currency'] ?? 'EUR';
@endphp

<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.billing.heading') }}</h1>
		<p>{{ __('lexi.admin.billing.intro') }}</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.billing.back_panel') }}</a>
	</div>
</section>

<p class="admin-empty-state" style="margin-top: 0;">
	<i class="bi bi-diagram-3"></i>
	{{ __('lexi.admin.billing.scope_note') }}
</p>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.billing.plans_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-gem"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['plans']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.billing.plans_active') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-check2-circle"></i> {{ __('lexi.admin.billing.plans_mix', ['free' => $stats['free_plans'], 'paid' => $stats['paid_plans']]) }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.billing.subscriptions_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-people"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['active_subscriptions']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.billing.subscriptions_active') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-stars"></i> {{ __('lexi.admin.billing.paid_share', ['paid' => number_format($stats['paid_subscriptions']), 'percent' => number_format($stats['paid_share_percent'])]) }} · {{ __('lexi.admin.billing.portfolio_total', ['count' => number_format($stats['total_subscriptions'])]) }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--amber">{{ __('lexi.admin.billing.mrr_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-cash-stack"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['mrr_cents'] / 100, 2, ',', '.') }} {{ $currency }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.billing.estimated_monthly_income') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-lightning-charge"></i> {{ __('lexi.admin.billing.trialing_count', ['count' => number_format($stats['trialing_subscriptions'])]) }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.billing.operations_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-calendar-check"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['renewals_next_30']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.billing.renewals_next_30') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-info-circle"></i> {{ __('lexi.admin.billing.renewals_scope') }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.billing.plans_title') }}</h2>
				<p>{{ __('lexi.admin.billing.plans_text') }}</p>
			</div>
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.billing.real_chip') }}</span>
		</div>
		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_plan') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_code') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_type') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_price') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_interval') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_subscribers') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_status') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($plans as $plan)
						<tr>
							<td>{{ $plan->name }}</td>
							<td>{{ $plan->code }}</td>
							<td>{{ (int) $plan->price_cents === 0 ? __('lexi.admin.billing.type_free') : __('lexi.admin.billing.type_paid') }}</td>
							<td>{{ number_format($plan->price_cents / 100, 2, ',', '.') }} {{ $plan->currency }}</td>
							<td>{{ $intervalLabels[$plan->billing_interval] ?? $plan->billing_interval }}</td>
							<td>{{ number_format($plan->subscribers) }}</td>
							<td><span class="admin-status {{ $plan->is_active ? 'admin-status--active' : 'admin-status--draft' }}">{{ $plan->is_active ? __('lexi.admin.billing.status_active') : __('lexi.admin.billing.status_inactive') }}</span></td>
						</tr>
					@empty
						<tr>
							<td colspan="7">{{ __('lexi.admin.billing.no_plans') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.billing.recent_subscriptions_title') }}</h2>
				<p>{{ __('lexi.admin.billing.recent_subscriptions_text') }}</p>
			</div>
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.billing.persistence_chip') }}</span>
		</div>
		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_user') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_email') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_plan') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_quantity') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_status') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_start') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_renewal') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_provider') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($subscriptions as $subscription)
						<tr>
							<td>{{ trim(($subscription->name ?? '') . ' ' . ($subscription->surname ?? '')) ?: __('lexi.admin.billing.no_name') }}</td>
							<td>{{ $subscription->email }}</td>
							<td>{{ $subscription->plan_name }}</td>
							<td>{{ number_format($subscription->quantity) }}</td>
							<td><span class="admin-status {{ in_array($subscription->status, ['active', 'trialing'], true) ? 'admin-status--active' : 'admin-status--draft' }}">{{ $subscriptionStatusLabels[$subscription->status] ?? $subscription->status }}</span></td>
							<td>{{ $subscription->starts_at ? \Illuminate\Support\Carbon::parse($subscription->starts_at)->format('d/m/Y') : __('lexi.admin.billing.no_date') }}</td>
							<td>{{ $subscription->renews_at ? \Illuminate\Support\Carbon::parse($subscription->renews_at)->format('d/m/Y') : __('lexi.admin.billing.no_date') }}</td>
							<td>{{ $subscription->provider ?: __('lexi.admin.billing.manual_provider') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="8">{{ __('lexi.admin.billing.no_subscriptions') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection