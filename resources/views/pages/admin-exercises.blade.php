@extends('layouts.admin', ['title' => __('lexi.admin.exercises.meta_title'), 'description' => __('lexi.admin.exercises.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.exercises.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.exercises.sidebar_text')])

@section('content')
@php
	$typeLabels = [
		'reading' => __('lexi.admin.exercises.type_reading'),
		'listening' => __('lexi.admin.exercises.type_listening'),
		'speaking' => __('lexi.admin.exercises.type_speaking'),
		'writing' => __('lexi.admin.exercises.type_writing'),
		'mix' => __('lexi.admin.exercises.type_mix'),
	];
	$sourceLabels = [
		'manual' => __('lexi.admin.exercises.source_manual'),
		'ai' => __('lexi.admin.exercises.source_ai'),
	];
	$itemTypeLabels = [
		'question' => __('lexi.admin.exercises.item_type_question'),
		'prompt' => __('lexi.admin.exercises.item_type_prompt'),
		'instruction' => __('lexi.admin.exercises.item_type_instruction'),
		'choice' => __('lexi.admin.exercises.item_type_choice'),
		'translate' => __('lexi.admin.exercises.item_type_translate'),
		'fillin' => __('lexi.admin.exercises.item_type_fillin'),
		'pronounce' => __('lexi.admin.exercises.item_type_pronounce'),
	];
@endphp
<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.exercises.heading') }}</h1>
		<p>{{ __('lexi.admin.exercises.intro') }}</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.exercises.back_panel') }}</a>
	</div>
</section>

@if (session('status'))
	<section class="admin-card">
		<div class="admin-card__inner">
			<div class="alert alert-success mb-0" role="status">{{ session('status') }}</div>
		</div>
	</section>
@endif

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.exercises.published') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-ui-checks-grid"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['published']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.exercises.published_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-play-circle"></i> {{ __('lexi.admin.exercises.published_meta') }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--amber">{{ __('lexi.admin.exercises.attempts') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-pencil-square"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['attempts']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.exercises.attempts_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-hourglass-split"></i> {{ number_format($stats['drafts']) }} {{ __('lexi.admin.exercises.drafts') }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">{{ __('lexi.admin.exercises.answers') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-list-check"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['answers']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.exercises.answers_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-bullseye"></i> {{ $stats['accuracy_rate'] !== null ? $stats['accuracy_rate'] . '%' : __('lexi.admin.exercises.no_accuracy_yet') }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">{{ __('lexi.admin.exercises.templates') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-diagram-3"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['templates']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.exercises.templates_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-list-ol"></i> {{ number_format($stats['template_items']) }} {{ __('lexi.admin.exercises.items') }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--amber">{{ __('lexi.admin.exercises.instances') }}</span>
			<div class="admin-stat__icon"><i class="bi bi-diagram-2"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['instances']) }}</p>
		<p class="admin-stat__label">{{ __('lexi.admin.exercises.instances_label') }}</p>
		<span class="admin-stat__meta"><i class="bi bi-person-workspace"></i> {{ __('lexi.admin.exercises.runtime_normalized') }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.exercises.builder_title') }}</h2>
				<p>{{ __('lexi.admin.exercises.builder_text') }}</p>
			</div>
		</div>

		<div class="row g-4">
			<div class="col-lg-5">
				<form class="row g-3" method="post" action="{{ route('admin-exercises.templates.store') }}">
					@csrf
					<div class="col-12">
						<label class="form-label" for="templateTitle">{{ __('lexi.admin.exercises.field_title') }}</label>
						<input class="form-control" id="templateTitle" type="text" name="title" maxlength="150" placeholder="{{ __('lexi.admin.exercises.placeholder_template_title') }}" required>
					</div>
					<div class="col-md-6">
						<label class="form-label" for="templateType">{{ __('lexi.admin.exercises.field_type') }}</label>
						<select class="form-select" id="templateType" name="type">
							<option value="reading">{{ $typeLabels['reading'] }}</option>
							<option value="listening">{{ $typeLabels['listening'] }}</option>
							<option value="speaking">{{ $typeLabels['speaking'] }}</option>
							<option value="writing">{{ $typeLabels['writing'] }}</option>
							<option value="mix">{{ $typeLabels['mix'] }}</option>
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label" for="templateSource">{{ __('lexi.admin.exercises.field_source') }}</label>
						<select class="form-select" id="templateSource" name="source">
							<option value="manual">{{ $sourceLabels['manual'] }}</option>
							<option value="ai">{{ $sourceLabels['ai'] }}</option>
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label" for="templateTopic">{{ __('lexi.admin.exercises.field_topic') }}</label>
						<input class="form-control" id="templateTopic" type="text" name="topic" maxlength="80" placeholder="{{ __('lexi.admin.exercises.placeholder_topic') }}">
					</div>
					<div class="col-md-3">
						<label class="form-label" for="templateDifficulty">{{ __('lexi.admin.exercises.field_level') }}</label>
						<input class="form-control" id="templateDifficulty" type="text" name="difficulty" maxlength="10" placeholder="A1">
					</div>
					<div class="col-md-3">
						<label class="form-label" for="templateVersion">{{ __('lexi.admin.exercises.field_version') }}</label>
						<input class="form-control" id="templateVersion" type="number" name="schema_version" min="1" max="99" value="1">
					</div>
					<div class="col-12">
						<button class="admin-btn admin-btn--primary" type="submit"><i class="bi bi-plus-circle"></i> {{ __('lexi.admin.exercises.create_template') }}</button>
					</div>
				</form>
			</div>

			<div class="col-lg-7">
				<form class="row g-3" method="post" action="{{ route('admin-exercises.items.store') }}">
					@csrf
					<div class="col-md-6">
						<label class="form-label" for="itemTemplate">{{ __('lexi.admin.exercises.field_template') }}</label>
						<select class="form-select" id="itemTemplate" name="template_id" required>
							<option value="">{{ __('lexi.admin.exercises.select_template') }}</option>
							@foreach ($templateOptions as $templateOption)
								<option value="{{ $templateOption->id }}">{{ $templateOption->title ?: __('lexi.admin.exercises.untitled') }} ({{ $typeLabels[$templateOption->type] ?? $templateOption->type }})</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label" for="itemOrder">{{ __('lexi.admin.exercises.field_order') }}</label>
						<input class="form-control" id="itemOrder" type="number" name="item_order" min="1" max="999" value="1" required>
					</div>
					<div class="col-md-3">
						<label class="form-label" for="itemType">{{ __('lexi.admin.exercises.field_item_type') }}</label>
						<select class="form-select" id="itemType" name="item_type">
							<option value="question">{{ $itemTypeLabels['question'] }}</option>
							<option value="prompt">{{ $itemTypeLabels['prompt'] }}</option>
							<option value="instruction">{{ $itemTypeLabels['instruction'] }}</option>
							<option value="choice">{{ $itemTypeLabels['choice'] }}</option>
							<option value="translate">{{ $itemTypeLabels['translate'] }}</option>
							<option value="fillin">{{ $itemTypeLabels['fillin'] }}</option>
							<option value="pronounce">{{ $itemTypeLabels['pronounce'] }}</option>
						</select>
					</div>
					<div class="col-12">
						<label class="form-label" for="itemQuestion">{{ __('lexi.admin.exercises.field_question') }}</label>
						<textarea class="form-control" id="itemQuestion" name="question_text" rows="3" maxlength="2000" placeholder="{{ __('lexi.admin.exercises.placeholder_question') }}" required></textarea>
					</div>
					<div class="col-md-6">
						<label class="form-label" for="itemCorrectAnswer">{{ __('lexi.admin.exercises.field_correct_answer') }}</label>
						<input class="form-control" id="itemCorrectAnswer" type="text" name="correct_answer" maxlength="4000" placeholder="{{ __('lexi.admin.exercises.placeholder_correct_answer') }}">
					</div>
					<div class="col-md-3">
						<label class="form-label" for="itemHint">{{ __('lexi.admin.exercises.field_hint') }}</label>
						<input class="form-control" id="itemHint" type="text" name="hint" maxlength="500" placeholder="{{ __('lexi.admin.exercises.placeholder_hint') }}">
					</div>
					<div class="col-md-3">
						<label class="form-label" for="itemMinWords">{{ __('lexi.admin.exercises.field_min_words') }}</label>
						<input class="form-control" id="itemMinWords" type="number" name="min_words" min="0" max="500">
					</div>
					<div class="col-md-9">
						<label class="form-label" for="itemOptions">{{ __('lexi.admin.exercises.field_options') }}</label>
						<textarea class="form-control" id="itemOptions" name="options_text" rows="4" maxlength="4000" placeholder="{{ __('lexi.admin.exercises.placeholder_options') }}"></textarea>
					</div>
					<div class="col-md-3">
						<label class="form-label" for="itemCorrectOptionOrder">{{ __('lexi.admin.exercises.field_correct_option') }}</label>
						<input class="form-control" id="itemCorrectOptionOrder" type="number" name="correct_option_order" min="1" max="50" placeholder="1">
					</div>
					<div class="col-12">
						<button class="admin-btn admin-btn--primary" type="submit"><i class="bi bi-node-plus"></i> {{ __('lexi.admin.exercises.add_item') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.exercises.catalog_title') }}</h2>
				<p>{{ __('lexi.admin.exercises.catalog_text') }}</p>
			</div>
		</div>

		<form class="row g-3 mb-4" method="get" action="{{ route('admin-exercises') }}">
			<div class="col-md-10">
				<label class="form-label" for="exerciseSearch">{{ __('lexi.admin.exercises.search_exercise') }}</label>
				<input class="form-control" id="exerciseSearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('lexi.admin.exercises.search_placeholder') }}">
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button class="admin-btn admin-btn--primary w-100" type="submit"><i class="bi bi-search"></i> {{ __('lexi.admin.exercises.filter') }}</button>
			</div>
		</form>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>{{ __('lexi.admin.exercises.table_title') }}</th>
						<th>{{ __('lexi.admin.exercises.table_type') }}</th>
						<th>{{ __('lexi.admin.exercises.table_source') }}</th>
						<th>{{ __('lexi.admin.exercises.table_status') }}</th>
						<th>{{ __('lexi.admin.exercises.table_attempts') }}</th>
						<th>{{ __('lexi.admin.exercises.table_answers') }}</th>
						<th>{{ __('lexi.admin.exercises.table_accuracy') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse($exercises as $exercise)
						@php
							$accuracyRate = $exercise->answers_total > 0
								? (int) round(($exercise->correct_answers_total / $exercise->answers_total) * 100)
								: null;
						@endphp
						<tr>
							<td>{{ $exercise->id }}</td>
							<td>{{ $exercise->title ?: __('lexi.admin.exercises.untitled') }}</td>
							<td>{{ $typeLabels[$exercise->type] ?? $exercise->type }}</td>
							<td>{{ $sourceLabels[$exercise->source ?: 'manual'] ?? ($exercise->source ?: 'manual') }}</td>
							<td>
								<span class="admin-status {{ $exercise->attempts_total > 0 ? 'admin-status--active' : 'admin-status--review' }}">
									{{ $exercise->attempts_total > 0 ? __('lexi.admin.exercises.with_usage') : __('lexi.admin.exercises.without_attempts') }}
								</span>
							</td>
							<td>{{ number_format($exercise->attempts_total) }}</td>
							<td>{{ number_format($exercise->answers_total) }} <span class="text-muted">/ {{ number_format($exercise->correct_answers_total) }} {{ __('lexi.admin.exercises.correct_suffix') }}</span></td>
							<td>{{ $accuracyRate !== null ? $accuracyRate . '%' : __('lexi.admin.exercises.na') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="8">{{ __('lexi.admin.exercises.no_exercises_filter') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($exercises->hasPages())
			<div class="d-flex justify-content-between align-items-center pt-3">
				<p class="mb-0 text-muted small">{{ __('lexi.admin.exercises.showing_rows', ['from' => $exercises->firstItem(), 'to' => $exercises->lastItem(), 'total' => $exercises->total()]) }}</p>
				<div>{{ $exercises->onEachSide(1)->links() }}</div>
			</div>
		@endif
	</div>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.exercises.inventory_title') }}</h2>
				<p>{{ __('lexi.admin.exercises.inventory_text') }}</p>
			</div>
		</div>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>{{ __('lexi.admin.exercises.table_template') }}</th>
						<th>{{ __('lexi.admin.exercises.table_type') }}</th>
						<th>{{ __('lexi.admin.exercises.table_source') }}</th>
						<th>{{ __('lexi.admin.exercises.table_version') }}</th>
						<th>{{ __('lexi.admin.exercises.table_items') }}</th>
						<th>{{ __('lexi.admin.exercises.table_options') }}</th>
						<th>{{ __('lexi.admin.exercises.instances') }}</th>
						<th>{{ __('lexi.admin.exercises.table_author') }}</th>
						<th>{{ __('lexi.admin.exercises.table_manage') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse($templates as $template)
						@php
							$author = trim(($template->author_name ?? '') . ' ' . ($template->author_surname ?? '')) ?: __('lexi.admin.exercises.system');
							$templatePayload = json_decode((string) ($template->payload ?? ''), true);
						@endphp
						<tr>
							<td>{{ $template->id }}</td>
							<td>{{ $template->title ?: __('lexi.admin.exercises.untitled') }}</td>
							<td>{{ $typeLabels[$template->type] ?? $template->type }}</td>
							<td>{{ $sourceLabels[$template->source ?: 'manual'] ?? ($template->source ?: 'manual') }}</td>
							<td>v{{ $template->schema_version }}</td>
							<td>{{ number_format($template->items_total) }}</td>
							<td>{{ number_format($template->options_total) }}</td>
							<td>{{ number_format($template->instances_total) }}</td>
							<td>{{ $author }}</td>
							<td>
								<form class="row g-2 mb-2" method="post" action="{{ route('admin-exercises.templates.update', $template->id) }}">
									@csrf
									@method('PATCH')
									<div class="col-12"><input class="form-control form-control-sm" type="text" name="title" value="{{ $template->title }}" maxlength="150" required></div>
									<div class="col-6"><select class="form-select form-select-sm" name="type"><option value="reading" @selected($template->type === 'reading')>{{ $typeLabels['reading'] }}</option><option value="listening" @selected($template->type === 'listening')>{{ $typeLabels['listening'] }}</option><option value="speaking" @selected($template->type === 'speaking')>{{ $typeLabels['speaking'] }}</option><option value="writing" @selected($template->type === 'writing')>{{ $typeLabels['writing'] }}</option><option value="mix" @selected($template->type === 'mix')>{{ $typeLabels['mix'] }}</option></select></div>
									<div class="col-6"><select class="form-select form-select-sm" name="source"><option value="manual" @selected(($template->source ?? 'manual') === 'manual')>{{ $sourceLabels['manual'] }}</option><option value="ai" @selected($template->source === 'ai')>{{ $sourceLabels['ai'] }}</option></select></div>
									<div class="col-4"><input class="form-control form-control-sm" type="number" name="schema_version" min="1" max="99" value="{{ $template->schema_version }}"></div>
									<div class="col-4"><input class="form-control form-control-sm" type="text" name="topic" maxlength="80" value="{{ is_array($templatePayload) ? ($templatePayload['topic'] ?? '') : '' }}" placeholder="{{ __('lexi.admin.exercises.placeholder_topic_short') }}"></div>
									<div class="col-4"><input class="form-control form-control-sm" type="text" name="difficulty" maxlength="10" value="{{ is_array($templatePayload) ? ($templatePayload['difficulty'] ?? '') : '' }}" placeholder="A1"></div>
									<div class="col-12 d-flex gap-2"><button class="admin-btn admin-btn--primary btn-sm" type="submit">{{ __('lexi.admin.exercises.save') }}</button></div>
								</form>
								<form method="post" action="{{ route('admin-exercises.templates.destroy', $template->id) }}">
									@csrf
									@method('DELETE')
									<button class="admin-btn admin-btn--ghost btn-sm" type="submit">{{ __('lexi.admin.exercises.delete') }}</button>
								</form>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="10">{{ __('lexi.admin.exercises.no_templates_yet') }}</td>
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
				<h2>{{ __('lexi.admin.exercises.items_title') }}</h2>
				<p>{{ __('lexi.admin.exercises.items_text') }}</p>
			</div>
		</div>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>{{ __('lexi.admin.exercises.table_template') }}</th>
						<th>{{ __('lexi.admin.exercises.table_order') }}</th>
						<th>{{ __('lexi.admin.exercises.table_type') }}</th>
						<th>{{ __('lexi.admin.exercises.table_question') }}</th>
						<th>{{ __('lexi.admin.exercises.table_answer') }}</th>
						<th>{{ __('lexi.admin.exercises.table_options') }}</th>
						<th>{{ __('lexi.admin.exercises.table_manage') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($templateItems as $item)
						<tr>
							<td>{{ $item->id }}</td>
							<td>{{ $item->template_title ?: __('lexi.admin.exercises.untitled') }}</td>
							<td>{{ $item->item_order }}</td>
							<td>{{ $itemTypeLabels[$item->item_type] ?? $item->item_type }}</td>
							<td>{{ \Illuminate\Support\Str::limit($item->question_text ?: __('lexi.admin.exercises.no_question'), 90) }}</td>
							<td>{{ \Illuminate\Support\Str::limit($item->correct_answer ?: __('lexi.admin.exercises.na'), 50) }}</td>
							<td>{{ number_format($item->options_total) }}</td>
							<td>
								<form class="row g-2 mb-2" method="post" action="{{ route('admin-exercises.items.update', $item->id) }}">
									@csrf
									@method('PATCH')
									<div class="col-12"><select class="form-select form-select-sm" name="template_id" required>@foreach ($templateOptions as $templateOption)<option value="{{ $templateOption->id }}" @selected($templateOption->id === $item->template_id)>{{ $templateOption->title ?: __('lexi.admin.exercises.untitled') }} ({{ $typeLabels[$templateOption->type] ?? $templateOption->type }})</option>@endforeach</select></div>
									<div class="col-4"><input class="form-control form-control-sm" type="number" name="item_order" min="1" max="999" value="{{ $item->item_order }}" required></div>
									<div class="col-8"><select class="form-select form-select-sm" name="item_type"><option value="question" @selected($item->item_type === 'question')>{{ $itemTypeLabels['question'] }}</option><option value="prompt" @selected($item->item_type === 'prompt')>{{ $itemTypeLabels['prompt'] }}</option><option value="instruction" @selected($item->item_type === 'instruction')>{{ $itemTypeLabels['instruction'] }}</option><option value="choice" @selected($item->item_type === 'choice')>{{ $itemTypeLabels['choice'] }}</option><option value="translate" @selected($item->item_type === 'translate')>{{ $itemTypeLabels['translate'] }}</option><option value="fillin" @selected($item->item_type === 'fillin')>{{ $itemTypeLabels['fillin'] }}</option><option value="pronounce" @selected($item->item_type === 'pronounce')>{{ $itemTypeLabels['pronounce'] }}</option></select></div>
									<div class="col-12"><textarea class="form-control form-control-sm" name="question_text" rows="2" maxlength="2000" required>{{ $item->question_text }}</textarea></div>
									<div class="col-6"><input class="form-control form-control-sm" type="text" name="correct_answer" maxlength="4000" value="{{ $item->correct_answer }}" placeholder="{{ __('lexi.admin.exercises.field_correct_answer') }}"></div>
									<div class="col-3"><input class="form-control form-control-sm" type="text" name="hint" maxlength="500" value="{{ $item->hint }}" placeholder="{{ __('lexi.admin.exercises.field_hint') }}"></div>
									<div class="col-3"><input class="form-control form-control-sm" type="number" name="min_words" min="0" max="500" value="{{ $item->min_words }}" placeholder="{{ __('lexi.admin.exercises.field_min_words') }}"></div>
									<div class="col-9"><textarea class="form-control form-control-sm" name="options_text" rows="3" maxlength="4000" placeholder="{{ __('lexi.admin.exercises.one_option_per_line') }}">{{ $item->options_text }}</textarea></div>
									<div class="col-3"><input class="form-control form-control-sm" type="number" name="correct_option_order" min="1" max="50" placeholder="{{ __('lexi.admin.exercises.correct_short') }}" value="{{ $item->correct_option_order }}"></div>
									<div class="col-12"><button class="admin-btn admin-btn--primary btn-sm" type="submit">{{ __('lexi.admin.exercises.save') }}</button></div>
								</form>
								<form method="post" action="{{ route('admin-exercises.items.destroy', $item->id) }}">
									@csrf
									@method('DELETE')
									<button class="admin-btn admin-btn--ghost btn-sm" type="submit">{{ __('lexi.admin.exercises.delete') }}</button>
								</form>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="8">{{ __('lexi.admin.exercises.no_items_yet') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</section>

<section class="admin-card" id="instances">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>{{ __('lexi.admin.exercises.generated_title') }}</h2>
				<p>{{ __('lexi.admin.exercises.generated_text') }}</p>
			</div>
		</div>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>{{ __('lexi.admin.exercises.table_template') }}</th>
						<th>{{ __('lexi.admin.exercises.table_type') }}</th>
						<th>{{ __('lexi.admin.exercises.table_user') }}</th>
						<th>{{ __('lexi.admin.exercises.table_language') }}</th>
						<th>{{ __('lexi.admin.exercises.table_payload') }}</th>
						<th>{{ __('lexi.admin.exercises.table_created') }}</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($templateInstances as $instance)
						@php
							$instanceUser = trim(($instance->name ?? '') . ' ' . ($instance->surname ?? '')) ?: $instance->email;
						@endphp
						<tr>
							<td>{{ $instance->id }}</td>
							<td>{{ $instance->template_title ?: __('lexi.admin.exercises.untitled') }}</td>
							<td>{{ $typeLabels[$instance->template_type] ?? $instance->template_type }}</td>
							<td>{{ $instanceUser }}</td>
							<td>{{ strtoupper($instance->assigned_language_code ?: __('lexi.admin.exercises.na')) }}</td>
							<td>{{ \Illuminate\Support\Str::limit($instance->payload_summary ?: __('lexi.admin.exercises.no_payload'), 120) }}</td>
							<td>{{ \Illuminate\Support\Carbon::parse($instance->created_at)->format('d/m/Y H:i') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="7">{{ __('lexi.admin.exercises.no_instances_yet') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection