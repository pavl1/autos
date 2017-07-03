<ul class="models">
    @foreach ($car->series as $series)
        <li class="model-item">
            <a class="model-link" href="/models/?cat={{ $catalog }}&mark={{ $oid->mark }}&type={{ $oid->type }}&series={{ $series->Baureihe }}">
                <img src="{{ $series->imgUrl }}" alt="">
                <span>{{ $series->ExtBaureihe }}</span>
            </a>
        </li>
    @endforeach
</ul>
