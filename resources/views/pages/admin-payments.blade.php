@extends('layouts.admin', ['title' => __('lexi.admin.payments.meta_title'), 'description' => __('lexi.admin.payments.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.payments.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.payments.sidebar_text')])

@section('content')
@php
	$paymentStatusLabels = [
		'paid' => __('lexi.admin.payments.status_paid'),
		'pending' => __('lexi.admin.payments.status_pending'),
		'failed' => __('lexi.admin.payments.status_failed'),
		'canceled' => __('lexi.admin.payments.status_canceled'),
		'cancelled' => __('lexi.admin.payments.status_canceled'),
		'refunded' => __('lexi.admin.payments.status_refunded'),
	];
	$currency = $stats['currency'] ?? 'EUR';
@endphp

<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.payments.heading') }}</h1>
		<p>{{ __('lexi.admin.payments.intro') }}</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.payments.back_panel') }}</a>
	</div>
</section>

<p class="admin-empty-state" style="margin-top: 0;">
	<i class="bi bi-diagram-3"></i>
	{{ __('lexi.admin.payments.scope_note') }}
</p>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.payments.ledger_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-receipt"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['total']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.payments.total_records') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-check2"></i> {{ __('lexi.admin.payments.paid_count', ['count' => number_format($stats['paid'])]) }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.payments.revenue_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-cash-stack"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['revenue_cents'] / 100, 2, ',', '.') }} {{ $currency }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.payments.total_revenue') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-calendar-check"></i> {{ __('lexi.admin.payments.pending_count', ['count' => number_format($stats['pending'])]) }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--amber">{{ __('lexi.admin.payments.control_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-exclamation-triangle"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['failed']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.payments.failed_records') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-shield-check"></i> {{ __('lexi.admin.payments.healthy_rate', ['percent' => number_format($stats['healthy_rate_percent'])]) }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.payments.recent_title') }}</h2>
				<p>{{ __('lexi.admin.payments.recent_text') }}</p>
			</div>
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.payments.ledger_chip') }}</span>
		</div>
		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th class="admin-table__th-strong">ID</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.payments.table_user') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.payments.table_email') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.payments.table_plan') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.payments.table_amount') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.payments.table_status') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.payments.table_provider') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.payments.table_date') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($payments as $payment)
						<tr>
							<td>{{ $payment->id }}</td>
							<td>{{ trim(($payment->name ?? '') . ' ' . ($payment->surname ?? '')) ?: __('lexi.admin.payments.no_name') }}</td>
							<td>{{ $payment->email }}</td>
							<td>{{ $payment->plan_name }}</td>
							<td>{{ number_format($payment->amount_cents / 100, 2, ',', '.') }} {{ $payment->currency }}</td>
							<td><span class="admin-status {{ $payment->status === 'paid' ? 'admin-status--active' : ($payment->status === 'failed' ? 'admin-status--review' : 'admin-status--draft') }}">{{ $paymentStatusLabels[$payment->status] ?? $payment->status }}</span></td>
							<td>{{ $payment->provider ?: __('lexi.admin.payments.manual_provider') }}</td>
							<td>{{ $payment->paid_at ? \Illuminate\Support\Carbon::parse($payment->paid_at)->format('d/m/Y') : __('lexi.admin.payments.no_date') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="8">{{ __('lexi.admin.payments.no_payments') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection
