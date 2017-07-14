<ul class="models">
    @foreach ($car->models as $model)
        <li class="model-item">
            <a class="model-link" href="/equipments/?cat=td&mark={{ $oid->mark }}&model={{ $model->mod_id }}">
                <span>{{ $model->mod_name }}</span>
            </a>
        </li>
    @endforeach
</ul>
