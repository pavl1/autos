@extends('layouts.app')
@section('content')
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @if ($catalog)
            @include("partials.$catalog.modifications")
        @endif
    @endwhile
@endsection