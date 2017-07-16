@extends('layouts.app')
@section('content')
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @if ($oid->catalog)
            @include("partials.$oid->catalog.groups")
        @endif
    @endwhile
@endsection
