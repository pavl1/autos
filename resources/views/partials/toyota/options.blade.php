<table>
    <thead>
        <tr>
            <th>Комплектация</th>
            <th>Производитель</th>
            <th>Двигатель</th>
            <th>Кузов</th>
            <th>Класс</th>
            <th>КПП</th>
            <th>Другое</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($car->options as $option)
            <tr onclick="window.location.href = '{{ $car->url }}&compl={{ $option->compl }}&option={{ $option->sysopt }}&code={{ $option->code }}'" style="cursor: pointer">
                <td>{{ $option->compl }}</td>
                <td>{{ $option->engine }}</td>
                <td>{{ $option->body }}</td>
                <td>{{ $option->grade }}</td>
                <td>{{ $option->kpp }}</td>
                <td>{{ $option->other }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="shortening">
    <div class="shortening-title">{{ __('Shortening', 'sage') }}</div>
    <div class="shortening-body">
        @foreach ($car->shortening as $key => $shorts)
            <div class="shortening-group-title">{{ $key }}</div>
            <div class="shortening-group-body">
                @foreach ($shorts as $short)
                    <div class="shortening-row">
                        <span class="shortening-sign">{{ $short['sign'] }}</span>
                        =
                        <span class="shortening-description">{{ $short['description'] }}</span>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
