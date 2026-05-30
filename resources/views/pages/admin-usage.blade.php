@extends('layouts.admin', ['title' => __('lexi.admin.usage.meta_title'), 'description' => __('lexi.admin.usage.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.usage.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.usage.sidebar_text')])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.usage.heading') }}</h1>
		<p>{{ __('lexi.admin.usage.intro') }}</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.usage.back_panel') }}</a>
	</div>
</section>

<p class="admin-empty-state" style="margin-top: 0;">
	<i class="bi bi-diagram-3"></i>
	{{ __('lexi.admin.usage.scope_note') }}
</p>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--amber">{{ __('lexi.admin.usage.periods_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-calendar3"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['rows']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.usage.periods_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-people"></i> {{ __('lexi.admin.usage.users_meta', ['count' => number_format($stats['users'])]) }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.usage.ai_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-robot"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['ai']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.usage.ai_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-grid"></i> {{ __('lexi.admin.usage.generated_meta', ['count' => number_format($stats['generated'])]) }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.usage.progress_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-activity"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['attempts']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.usage.attempts_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-bookmark-check"></i> {{ __('lexi.admin.usage.saved_meta', ['count' => number_format($stats['saved'])]) }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.usage.table_title') }}</h2>
				<p>{{ __('lexi.admin.usage.table_text') }}</p>
			</div>
			<span class="admin-chip admin-chip--amber">{{ __('lexi.admin.usage.metric_chip') }}</span>
		</div>
		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th class="admin-table__th-strong">{{ __('lexi.admin.usage.table_user') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.usage.table_email') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.usage.table_period') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.usage.table_ai') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.usage.table_generated') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.usage.table_attempts') }}</th>
						<th class="admin-table__th-strong">{{ __('lexi.admin.usage.table_saved') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($usageRows as $usage)
						<tr>
							<td>{{ trim(($usage->name ?? '') . ' ' . ($usage->surname ?? '')) ?: __('lexi.admin.usage.no_name') }}</td>
							<td>{{ $usage->email }}</td>
							<td>{{ \Illuminate\Support\Carbon::parse($usage->period_start)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($usage->period_end)->format('d/m/Y') }}</td>
							<td>{{ number_format($usage->ai_generations_count) }}</td>
							<td>{{ number_format($usage->exercises_generated_count) }}</td>
							<td>{{ number_format($usage->exercise_attempts_count) }}</td>
							<td>{{ number_format($usage->saved_words_count) }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="7">{{ __('lexi.admin.usage.no_usage') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection
