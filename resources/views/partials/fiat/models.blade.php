<ul class="models">
    @foreach ($car->models as $item)
        <li class="model-item">
            <a class="model-link" href="/productions/?cat={{ $catalog }}&mark={{ $id->mark }}&model={{ $item->cmg_cod }}">
                <img src="{{ $item->img }}" alt="">
                <span>{{ $item->name }}</span>
            </a>
        </li>
    @endforeach
<ul>
