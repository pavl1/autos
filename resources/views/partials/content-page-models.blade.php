<h2>{{ $oid->mark }}</h2>

@if ($catalog == 'fiat')
    @include('partials.models.fiat')
@endif
@if ($catalog == 'etka')
    @include('partials.models.etka')
@endif
@if ($catalog == 'bmw')
    @include('partials.models.bmw')
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
