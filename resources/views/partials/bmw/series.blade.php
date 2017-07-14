<script type="text/html" id="models-list">
    @verbatim
    @endverbatim
</script>

<div class="series">

    @foreach ($car->series as $series)
        <div class="series-container">
            <a class="series-link" href="#{{ $series->Baureihe }}" aria-expanded="false"
                data-toggle="collapse"
                data-catalog="{{ $catalog }}"
                data-oid="{{ json_encode($oid) }}"
                data-series="{{ $series->Baureihe}}"
            >
                <span class="col">{{ explode(' ', $series->ExtBaureihe)[0] }}</span>
                <span class="col">{{ explode(' ', $series->ExtBaureihe)[1] }}</span>
            </a>
            <div class="series-models collapse" id="{{ $series->Baureihe }}">
                Комплектация
                <img src="{{ $series->imgUrl }}" alt="">
            </div>
        </div>
    @endforeach
</div>
