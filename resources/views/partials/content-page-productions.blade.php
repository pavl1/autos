<h2>{{ $oid->mark }}</h2>

<ul class="models">
    @if ($catalog == 'fiat')
        @foreach ($car->productions as $production)
            <li class="model-item">
                <a class="model-link" href="/groups/?cat={{ $catalog }}&mark={{ $oid->mark }}&model={{ $oid->model }}&production={{ $production->cat_cod }}">
                    <span>{{ $production->cat_dsc }}</span>
                </a>
            </li>
        @endforeach
    @endif
    @if ($catalog == 'etka')
        @foreach ($car->productions as $production)
            <li class="model-item">
                <a class="model-link" href="/groups/?cat={{ $catalog }}&mark={{ $oid->mark }}&market={{ $oid->market }}&model={{ $oid->model }}&production_year={{ $production->einsatz }}&production={{ $production->epis_typ }}&dir={{ $oid->dir }}">
                    {{ $production->einsatz }}<br />
                </a>
            </li>
        @endforeach
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
        @foreach ($models as $model)
            <li class="model-item">
                <a class="model-link" href="/equipments/?cat=td&markID={{ $markID }}&modelID={{ $model->mod_id }}">
                    <span>{{ $model->mod_name }}</span>
                </a>
            </li>
        @endforeach
    @endif
</ul>



@php(the_content())
{!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
