@if ($oid->market != 'jp')
    <img src="{{ $car->image }}" alt="">
@endif
<table>
    <thead>
        <tr>
            <th>Группа</th>
            <th>Название</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($car->groups as $group)
            <tr onclick="window.location.href = '{{ $car->url }}&group={{ $group->Group }}';" style="cursor: pointer">
                <td>{{ $group->Group }}</td>
                <td>{{ $group->GroupName }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
