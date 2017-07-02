<ul>
@foreach ($items as $item)
    <li class="tree-item">
        <a class="equipment-link" href="/details/?cat=td&markID={{ $markID }}&modelID={{ $modelID }}&equipmentID={{ $equipmentID }}&treeID={{ $item->str_id }}">
            <span>{{ $item->str_des }}</span>
        </a>
        @if (isset($item->childrens))
            @include('helpers.list', array('items' => $item->childrens))
        @endif
    </li>
@endforeach
</ul>
