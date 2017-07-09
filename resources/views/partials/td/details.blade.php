<p>{{ $car->info->markName }} {{ $car->info->modelName }}</p>
<h3>{{ $car->group->str_des }}</h3>
<table>
    @foreach ($car->details as $item)
        <tr>
            <td><img src="{{ $item->brandLogo }}" alt=""></td>
            <td>{{ $item->art_article_nr }}</td>
            <td>{{ $item->ga_des }}</td>
            <td><a href="/search/{{ $item->art_article_nr }}">Цена</a></td>
        </tr>
    @endforeach
</table>
