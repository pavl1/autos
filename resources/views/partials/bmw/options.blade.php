<ul class="models">
    @foreach ($car->options as $option)
        <li class="model-item">
            <a class="model-link" href="/production/?cat={{ $catalog }}&mark={{ $id->mark }}&type={{ $id->type }}&series={{ $id->series }}&body={{ $id->body }}&model={{ $id->model }}&market={{ $id->market}}&rule={{ $option->RuleCode }}&transmission={{ $option->GetriebeCode }}">
                <span>{{ $option->RuleName }} / {{ $option->GetriebeName }}</span>
            </a>
        </li>
    @endforeach
</ul>
