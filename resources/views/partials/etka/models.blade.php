<ul class="models-etka">
    @foreach ($car->markets as $market)
        <li>
            <h3>{{ $market->ru }}</h3>
            <ul class="models">
                @foreach ($car->models[$market->code] as $model)
                    <li class="model-item">
                        <a class="model-link" href="/production/?cat={{ $oid->catalog }}&mark={{ $oid->mark }}&market={{ $market->code }}&model={{ $model->modell }}">
                            {{ $model->bezeichnung }}<br />
                            {{ $model->einsatz }} - @if( $model->auslauf > 0) {{ $model->auslauf }} @endif <br />
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>
