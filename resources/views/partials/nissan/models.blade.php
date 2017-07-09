@foreach ($car->markets as $key => $market)
<h3>{{ $market->name }}</h3>
<ul class="models">
    @foreach ($market->models as $model)
        <li class="model-item">
            <a class="model-link" href="{{ $car->url }}&model={{ $model->series }}&market={{ $key }}">
                <span>{{ $model->model }}</span><br />
                <span>{{ $model->series }}</span><br />
                <span>{{ $model->date }}</span>
            </a>
        </li>
    @endforeach
</ul>
@endforeach
