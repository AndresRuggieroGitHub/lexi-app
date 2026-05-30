@extends('layouts.admin', ['title' => __('lexi.admin.words.meta_title'), 'description' => __('lexi.admin.words.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.words.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.words.sidebar_text')])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.words.heading') }}</h1>
		<p>{{ __('lexi.admin.words.intro') }}</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.words.back_panel') }}</a>
	</div>
</section>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--amber">{{ __('lexi.admin.words.words_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-book"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['words']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.words.words_rows') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-database"></i> {{ $databaseInfo['driver'] }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.words.categories_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-tags"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['categories']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.words.categories_rows') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-diagram-3"></i> {{ __('lexi.admin.words.categories_meta') }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.words.usage_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-bookmark-check"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['saved_links']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.words.usage_rows') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-hdd-stack"></i> {{ $databaseInfo['database'] ?: __('lexi.admin.words.unnamed_database') }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.words.database_snapshot') }}</h2>
				<p>{{ __('lexi.admin.words.database_snapshot_text') }}</p>
			</div>
		</div>

		<div class="admin-db-snapshot" role="region" aria-label="{{ __('lexi.admin.words.database_snapshot') }}">
			<div class="admin-db-snapshot__grid">
				<article class="admin-db-kpi">
					<span class="admin-db-kpi__label">{{ __('lexi.admin.words.connection') }}</span>
					<p class="admin-db-kpi__value">{{ $databaseInfo['connection'] }}</p>
				</article>
				<article class="admin-db-kpi">
					<span class="admin-db-kpi__label">{{ __('lexi.admin.words.driver') }}</span>
					<p class="admin-db-kpi__value">{{ strtoupper($databaseInfo['driver']) }}</p>
				</article>
				<article class="admin-db-kpi">
					<span class="admin-db-kpi__label">{{ __('lexi.admin.words.database') }}</span>
					<p class="admin-db-kpi__value">{{ $databaseInfo['database'] ?: __('lexi.admin.words.na') }}</p>
				</article>
			</div>

			<div class="admin-db-state">
				<div class="admin-db-state__left">
					<span class="admin-db-state__title">{{ __('lexi.admin.words.state') }}</span>
					@if ($databaseInfo['is_mysql_like'])
						<span class="admin-status admin-status--active">{{ __('lexi.admin.words.mysql_active') }}</span>
					@else
						<span class="admin-status admin-status--review">{{ __('lexi.admin.words.sqlite_active') }}</span>
					@endif
				</div>
				<span class="admin-db-state__target">{{ __('lexi.admin.words.target') }}: {{ strtoupper($databaseInfo['target']) }}</span>
			</div>

		</div>
	</div>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.words.inventory_title') }}</h2>
				<p>{{ __('lexi.admin.words.inventory_text') }}</p>
			</div>
		</div>

		<form class="row g-3 mb-4" method="get" action="{{ route('admin-words') }}">
			<div class="col-md-4">
				<label class="form-label" for="wordSearch">{{ __('lexi.admin.words.search_word') }}</label>
				<input class="form-control" id="wordSearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('lexi.admin.words.search_placeholder') }}">
			</div>
			<div class="col-md-3">
				<label class="form-label" for="languageFilter">{{ __('lexi.admin.words.source_language') }}</label>
				<select class="form-select" id="languageFilter" name="language">
					<option value="">{{ __('lexi.admin.words.all') }}</option>
					@foreach ($languages as $language)
						<option value="{{ $language->code }}" @selected($filters['language'] === $language->code)>{{ $language->name }} ({{ strtoupper($language->code) }})</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-5 d-flex align-items-end gap-2">
				<button class="admin-btn admin-btn--primary w-100" type="submit"><i class="bi bi-search"></i> {{ __('lexi.admin.words.filter') }}</button>
			</div>
		</form>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>{{ __('lexi.admin.words.table_id') }}</th>
						<th>{{ __('lexi.admin.words.table_client_key') }}</th>
						<th>{{ __('lexi.admin.words.table_word') }}</th>
						<th>{{ __('lexi.admin.words.language') }}</th>
						<th>{{ __('lexi.admin.words.table_category') }}</th>
						<th>{{ __('lexi.admin.words.table_level') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($words as $word)
						<tr>
							<td>{{ $word->id }}</td>
							<td>{{ $word->client_key ?: __('lexi.admin.words.na') }}</td>
							<td>{{ $word->text }}</td>
							<td>{{ strtoupper($word->language_code) }}</td>
							<td>{{ $word->category?->name ?: __('lexi.admin.words.no_category') }}</td>
							<td>{{ $word->cefr_level ?: __('lexi.admin.words.na') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="6">{{ __('lexi.admin.words.no_rows') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($words->hasPages())
			<div class="d-flex justify-content-between align-items-center pt-3">
				<p class="mb-0 text-muted small">{{ __('lexi.admin.words.showing_rows', ['from' => $words->firstItem(), 'to' => $words->lastItem(), 'total' => $words->total()]) }}</p>
				<div>{{ $words->onEachSide(1)->links() }}</div>
			</div>
		@endif
	</div>
</section>
@endsection