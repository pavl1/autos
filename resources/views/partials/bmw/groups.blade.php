{{-- @include('helpers.info.bmw') --}}
<script type="text/html" id="tmpl-subgroups">
    @verbatim
        Агрегат
        <ul class="subgroups-list">
            <# _.each(data.subgroups, (subgroup) => { #>
                <li class="subgroups-item col"><a class="subgroups-link" href="{{ data.url }}&graphic={{ subgroup.code }}">{{ subgroup.name }}</a></li>
            <# }) #>
        </ul>
    @endverbatim
</script>


<div class="groups">

    <input class="groups-search" type="text" name="" value="" placeholder="Выберите / введите узел агрегата">

    @foreach( $car->groups AS $group )
        @php( $oid->group = $group->code )
        <div class="groups-item">
            <a class="groups-link col" href="#group-{{ $group->code}}" aria-expanded="false"
                data-toggle="collapse"
                data-oid="{{ json_encode($oid) }}"
            >
                {{ $group->name }}
            </a>
            <div class="subgroups collapse" id="group-{{ $group->code }}">

            </div>
        </div>
    @endforeach
</div>
