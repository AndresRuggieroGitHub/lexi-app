@extends('layouts.site', ['title' => __('lexi.meta.library.title'), 'description' => __('lexi.meta.library.description'), 'activeNav' => 'library', 'showFooter' => false])

@section('content')
@include('partials.library-content')
@endsection