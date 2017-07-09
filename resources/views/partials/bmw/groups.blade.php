@include('helpers.info.bmw')

<ul class="models">
    @foreach( $car->groups AS $v )
        <li class="model-item">
            <a href="{{ $car->url }}&group={{ $v->code}}" class="fl defBorder">
                <img src="{{ $v->imgUrl }}"><br/>
                <span>{{ $v->name }}</span>
            </a>
        </li>
    @endforeach
</ul>
