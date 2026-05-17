@extends('layouts.admin', ['title' => __('lexi.admin.dashboard.meta_title'), 'description' => __('lexi.admin.dashboard.meta_description')])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>{{ __('lexi.admin.dashboard.heading') }}</h1>
		<p>{{ __('lexi.admin.dashboard.intro') }}</p>
	</div>
</section>

<section class="admin-overview-grid">
	@foreach ($cards as $card)
	<article class="admin-card">
		<a class="admin-card__inner admin-entity-card admin-entity-card--selectable" href="{{ $card['href'] }}" aria-label="{{ __('lexi.admin.dashboard.open_aria', ['title' => $card['title']]) }}">
			<div class="admin-entity-card__icon"><i class="bi {{ $card['icon'] }}"></i></div>
			<div>
				<h3>{{ $card['title'] }}</h3>
				<p>{{ $card['description'] }}</p>
			</div>
			<div class="admin-entity-card__meta">
				<span>{{ $card['count'] }}</span>
				<span class="admin-status {{ $card['statusClass'] }}">{{ $card['status'] }}</span>
			</div>
			<div class="admin-entity-card__footer">
				<span class="admin-inline-link">{{ __('lexi.admin.dashboard.open_section') }}</span>
				<i class="bi bi-arrow-right-short admin-entity-card__arrow" aria-hidden="true"></i>
			</div>
		</a>
	</article>
	@endforeach
</section>
@endsection