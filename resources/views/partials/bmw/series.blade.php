<script type="text/html" id="tmpl-models-list">
    @verbatim
        <p>Комплектация</p>
        <# _.each(data.markets, (market) => { #>
            <p class="model-market">{{ market.MarketName }}</p>
            <ul class="model-list">
                <# _.each(market.ModelInfo, (model) => {
                    data.oid.body = data.body;
                    data.oid.model = model.ModelID;
                    data.oid.market = market.MarketCode;
                    let oid = JSON.stringify(data.oid);
                #>
                    <li class="model-item col"
                        data-oid="{{ oid }}"
                    >
                        {{ model.ModelCode }}
                    </li>
                <# }) #>
            </ul>
        <# }) #>
    @endverbatim
</script>
<script type="text/html" id="tmpl-options-list">
    @verbatim
        <div class="modal-header">
            <h5 class="modal-title">Выберите опцию</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <ul class="option-list">
                <# _.each(data.options, (option) => {
                    data.oid.rule = option.RuleCode;
                    data.oid.transmission = option.GetriebeCode;
                    let oid = JSON.stringify(data.oid);
                    #>
                    <li class="option-item">
                        <a class="option-link"
                            href="#"
                            data-oid="{{ oid }}"
                        >
                        {{ option.RuleName }} / {{ option.GetriebeCode }}
                    </a>
                    </li>
                <# }) #>
            </ul>
        </div>
        <div class="modal-footer">
        </div>
    @endverbatim
</script>
<script type="text/html" id="tmpl-production-list">
    @verbatim
    <div class="modal-header">
        <h5 class="modal-title">Выберите дату производства</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <a class="back-to-options" href="#">Вернуться к выбору опций</a>
        <table>
            <# let  startYear = data.production.startYear,
            endYear = data.production.endYear,
            startMonth = data.production.startMonth,
            endMonth = data.production.endMonth,
            startDay = data.production.startDay;
            #>
            <# for ( y = startYear; y <= endYear; y++ ) { #>
                <tr>
                    <td>{{ y }}</td>
                    <# for ( m = 1; m <= 12; m++ ) { #>
                        <td class="production-cell">
                            <# if ( y == startYear && m == startMonth ) d = startDay;
                            else if ( y == endYear && m == endYear ) d = endYear;
                            else d = '00'; #>

                            <# if ( (y == startYear && m < startMonth) || (y == endYear && m > endYear) ) { #>
                                &emsp;
                            <# } else { m = m > 9 ? m : '0' + m; #>
                                <a class="production-link" href="{{ data.url }}&production={{ '' + y + m + d }}">{{ m }}</a>
                            <# } #>
                        </td>
                    <# } #>
                </tr>
            <# } #>
        </table>
        <a class="production-link" href="{{ data.url }}&production=any">Не важно</a>
    </div>
    <div class="modal-footer">
    </div>
    @endverbatim
</script>

<div class="series">

    <input class="series-search" type="text" name="" value="" placeholder="Выберите / введите модель автомобиля">

    <div class="series-container">
        <div class="series-header">
            <span class=col>Модель</span>
            <span class=col>Серия</span>
        </div>
    </div>
    @foreach ($car->series as $series)
        @php( $oid->series = $series->Baureihe )
        <div class="series-container">
            <a class="series-link" href="#{{ $series->Baureihe }}" aria-expanded="false"
                data-toggle="collapse"
                data-oid="{{ json_encode($oid) }}"
            >
                <span class="col">{{ explode(' ', $series->ExtBaureihe)[0] }}</span>
                <span class="col">{{ $series->Baureihe }}</span>
            </a>
            <div class="model collapse" id="{{ $series->Baureihe }}">
            </div>
        </div>
    @endforeach
</div>

<div class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>
