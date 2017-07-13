<ul>
@foreach ($items as $item)
    <li class="tree-item">
        <a class="equipment-link" href="{{ $car->url }}&tree={{ $item->id }}">
            <span>{{ $item->tree_name }}</span>
        </a>
        @if (isset($item->childrens))
            @include('partials.adc.helper-list', array('items' => $item->childrens))
        @endif
    </li>
@endforeach
</ul>
