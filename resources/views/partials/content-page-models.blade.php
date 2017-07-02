<h2>{{ $oid->mark }}</h2>

@if ($catalog == 'fiat')
    <ul class="models">
        @foreach ($car->models as $item)
            <li class="model-item">
                <a class="model-link" href="/productions/?cat={{ $catalog }}&mark={{ $oid->mark }}&model={{ $item->cmg_cod }}">
                    <img src="{{ $item->img }}" alt="">
                    <span>{{ $item->name }}</span>
                </a>
            </li>
        @endforeach
    <ul>
@endif
@if ($catalog == 'etka')
    <ul class="models-etka">
        @foreach ($car->markets as $market)
            <li>
                <h3>{{ $market->ru }}</h3>
                <ul class="models">
                    @foreach ($car->models[$market->code] as $model)
                        <li class="model-item">
                            <a class="model-link" href="/production/?cat={{ $catalog }}&mark={{ $oid->mark }}&market={{ $market->code }}&model={{ $model->modell }}">
                                {{ $model->bezeichnung }}<br />
                                {{ $model->einsatz }} - @if( $model->auslauf > 0) {{ $model->auslauf }} @endif <br />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
@endif
@if ($catalog == 'bmw')
    @foreach ($models as $model)
        <li class="model-item">
            <a class="model-link" href="{{ $model->Baureihe }}">
                <img src="{{ $model->imgUrl }}" alt="">
                <span>{{ $model->ExtBaureihe }}</span>
            </a>
        </li>
    @endforeach
@endif
@if ($catalog == 'nissan')
    @foreach ($models as $model)
        <li class="model-item">
            <a class="model-link" href="{{ $model->seies }}">
                <span>{{ $model->model }}</span>
                <span>{{ $model->series }}</span>
            </a>
        </li>
    @endforeach
@endif
@if ($catalog == 'toyota')
    @foreach ($models as $model)
        <li class="model-item">
            <a class="model-link" href="{{ $model->modelCode }}">
                <span>{{ $model->modelName }}</span>
                <span>{{ $model->modifications }}</span>
                <span>{{ $model->prodaction }}</span>
            </a>
        </li>
    @endforeach
@endif
@if ($catalog == 'adc')
    @foreach ($models as $model)
        <li class="model-item">
            <a class="model-link" href="{{ $model->model_id }}">
                <img src="{{ $model->model_url }}" alt="">
                <span>{{ $model->model_name }}</span>
            </a>
        </li>
    @endforeach
@endif
@if ($catalog == 'td')
    <ul class="models">
    @foreach ($car->models as $model)
        <li class="model-item">
            <a class="model-link" href="/equipments/?cat=td&mark={{ $id->mark }}&model={{ $model->mod_id }}">
                <span>{{ $model->mod_name }}</span>
            </a>
        </li>
    @endforeach
</ul>
@endif

@php(the_content())
{!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
