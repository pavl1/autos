<ul class="models">
    @foreach ($car->groups as $group)
        <li class="model-item">
            <img src="{{ $car->image }}{{ $group->hg }}.png" alt="">
            <a class="model-link" href="/subgroups/?cat={{ $catalog }}&mark={{ $id->mark }}&market={{ $id->market}}&model={{ $id->model }}&production_year={{ $id->production_year }}&code={{ $id->code }}&type=G&group={{ $group->hg }}">
                {{ $group->text }}<br />
            </a>
        </li>
    @endforeach
</ul>
