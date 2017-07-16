<h3>Список моделей {{ $car->mark }} {{ $car->series }}</h3>
<ul class="models">
    @foreach ($car->models as $body)
        <li class="model-item">
            <img src="{{ $body->image }}" alt="">
            <span>{{ $body->name }}</span>
            <ul>
                @foreach($body->models as $code => $models)
                    <li>
                        {{ $models->MarketName }}
                        <ul>
                            @foreach ($models->ModelInfo as $model)
                                <li>
                                    <a href="/options/?cat={{ $oid->catalog }}&mark={{ $oid->mark }}&type={{ $oid->type }}&series={{ $oid->series }}&body={{ $body->code }}&model={{ $model->ModelID }}&market={{ $models->MarketCode }}">
                                        {{ $model->ModelCode }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>
