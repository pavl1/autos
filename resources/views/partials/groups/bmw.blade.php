@include('helpers.info.bmw')

@foreach( $car->groups AS $v )
    <a href="{{ $car->url }}&group={{ $v->code}}" class="fl defBorder">
        <img src="{{ $v->imgUrl }}"><br/>
        <span>{{ $v->name }}</span>
    </a>
@endforeach
