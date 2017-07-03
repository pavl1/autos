<h2>{{ $oid->mark }}</h2>

@if ($catalog == 'fiat')
    @foreach ($car->groups as $group)
        <li class="model-item">
            <a class="model-link" href="/subgroups/?cat={{ $catalog }}&mark={{ $oid->mark }}&model={{ $oid->model }}&production={{ $oid->production }}&group={{ $group->code }}">
                <img src="{{ $group->g_img }}" alt="">
                <span>{{ $group->descr }}</span>
            </a>
        </li>
    @endforeach
@endif
@if ($catalog == 'etka')
    @foreach ($car->groups as $group)
        <li class="model-item">
            <img src="{{ $car->image }}{{ $group->hg }}.png" alt="">
            <a class="model-link" href="/subgroups/?cat={{ $catalog }}&mark={{ $oid->mark }}&market={{ $oid->market}}&model={{ $oid->model }}&production_year={{ $oid->production_year }}&production={{ $oid->production }}&type=G&group={{ $group->hg }}">
                {{ $group->text }}<br />
            </a>
        </li>
    @endforeach
@endif
@if ($catalog == 'bmw')
    @include('partials.groups.bmw')
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



@php(the_content())
{!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
