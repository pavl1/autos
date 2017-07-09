<ul class="models">
    @foreach ($car->groups as $group)
        <li class="model-item">
            <img src="{{ $car->image }}{{ $group->hg }}.png" alt="">
            <a class="model-link" href="/subgroups/?cat={{ $catalog }}&mark={{ $oid->mark }}&market={{ $oid->market}}&model={{ $oid->model }}&production_year={{ $oid->production_year }}&code={{ $oid->code }}&type=G&group={{ $group->hg }}">
                {{ $group->text }}<br />
            </a>
        </li>
    @endforeach
</ul>
