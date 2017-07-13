@php
$imgInfo    = isset($car->illustration->imgInfo) ? $car->illustration->imgInfo : ''; /// Объект:
$aLabels    = isset($imgInfo->labels) ? $imgInfo->labels : array();
$aComments  = isset($imgInfo->comments) ? $imgInfo->comments : array();
$iSID       = isset($imgInfo->iSID) ? $imgInfo->iSID : '';        /// Ключ, нужен для построение картинки
$imgUrl     = isset($imgInfo->url) ? $imgInfo->url : '';         /// Адрес иллюстрации на сервере
$width      = isset($imgInfo->width) ? $imgInfo->width : '';       /// Ширина изображения
$height     = isset($imgInfo->height) ? $imgInfo->height: '';      /// Высота изображения
$attrs      = isset($imgInfo->attrs) ? $imgInfo->attrs: '';       /// Те же данные одним атрибутом
$prc        = isset($imgInfo->percent) ? $imgInfo->percent/100 : ''; /// Коэффициент в каком соотношение вернулась иллюстрация, нужно для ограничения показов с одного агента на IP
$limit      = isset($imgInfo->limit) ? $imgInfo->limit : '';       /// Ваше число ограничений для отображения пользователю, у которого сработало ограничение

$rootZoom = "imageLayout";

foreach( $car->parts->partDrawData->variants as $pd ){
    $tabs[] = [
        'url' => "/illustration/?cat=$catalog&mark={ $id->mark }&model={ $id->model }&production={ $id->production }&group={ $id->group }&subGroup={ $id->subgroup }&tableCod=".urlencode(base64_encode($id->table))."&variant=".$pd->variante,
        'description' => $pd->variante
    ];
}
@endphp

<div class="fiat_draw" id="detailsMap">
    <div class="row">
        <script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            @if( count($tabs) > 1 )

                <div id="tabs">
                    @php
                        $widthTabs = count($tabs) <= 4 ? 23 : 100 / count($tabs) - 1
                    @endphp

                    @foreach( $tabs as $tab )
                        @php
                            $class = $res->meta->variante == $tab['description'] ? 'activeTab cBlue' : '';
                        @endphp
                        <a class="{{ $class }}" style="width: {{ $widthTabs }}%" href=" {{ $t['url'] }}">
                            Вид {{ $tab['description'] }}
                        </a>
                    @endforeach
            </div>

        @endif

        <div class="defBorder imgArea mb30" id="imageArea" style="width: 100%; height: 500px; overflow: hidden; border: 1px solid #333; position: relative">
            @if ( $prc < 1 )
                @php( $zoom=$prc )
                <div class="isLimit">Превышен лимит показов в сутки (<?=$limit?>)</div>
            @else
                @php( $zoom=1 )
            @endif
            <div id="imageLayout" style="position:absolute;left:0;top:0;width:<?=$width?>px;height:<?=$height?>px">
                <canvas id="canvas" width="<?=$width?>" height="<?=$height?>" style="margin:0;padding:0;"></canvas>
                @foreach( $aLabels AS $_v )
                    <div id="l{{ $_v->label }}"
                        class="l{{ $_v->label }} mapLabel"
                        style="
                            position:absolute;
                            left:       {{ $_v->topX }}px;
                            top:        {{ $_v->topY }}px;
                            min-width:  {{ $_v->width }}px;
                            min-height: {{ $_v->height }}px;
                        "
                        onclick="labelClick(this,false)"
                        ondblclick="labelClick(this,true)"
                        >
                    </div>
                @endforeach
            </div>
            @php (include( get_theme_root() . '/autos/vendor/autodealer/helpers/zoomer.php'))
        </div>
    </div>
    {{-- Таблица с деталями --}}
    <div class="">
        <table>
            <thead>
                <tr>
                    <th>№</th>
                    <th>Код</th>
                    <th>Описание</th>
                    <th>Применяемость</th>
                    <th>Модификация</th>
                    <th>Кол-во</th>
                </tr>
            </thead>
            <tbody>
                @foreach ( $car->illustration->draw as $detail )
                    <tr>
                        <td
                            id="{{ $detail->tbd_rif }}"
                            data-position="{{ $detail->tbd_rif }}"
                            onclick="trClick(this, 0)"
                            ondblclick="trClick(this, 0)"
                        ></td>
                        <td>{{ $detail->tbd_rif }}</td>
                        <td id="c2cValue_{{ $detail->tbd_rif }}">
                            @php( \A2D::callBackLink( $detail->prt_cod, \A2D::$callback ) )
                        </td>
                        <td>{{ $detail->cds_dsc }}</td>
                        <td>{{ $detail->tbd_val_formula }}</td>
                        <td>{{ $detail->modif }}</td>
                        <td>{{ $detail->tbd_qty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- Таблица с расшифровками  --}}
    @if( isset($car->illustration->meta->patterns) )
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="explanation mt40">
                    <div class="expTable">
                        <div class="eTableHead">Расшифровка сокращений</div>
                        <div class="eTableBody">
                            @foreach( $car->illustration->meta->patterns as $pattern )
                                <span class="sign"> {{ $pattern->name }}</span> = <span class="desc">{{ $pattern->description }}</span>
                                <br/>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
