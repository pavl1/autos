<h2>{{ $oid->mark }}</h2>

@if ($catalog)
    @include("partials.subgroups.$catalog")
@endif

<ul class="models">
    @if ($catalog == 'fiat')
        <table>
            <thead>
                <tr>
                    <td>Группа</td>
                    <td>Картинка</td>
                    <td>Подгруппа</td>
                    <td>Применяемость</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($car->subgroups as $subgroup)
                    <tr>
                        <td rowspan="{{ count($subgroup->board) + 1 }}">
                            {{ $subgroup->subGroup->sgrp_dsc }}
                        </td>
                    </tr>
                    @foreach ($subgroup->board as $subitem)
                        <tr>
                            <td>
                                <img src="{{ $subitem->img_path }}" alt="" style="max-width: 100px">
                            </td>
                            <td>
                                <a href="/illustration/?cat={{ $catalog }}&mark={{ $oid->mark }}&model={{ $oid->model }}&production={{ $oid->production }}&group={{ $oid->group }}&subgroup={{ $subgroup->subGroup->sgrp_cod }}&table={{ urlencode(base64_encode($subitem->table_cod)) }}">
                                    {{ $subitem->dsc }}
                                </a>
                            </td>
                            <td>
                                <a href="/illustration/?cat={{ $catalog }}&mark={{ $oid->mark }}&model={{ $oid->model }}&production={{ $oid->production }}&group={{ $oid->group }}&subgroup={{ $subgroup->subGroup->sgrp_cod }}&table={{ urlencode(base64_encode($subitem->table_cod)) }}">
                                    {{ $subitem->pattern }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif
    @if ($catalog == 'etka')
        @foreach ($car->subgroups as $subgroup)
            <li class="model-item">
                <img src="{{ $subgroup->img }}" alt="" style="max-width: 100%; height: 100px">
                <span>
                    {{ $subgroup->tsbem_text }}
                    {{ $subgroup->tsmoa_text }}
                </span>
                <a class="model-link" href="/illustration/?cat={{ $catalog }}&mark={{ $oid->mark }}&market={{ $oid->market }}&model={{ $oid->model }}&production_year={{ $oid->production_year }}&production={{ $oid->production }}&group={{ $oid->group }}&subgroup={{ $subgroup->hg_ug }}&image={{ $subgroup->bildtafel2 }}">
                    {{ $subgroup->tsben_text }}
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
