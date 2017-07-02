<h2>{{ $brand }}</h2>

<ul class="details">
    @if ($catalog == 'fiat')
        @foreach ($models as $model)
            <li class="model-item">
                <a class="model-link" href="#">
                    <img src="{{ $model->img }}" alt="">
                    <span>{{ $model->name }}</span>
                </a>
            </li>
        @endforeach
    @endif
    @if ($catalog == 'etka')
        @foreach ($models as $model)
            <li class="model-item">
                <a class="model-link" href="{{ $model->modell }}">
                    {{ $model->bezeichnung }}<br />
                    {{ $model->einsatz }} - @if( $model->auslauf > 0) {{ $model->auslauf }} @endif <br />
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
        <p>{{ $car->info->markName }} {{ $car->info->modelName }}</p>
        <h3>{{ $car->group->str_des }}</h3>
        <table>
            @foreach ($car->details as $item)
                <tr>
                    <td><img src="{{ $item->brandLogo }}" alt=""></td>
                    <td>{{ $item->art_article_nr }}</td>
                    <td>{{ $item->ga_des }}</td>
                    <td><a href="/search/{{ $item->art_article_nr }}">Цена</a></td>
                </tr>
            @endforeach
        </table>
    @endif
</ul>

@php(the_content())
{!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
