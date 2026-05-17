@extends('layouts.admin', ['title' => __('lexi.admin.translations.meta_title'), 'description' => __('lexi.admin.translations.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.translations.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.translations.sidebar_text')])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.translations.heading') }}</h1>
		<p>{{ __('lexi.admin.translations.intro') }}</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.translations.back_panel') }}</a>
	</div>
</section>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.translations.pairs_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-shuffle"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['pairs']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.translations.pairs_rows') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-link-45deg"></i> {{ __('lexi.admin.translations.source_target_relations') }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.translations.context_chip') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-chat-square-text"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['with_context']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.translations.with_context') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-check2-circle"></i> {{ __('lexi.admin.translations.without_context', ['count' => number_format($stats['without_context'])]) }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.translations.map_title') }}</h2>
				<p>{{ __('lexi.admin.translations.map_text') }}</p>
			</div>
		</div>

		<form class="row g-3 mb-4" method="get" action="{{ route('admin-translations') }}">
			<div class="col-md-4">
				<label class="form-label" for="translationSearch">{{ __('lexi.admin.translations.search') }}</label>
				<input class="form-control" id="translationSearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('lexi.admin.translations.search_placeholder') }}">
			</div>
			<div class="col-md-3">
				<label class="form-label" for="sourceLanguage">{{ __('lexi.admin.translations.source_language') }}</label>
				<select class="form-select" id="sourceLanguage" name="source_language">
					<option value="">{{ __('lexi.admin.translations.all') }}</option>
					@foreach ($languages as $language)
						<option value="{{ $language->code }}" @selected($filters['source_language'] === $language->code)>{{ $language->name }} ({{ strtoupper($language->code) }})</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label" for="targetLanguage">{{ __('lexi.admin.translations.target_language') }}</label>
				<select class="form-select" id="targetLanguage" name="target_language">
					<option value="">{{ __('lexi.admin.translations.all') }}</option>
					@foreach ($languages as $language)
						<option value="{{ $language->code }}" @selected($filters['target_language'] === $language->code)>{{ $language->name }} ({{ strtoupper($language->code) }})</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button class="admin-btn admin-btn--primary w-100" type="submit"><i class="bi bi-search"></i> {{ __('lexi.admin.translations.filter') }}</button>
			</div>
		</form>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>{{ __('lexi.admin.translations.table_id') }}</th>
						<th>{{ __('lexi.admin.translations.table_source') }}</th>
						<th>{{ __('lexi.admin.translations.table_target') }}</th>
						<th>{{ __('lexi.admin.translations.table_context') }}</th>
						<th>{{ __('lexi.admin.translations.table_source_category') }}</th>
						<th>{{ __('lexi.admin.translations.table_status') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($translations as $translation)
						@php
							$source = $translation->sourceWord;
							$target = $translation->targetWord;
							$hasContext = filled($translation->context_note);
							$statusClass = $hasContext ? 'admin-status--active' : 'admin-status--review';
							$statusLabel = $hasContext ? __('lexi.admin.translations.status_contextualized') : __('lexi.admin.translations.status_without_context');
						@endphp
						<tr>
							<td>{{ $translation->id }}</td>
							<td>{{ $source?->text ?: __('lexi.admin.translations.na') }} ({{ strtoupper($source?->language_code ?: '--') }})</td>
							<td>{{ $target?->text ?: __('lexi.admin.translations.na') }} ({{ strtoupper($target?->language_code ?: '--') }})</td>
							<td>{{ $translation->context_note ?: __('lexi.admin.translations.no_note') }}</td>
							<td>{{ $source?->category?->name ?: __('lexi.admin.translations.no_category') }}</td>
							<td><span class="admin-status {{ $statusClass }}">{{ $statusLabel }}</span></td>
						</tr>
					@empty
						<tr>
							<td colspan="6">{{ __('lexi.admin.translations.no_translations_filter') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($translations->hasPages())
			<div class="d-flex justify-content-between align-items-center pt-3">
				<p class="mb-0 text-muted small">{{ __('lexi.admin.translations.showing_rows', ['from' => $translations->firstItem(), 'to' => $translations->lastItem(), 'total' => $translations->total()]) }}</p>
				<div>{{ $translations->onEachSide(1)->links() }}</div>
			</div>
		@endif
	</div>
</section>
@endsection