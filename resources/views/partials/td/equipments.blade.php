<ul class="equipments">
    @foreach ($car->equipments as $equipment)
        <li class="equipment-item">
            <a class="equipment-link" href="/tree/?cat=td&mark={{ $id->mark }}&model={{ $id->model }}&equipment={{ $equipment->typ_id }}">
                <span>{{ $equipment->typ_mmt_cds }}</span>
            </a>
        </li>
    @endforeach
</ul>
