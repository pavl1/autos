<ul class="marks">
    @foreach ($marks as $mark)
        {{-- @php ($attrsOriginal = isset($mark->original) ? explode( '/', str_replace( ['?', '.php'], ['/'], \A2D::getMarkUrl($mark->original) ) ) : '') --}}
        <li class="mark-item">
            <span>{{ $mark->name }}</span>
            <img src="{{ $mark->image }}" alt="">
            <ul class="mark-links">
                @if(isset($mark->original))
                    <li>
                        <a class="mark-link" href="{{ $mark->original->url }}">
                            <span>Оригиналы</span>
                        </a>
                    </li>
                @endif
                @if(isset($mark->aftermarket))
                    @foreach ($mark->aftermarket as $item)
                        <li>
                            <a class="mark-link" href="/models/?cat=td&mark={{ $item->mfa_id }}">
                                <span>Заменители {{ $item->mfa_brand }}</span>
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </li>
    @endforeach
</ul>

@php(the_content())
{!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
