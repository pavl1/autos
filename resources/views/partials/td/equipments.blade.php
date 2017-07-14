<ul class="equipments">
    @foreach ($car->equipments as $equipment)
        <li class="equipment-item">
            <a class="equipment-link" href="/tree/?cat=td&mark={{ $oid->mark }}&model={{ $oid->model }}&equipment={{ $equipment->typ_id }}">
                <span>{{ $equipment->typ_mmt_cds }}</span>
            </a>
        </li>
    @endforeach
</ul>
