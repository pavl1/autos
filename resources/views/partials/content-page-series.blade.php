<h2>{{ $oid->mark }}</h2>

@if ($catalog == 'bmw')
    @include('partials.series.bmw')
@endif

@php(the_content())
{!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
