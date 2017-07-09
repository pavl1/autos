<ul class="equipments">
    @foreach ($car->tree as $item)
        <li class="tree-item">
            <a class="equipment-link" href="/details/?cat=td&mark={{ $id->mark }}&model={{ $id->model }}&equipment={{ $id->equipment }}&tree={{ $item->str_id }}">
                <span>{{ $item->str_des }}</span>
            </a>
            @if (isset($item->childrens))
                @include('helpers.list', array('items' => $item->childrens))
            @endif
        </li>
    @endforeach
</ul>
