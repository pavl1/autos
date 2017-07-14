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
