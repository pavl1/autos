<ul class="models">

@foreach ($car->subgroups as $subgroup)
    <li class="model-item">
        <img src="{{ $subgroup->img }}" alt="" style="max-width: 100%; height: 100px">
        <span>
            {{ $subgroup->tsbem_text }}
            {{ $subgroup->tsmoa_text }}
        </span>
        <a class="model-link" href="/illustration/?cat={{ $catalog }}&mark={{ $id->mark }}&market={{ $id->market }}&model={{ $id->model }}&production_year={{ $id->production_year }}&code={{ $id->code }}&group={{ $id->group }}&subgroup={{ $subgroup->hg_ug }}&graphic={{ $subgroup->bildtafel2 }}">
            {{ $subgroup->tsben_text }}
        </a>
    </li>
@endforeach
</ul>
