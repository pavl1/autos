<ul class="marks">
    @foreach ($marks as $mark)
        <li class="mark-item">
            <img class="mark-image" src="@asset('images/' . strtolower($mark->name) . '.png')" alt="">
            <span class="mark-name">{{ $mark->name }}</span>
            <ul class="mark-links">
                @if(isset($mark->original))
                    <li>
                        <a class="mark-link" href="{{ $mark->original->url }}"> Оригиналы </a>
                    </li>
                @endif
                @if(isset($mark->aftermarket))
                    @foreach ($mark->aftermarket as $item)
                        <li>
                            <a class="mark-link" href="/models/?cat=td&mark={{ $item->mfa_id }}"> Заменители {{ str_replace(strtoupper($mark->name), '', $item->mfa_mfc_code) }}</a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </li>
    @endforeach
</ul>
