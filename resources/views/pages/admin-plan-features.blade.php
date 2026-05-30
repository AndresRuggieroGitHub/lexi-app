@extends('layouts.admin', ['title' => __('lexi.admin.plan_features.meta_title'), 'description' => __('lexi.admin.plan_features.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.plan_features.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.plan_features.sidebar_text')])

@section('content')
@php
	$featureLabels = [
		'library.access' => __('lexi.admin.billing.feature_library_access'),
		'exercises.basic' => __('lexi.admin.billing.feature_basic_exercises'),
		'analytics.basic' => __('lexi.admin.billing.feature_basic_analytics'),
		'teacher.guide' => __('lexi.admin.billing.feature_teacher_guide'),
		'exercises.personalized' => __('lexi.admin.billing.feature_personalized_exercises'),
	];
	$featureOrder = ['library.access', 'exercises.basic', 'analytics.basic', 'teacher.guide', 'exercises.personalized'];
	$featureMatrix = [];
	foreach ($planFeatures as $feature) {
		$featureMatrix[$feature->feature_key][$feature->plan_code] = $feature->feature_value;
	}
	$freePlanCode = optional($plans->firstWhere('code', 'free'))->code;
	$premiumMonthlyCode = optional($plans->firstWhere('code', 'plan-premium-mensual'))->code;
	$premiumAnnualCode = optional($plans->firstWhere('code', 'plan-premium-anual'))->code;
	$remainingFeatureKeys = array_diff(array_keys($featureMatrix), $featureOrder);
	$orderedFeatureKeys = array_values(array_merge($featureOrder, $remainingFeatureKeys));
@endphp

<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.plan_features.heading') }}</h1>
		<p>{{ __('lexi.admin.plan_features.intro') }}</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.plan_features.back_panel') }}</a>
	</div>
</section>

<p class="admin-empty-state" style="margin-top: 0;">
	<i class="bi bi-diagram-3"></i>
	{{ __('lexi.admin.plan_features.scope_note') }}
</p>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.plan_features.catalog_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-gem"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['plans']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.plan_features.plans_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-sliders2"></i> {{ __('lexi.admin.plan_features.features_meta', ['count' => number_format($stats['features'])]) }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.plan_features.coverage_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-stars"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['premium_only']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.plan_features.premium_only_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-check2-circle"></i> {{ __('lexi.admin.plan_features.premium_only_meta') }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--amber">{{ __('lexi.admin.plan_features.control_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-shield-check"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['mismatches']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.plan_features.mismatch_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-exclamation-triangle"></i> {{ __('lexi.admin.plan_features.mismatch_meta') }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.plan_features.table_title') }}</h2>
				<p>{{ __('lexi.admin.plan_features.table_text') }}</p>
			</div>
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.billing.configuration_chip') }}</span>
		</div>
		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_feature') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.table_coverage') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.type_free') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.premium_monthly_label') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.billing.premium_annual_label') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($orderedFeatureKeys as $featureKey)
						@php
							$freeValue = $freePlanCode ? ($featureMatrix[$featureKey][$freePlanCode] ?? null) : null;
							$monthlyValue = $premiumMonthlyCode ? ($featureMatrix[$featureKey][$premiumMonthlyCode] ?? null) : null;
							$annualValue = $premiumAnnualCode ? ($featureMatrix[$featureKey][$premiumAnnualCode] ?? null) : null;
							$freeEnabled = $freeValue === 'enabled';
							$monthlyEnabled = $monthlyValue === 'enabled';
							$annualEnabled = $annualValue === 'enabled';
							$isPremiumMismatch = $premiumMonthlyCode && $premiumAnnualCode && $monthlyValue !== null && $annualValue !== null && $monthlyValue !== $annualValue;
							$coverageClass = 'admin-status--draft';
							$coverageLabel = __('lexi.admin.billing.coverage_not_included');

							if ($isPremiumMismatch) {
								$coverageClass = 'admin-status--review';
								$coverageLabel = __('lexi.admin.billing.coverage_mismatch');
							} elseif ($freeEnabled && $monthlyEnabled && $annualEnabled) {
								$coverageClass = 'admin-status--active';
								$coverageLabel = __('lexi.admin.billing.coverage_all');
							} elseif (!$freeEnabled && ($monthlyEnabled || $annualEnabled)) {
								$coverageClass = 'admin-status--review';
								$coverageLabel = __('lexi.admin.billing.coverage_premium');
							} elseif ($freeEnabled && !$monthlyEnabled && !$annualEnabled) {
								$coverageClass = 'admin-status--draft';
								$coverageLabel = __('lexi.admin.billing.coverage_free_only');
							}
						@endphp
						<tr>
							<td>{{ $featureLabels[$featureKey] ?? $featureKey }}</td>
							<td><span class="admin-status {{ $coverageClass }}">{{ $coverageLabel }}</span></td>
							@foreach ([$freeValue, $monthlyValue, $annualValue] as $featureValue)
								<td>
									@if ($featureValue === 'enabled')
										<span class="admin-status admin-status--active"><i class="bi bi-check2"></i> {{ __('lexi.admin.billing.value_enabled') }}</span>
									@elseif ($featureValue === 'disabled')
										<span class="admin-status admin-status--draft"><i class="bi bi-x"></i> {{ __('lexi.admin.billing.value_disabled') }}</span>
									@else
										{{ __('lexi.admin.billing.na') }}
									@endif
								</td>
							@endforeach
						</tr>
					@empty
						<tr>
							<td colspan="5">{{ __('lexi.admin.billing.no_features') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection
