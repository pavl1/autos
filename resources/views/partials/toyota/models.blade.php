@foreach ($car->markets as $key => $market)
    <h3>{{ $market->name }}</h3>
    <ul class="models">
        @foreach ($market->models as $model)
            <li class="model-item">
                <a class="model-link" href="{{ $car->url }}&model={{ $model->modelCode }}&market={{ $key }}">
                    <span>{{ $model->modelName }}</span><br />
                    <span>{{ $model->modifications }}</span><br />
                    <span>{{ $model->prodaction }}</span>
                </a>
            </li>
        @endforeach
    </ul>
@endforeach
