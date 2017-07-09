<table>
    <thead>
        <tr>
            <th>Производство</th>
            <th>Кузов</th>
            <th>Двигатель</th>
            <th>Привод</th>
            <th>Трансмисия</th>
            <th>Другое</th>
        </tr>
    </thead>
    @foreach ($car->modifications as $modification)
        <tbody>
            <tr onclick="window.location.href = '{{ $car->url }}&modification={{ $modification->compl }}';" style="cursor: pointer">
                <td>{{ $modification->prod }}</td>
                <td>{{ $modification->Кузов }}</td>
                <td>{{ $modification->Двигатель }}</td>
                <td>{{ $modification->Привод }}</td>
                <td>{{ $modification->Трансмисия }}</td>
                <td>{{ implode(' ', $modification->other) }}</td>
            </tr>
        </tbody>
@endforeach
</table>
