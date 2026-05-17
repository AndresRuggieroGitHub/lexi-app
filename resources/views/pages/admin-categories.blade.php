@extends('layouts.admin', ['title' => __('lexi.admin.categories.meta_title'), 'description' => __('lexi.admin.categories.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.categories.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.categories.sidebar_text')])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.categories.heading') }}</h1>
		<p>{{ __('lexi.admin.categories.intro') }}</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.categories.back_panel') }}</a>
	</div>
</section>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.categories.categories_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-tags"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['categories']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.categories.categories_rows') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-diagram-3"></i> {{ __('lexi.admin.categories.taxonomy_available') }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.categories.linked_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-link-45deg"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['linked_words']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.categories.categorized_words') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-check2-circle"></i> {{ __('lexi.admin.categories.empty_categories', ['count' => number_format($stats['empty_categories'])]) }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.categories.catalog_title') }}</h2>
				<p>{{ __('lexi.admin.categories.catalog_text') }}</p>
			</div>
		</div>

		<form class="row g-3 mb-4" method="get" action="{{ route('admin-categories') }}">
			<div class="col-md-6">
				<label class="form-label" for="categorySearch">{{ __('lexi.admin.categories.search_category') }}</label>
				<input class="form-control" id="categorySearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('lexi.admin.categories.search_placeholder') }}">
			</div>
			<div class="col-md-4">
				<label class="form-label" for="categoryLanguage">{{ __('lexi.admin.categories.language') }}</label>
				<select class="form-select" id="categoryLanguage" name="language">
					<option value="">{{ __('lexi.admin.categories.all') }}</option>
					@foreach ($languages as $language)
						<option value="{{ $language->code }}" @selected($filters['language'] === $language->code)>{{ $language->name }} ({{ strtoupper($language->code) }})</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button class="admin-btn admin-btn--primary w-100" type="submit"><i class="bi bi-search"></i> {{ __('lexi.admin.categories.filter') }}</button>
			</div>
		</form>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>{{ __('lexi.admin.categories.table_id') }}</th>
						<th>{{ __('lexi.admin.categories.table_name') }}</th>
						<th>{{ __('lexi.admin.categories.language') }}</th>
						<th>{{ __('lexi.admin.categories.table_description') }}</th>
						<th>{{ __('lexi.admin.categories.table_words') }}</th>
						<th>{{ __('lexi.admin.categories.table_status') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($categories as $category)
						@php
							$hasWords = $category->words_count > 0;
						@endphp
						<tr>
							<td>{{ $category->id }}</td>
							<td>{{ $category->name }}</td>
							<td>{{ strtoupper($category->language_code ?: '--') }}</td>
							<td>{{ $category->description ?: __('lexi.admin.categories.no_description') }}</td>
							<td>{{ number_format($category->words_count) }}</td>
							<td><span class="admin-status {{ $hasWords ? 'admin-status--active' : 'admin-status--draft' }}">{{ $hasWords ? __('lexi.admin.categories.status_in_use') : __('lexi.admin.categories.status_empty') }}</span></td>
						</tr>
					@empty
						<tr>
							<td colspan="6">{{ __('lexi.admin.categories.no_categories_filter') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($categories->hasPages())
			<div class="d-flex justify-content-between align-items-center pt-3">
				<p class="mb-0 text-muted small">{{ __('lexi.admin.categories.showing_rows', ['from' => $categories->firstItem(), 'to' => $categories->lastItem(), 'total' => $categories->total()]) }}</p>
				<div>{{ $categories->onEachSide(1)->links() }}</div>
			</div>
		@endif
	</div>
</section>
@endsection