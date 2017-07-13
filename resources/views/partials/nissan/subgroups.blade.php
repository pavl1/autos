@if (strtolower($id->market) != 'js')
    <img src="{{ $car->image }}" alt="">
@endif

<table>
    <thead>
        <tr>
            <th>№ Фигуры</th>
            <th>Название</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($car->subgroups as $subgroup)
            <tr onclick="window.location.href = '{{ $car->url }}&figure={{ $subgroup->figure }}'" style="cursor: pointer">
                <td>{{ $subgroup->figure }}</td>
                <td>{{ $subgroup->PName }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
