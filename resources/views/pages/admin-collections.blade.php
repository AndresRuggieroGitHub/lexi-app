@extends('layouts.admin', ['title' => __('lexi.admin.collections.meta_title'), 'description' => __('lexi.admin.collections.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.collections.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.collections.sidebar_text')])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.collections.heading') }}</h1>
		<p>{{ __('lexi.admin.collections.intro') }}</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.collections.back_panel') }}</a>
	</div>
</section>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.collections.collections_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-collection"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['collections']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.collections.collections_rows') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-bookmark"></i> {{ __('lexi.admin.collections.default_collections', ['count' => number_format($stats['default_collections'])]) }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.collections.linked_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-link"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['linked_words']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.collections.linked_rows') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-diagram-3"></i> {{ __('lexi.admin.collections.collection_word_relations') }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.collections.library_title') }}</h2>
				<p>{{ __('lexi.admin.collections.library_text') }}</p>
			</div>
		</div>

		<form class="row g-3 mb-4" method="get" action="{{ route('admin-collections') }}">
			<div class="col-md-6">
				<label class="form-label" for="collectionSearch">{{ __('lexi.admin.collections.search_collection') }}</label>
				<input class="form-control" id="collectionSearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('lexi.admin.collections.search_placeholder') }}">
			</div>
			<div class="col-md-4">
				<label class="form-label" for="collectionLanguage">{{ __('lexi.admin.collections.language') }}</label>
				<select class="form-select" id="collectionLanguage" name="language">
					<option value="">{{ __('lexi.admin.collections.all') }}</option>
					@foreach ($languages as $language)
						<option value="{{ $language->code }}" @selected($filters['language'] === $language->code)>{{ $language->name }} ({{ strtoupper($language->code) }})</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button class="admin-btn admin-btn--primary w-100" type="submit"><i class="bi bi-search"></i> {{ __('lexi.admin.collections.filter') }}</button>
			</div>
		</form>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>{{ __('lexi.admin.collections.table_id') }}</th>
						<th>{{ __('lexi.admin.collections.table_name') }}</th>
						<th>{{ __('lexi.admin.collections.table_owner') }}</th>
						<th>{{ __('lexi.admin.collections.table_language') }}</th>
						<th>{{ __('lexi.admin.collections.table_words') }}</th>
						<th>{{ __('lexi.admin.collections.table_status') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($collections as $collection)
						@php
							$owner = trim(($collection->user?->name ?? '') . ' ' . ($collection->user?->surname ?? '')) ?: __('lexi.admin.collections.no_user');
						@endphp
						<tr>
							<td>{{ $collection->id }}</td>
							<td>{{ $collection->name }}</td>
							<td>{{ $owner }}</td>
							<td>{{ strtoupper($collection->language_code) }}</td>
							<td>{{ number_format($collection->words_count) }}</td>
							<td><span class="admin-status {{ $collection->is_default ? 'admin-status--active' : 'admin-status--review' }}">{{ $collection->is_default ? __('lexi.admin.collections.status_default') : __('lexi.admin.collections.status_custom') }}</span></td>
						</tr>
					@empty
						<tr>
							<td colspan="6">{{ __('lexi.admin.collections.no_collections_filter') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($collections->hasPages())
			<div class="d-flex justify-content-between align-items-center pt-3">
				<p class="mb-0 text-muted small">{{ __('lexi.admin.collections.showing_rows', ['from' => $collections->firstItem(), 'to' => $collections->lastItem(), 'total' => $collections->total()]) }}</p>
				<div>{{ $collections->onEachSide(1)->links() }}</div>
			</div>
		@endif
	</div>
</section>
@endsection