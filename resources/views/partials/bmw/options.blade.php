<ul class="models">
    @foreach ($car->options as $option)
        <li class="model-item">
            <a class="model-link" href="/production/?cat={{ $oid->catalog }}&mark={{ $oid->mark }}&type={{ $oid->type }}&series={{ $oid->series }}&body={{ $oid->body }}&model={{ $oid->model }}&market={{ $oid->market}}&rule={{ $option->RuleCode }}&transmission={{ $option->GetriebeCode }}">
                <span>{{ $option->RuleName }} / {{ $option->GetriebeName }}</span>
            </a>
        </li>
    @endforeach
</ul>
