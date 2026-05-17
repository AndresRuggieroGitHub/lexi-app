@extends('layouts.admin', ['title' => __('lexi.admin.analytics.meta_title'), 'description' => __('lexi.admin.analytics.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.analytics.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.analytics.sidebar_text')])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.analytics.heading') }}</h1>
		<p>{{ __('lexi.admin.analytics.intro') }}</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.analytics.back_panel') }}</a>
	</div>
</section>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.analytics.recorded') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-graph-up-arrow"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['records']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.analytics.records_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-activity"></i> {{ __('lexi.admin.analytics.records_meta') }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.analytics.completion') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-trophy"></i></div>
		</div>
		<p class="admin-stat__value">{{ $stats['completion_rate'] }}%</p>
		<p class="admin-stat__label">{{ __('lexi.admin.analytics.completion_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-lightning-charge"></i> {{ __('lexi.admin.analytics.completion_meta') }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--amber">{{ __('lexi.admin.analytics.review') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-arrow-repeat"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['review_due']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.analytics.review_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-clock-history"></i> {{ __('lexi.admin.analytics.review_meta') }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.analytics.overview_title') }}</h2>
				<p>{{ __('lexi.admin.analytics.overview_text') }}</p>
			</div>
			<span class="admin-chip admin-chip--amber">{{ __('lexi.admin.analytics.reports') }}</span>
		</div>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>{{ __('lexi.admin.analytics.metric') }}</th>
						<th>{{ __('lexi.admin.analytics.value') }}</th>
						<th>{{ __('lexi.admin.analytics.window') }}</th>
						<th>{{ __('lexi.admin.analytics.status') }}</th>
					</tr>
				</thead>
				<tbody>
					@foreach($overview as $item)
						<tr>
							<td>{{ $item['metric'] }}</td>
							<td>{{ $item['value'] }}</td>
							<td>{{ $item['window'] }}</td>
							<td><span class="admin-status {{ $item['statusClass'] }}">{{ $item['status'] }}</span></td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.analytics.recent_activity_title') }}</h2>
				<p>{{ __('lexi.admin.analytics.recent_activity_text') }}</p>
			</div>
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.analytics.activity') }}</span>
		</div>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>{{ __('lexi.admin.analytics.user') }}</th>
						<th>{{ __('lexi.admin.analytics.exercise') }}</th>
						<th>{{ __('lexi.admin.analytics.result') }}</th>
						<th>{{ __('lexi.admin.analytics.score') }}</th>
						<th>{{ __('lexi.admin.analytics.completed') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse($recentActivity as $row)
						<tr>
							<td>{{ $row->id }}</td>
							<td>{{ $row->user_name ?: __('lexi.admin.analytics.deleted_user') }}</td>
							<td>{{ $row->exercise_title ?: __('lexi.admin.analytics.untitled_exercise') }}</td>
							<td>{{ $row->result_status ?: __('lexi.admin.analytics.no_status') }}</td>
							<td>{{ $row->score !== null ? $row->score : __('lexi.admin.exercises.na') }}</td>
							<td>{{ $row->completed_at ?: __('lexi.admin.analytics.pending') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="6">{{ __('lexi.admin.analytics.no_activity') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection