<ul class="models">
    @foreach ($car->models as $model)
        <li class="model-item">
            <a class="model-link" href="{{ $car->url }}&model={{ $model->model_id }}">
                <span>{{ $model->model_name }}</span>
                <span>{{ $model->model_years }}</span>
            </a>
        </li>
    @endforeach
</ul>
