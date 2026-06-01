@extends('layouts.admin', ['title' => __('lexi.admin.exercises.meta_title'), 'description' => __('lexi.admin.exercises.meta_description'), 'sidebarNoteTitle' => __('lexi.admin.exercises.sidebar_title'), 'sidebarNoteText' => __('lexi.admin.exercises.sidebar_text')])

@section('content')
@php
	$typeLabels = [
		'reading' => __('lexi.admin.exercises.type_reading'),
		'listening' => __('lexi.admin.exercises.type_listening'),
		'speaking' => __('lexi.admin.exercises.type_speaking'),
		'writing' => __('lexi.admin.exercises.type_writing'),
		'flashcards' => 'tarjetas',
		'matching' => 'emparejar',
		'mix' => __('lexi.admin.exercises.type_mix'),
	];
	$sourceLabels = [
		'manual' => __('lexi.admin.exercises.source_manual'),
		'ai' => __('lexi.admin.exercises.source_ai'),
		'catalog' => 'catálogo',
		'saved' => 'tus listas',
	];
	$resultStatusLabels = [
		'completed' => 'completado',
		'draft' => 'borrador',
		'started' => 'en curso',
		'failed' => 'fallido',
		'abandoned' => 'abandonado',
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
				<h2>Intentos recientes de runtime</h2>
				<p>Seguimiento operativo de las últimas sesiones de ejercicios (sin edición).</p>
			</div>
		</div>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Ejercicio</th>
						<th>Usuario</th>
						<th>Origen</th>
						<th>Resultado</th>
						<th>Score</th>
						<th>Respuestas</th>
						<th>Tiempo</th>
						<th>Completado</th>
					</tr>
				</thead>
				<tbody>
					@forelse($recentAttempts as $attempt)
						@php
							$attemptUser = trim(($attempt->user_name ?? '') . ' ' . ($attempt->user_surname ?? '')) ?: ($attempt->user_email ?: __('lexi.admin.analytics.deleted_user'));
							$sourceType = $attempt->source_type ? ($sourceLabels[$attempt->source_type] ?? $attempt->source_type) : __('lexi.admin.exercises.na');
							$sourceName = $attempt->source_name ?: '—';
							$timeSpent = $attempt->time_spent_seconds !== null ? $attempt->time_spent_seconds . 's' : __('lexi.admin.exercises.na');
							$mode = $typeLabels[$attempt->exercise_type] ?? $attempt->exercise_type;
							$resultStatus = $resultStatusLabels[$attempt->result_status] ?? ($attempt->result_status ?: __('lexi.admin.analytics.no_status'));
						@endphp
						<tr>
							<td>{{ $attempt->id }}</td>
							<td>
								<div>{{ $attempt->exercise_title ?: __('lexi.admin.exercises.untitled') }}</div>
								<small class="text-muted">{{ $mode }}</small>
							</td>
							<td>{{ $attemptUser }}</td>
							<td>
								<div>{{ $sourceType }}</div>
								<small class="text-muted">{{ $sourceName }}</small>
							</td>
							<td>{{ $resultStatus }}</td>
							<td>{{ $attempt->score !== null ? $attempt->score . '%' : __('lexi.admin.exercises.na') }}</td>
							<td>{{ number_format($attempt->answers_total) }} <span class="text-muted">/ {{ number_format($attempt->correct_answers_total) }} {{ __('lexi.admin.exercises.correct_suffix') }}</span></td>
							<td>{{ $timeSpent }}</td>
							<td>{{ $attempt->completed_at ? \Illuminate\Support\Carbon::parse($attempt->completed_at)->format('d/m/Y H:i') : __('lexi.admin.analytics.pending') }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="9">No hay intentos recientes registrados.</td>
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
				<h2>{{ __('lexi.admin.exercises.builder_title') }}</h2>
				<p>La administración de plantillas/items de ejercicios queda en modo lectura para evitar desalineación con el runtime actual.</p>
			</div>
		</div>
		<div class="alert alert-info mb-0" role="status">
			<div><strong>Modo visor activo.</strong> Esta sección muestra solo lectura para ejercicios, plantillas e items.</div>
			<div class="mt-2">Runtime actual cubierto: fuentes <strong>catálogo/tus listas</strong>, modos <strong>lectura, escucha, habla, escritura, tarjetas, emparejar y desafío</strong>, y registro de intentos con respuestas detalladas.</div>
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
							$exercisePayload = json_decode((string) ($exercise->payload ?? ''), true);
							$runtimeSourceType = is_array($exercisePayload) ? ($exercisePayload['source_type'] ?? null) : null;
							$runtimeSourceName = is_array($exercisePayload) ? ($exercisePayload['source_name'] ?? null) : null;
							$accuracyRate = $exercise->answers_total > 0
								? (int) round(($exercise->correct_answers_total / $exercise->answers_total) * 100)
								: null;
						@endphp
						<tr>
							<td>{{ $exercise->id }}</td>
							<td>{{ $exercise->title ?: __('lexi.admin.exercises.untitled') }}</td>
							<td>{{ $typeLabels[$exercise->type] ?? $exercise->type }}</td>
							<td>
								<div>{{ $sourceLabels[$exercise->source ?: 'manual'] ?? ($exercise->source ?: 'manual') }}</div>
								@if($runtimeSourceType)
									<small class="text-muted">{{ $sourceLabels[$runtimeSourceType] ?? $runtimeSourceType }} · {{ $runtimeSourceName ?: '—' }}</small>
								@endif
							</td>
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
						<th>Contexto</th>
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
								<div><strong>tema:</strong> {{ is_array($templatePayload) ? ($templatePayload['topic'] ?? '—') : '—' }}</div>
								<div><strong>dificultad:</strong> {{ is_array($templatePayload) ? ($templatePayload['difficulty'] ?? '—') : '—' }}</div>
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
				<p>Items de plantilla en modo lectura para auditoría de contenido y estructura.</p>
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
						<th>Detalle</th>
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
								<div><strong>pista:</strong> {{ $item->hint ?: '—' }}</div>
								<div><strong>mín. palabras:</strong> {{ $item->min_words ?? '—' }}</div>
								<div><strong>opción correcta:</strong> {{ $item->correct_option_order ?? '—' }}</div>
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