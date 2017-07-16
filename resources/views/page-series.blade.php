@extends('layouts.app')
@section('content')
    @while(have_posts()) @php(the_post())
        @if ($oid->catalog)
            @include("partials.$oid->catalog.series")
        @endif
    @endwhile
@endsection
