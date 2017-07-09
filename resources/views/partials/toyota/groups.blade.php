@php
    $part = array(
        1 => "Двигатель, топливная система и инструменты",
        2 => "Трансмиссия и шасси",
        3 => "Кузов и салон",
        4 => "Электрика"
    );
@endphp
<ul>
    @foreach ($car->groups as $key => $groups)
        <li>{{ $part[$key] }}
            <ul class="models">
                @foreach ($groups as $group)
                    <li class="model-item">
                        <a class="model-link" href="{{ $car->url }}&group={{ $group->part_group }}&graphic={{ $group->pic_code }}{{ $car->getString }}">
                            <img src="{{ $group->imgUrl }}" alt="">
                            <span>{{ $group->desc_en }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>
