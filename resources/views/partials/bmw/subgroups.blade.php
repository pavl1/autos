@include('helpers.info.bmw')

<ul class="models">
    @foreach( $car->subgroups AS $subgroup )
        <li class="model-item">
            <a href="{{ $car->url }}&graphic={{ $subgroup->code}}" class="fl defBorder">
                <img src="{{ $subgroup->imgUrl }}"><br/>
                <span>{{ $subgroup->name }}</span>
            </a>
        </li>
    @endforeach
</ul>
