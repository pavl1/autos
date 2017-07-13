@foreach ($car->productions as $productions)

    @if( count($productions) == 1 )
        {{-- @php( $production = $productions ) --}}
        <a href="{{ $car->url }}&production_year={{ $productions->einsatz }}&code={{ $productions->epis_typ }}&dir={{ $id->dir }}">
            {{ $productions->einsatz }}
        </a>
    @else
        @php( $years = current($productions)->einsatz )

        @foreach ($productions as $production)
            <a href="{{ $car->url }}&production_year={{ $production->einsatz }}&code={{ $production->epis_typ }}&dir={{ $id->dir }}">
                {{ $production->bezeichnung }}
            </a>
        @endforeach
    @endif

@endforeach
