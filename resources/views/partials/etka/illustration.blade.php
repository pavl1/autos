@php
    $zoom       = 0.5;
    $aLabels    = $car->illustration->labels;
    $aDetails   = $car->illustration->details;
    $aBreads    = $car->illustration->breads;

    $imgInfo = $car->illustration->imgInfo;
    $iSID    = A2D::property($imgInfo,'iSID');        /// Ключ, нужен для построение картинки
    $imgUrl  = A2D::property($imgInfo,'url');         /// Адрес иллюстрации на сервере
    $width   = A2D::property($imgInfo,'width');       /// Ширина изображения
    $height  = A2D::property($imgInfo,'height');      /// Высота изображения
    $attrs   = A2D::property($imgInfo,'attrs');       /// Те же данные одним атрибутом
    $percent = A2D::property($imgInfo,'percent')/100; /// Коэффициент в каком соотношение вернулась иллюстрация, нужно для ограничения показов с одного агента на IP
    $limit   = A2D::property($imgInfo,'limit');       /// Ваше число ограничений для отображения пользователю, у которого сработало ограничение

    /// Корневой элемент для зума
    $rootZoom = "imageLayout";

    /// Заголовок для страницы. Вполне подходит из "хлебных крошек".
    $h1 = $aBreads->illustration->name;

    $jumpUrl = $car->url;
@endphp

@php( include(get_theme_root() . "/autos/vendor/autodealer/helpers/illustration.php" ) )
<div id="detailsMap">

    <h1><?=$h1?></h1>

    <?php $px = 5; $py = 1; ?>

    <div class="defBorder imgArea mb30" id="imageArea" style="height: 500px; width: 100%; overflow: hidden; position: relative">
        @if( $percent<1 )
            @php ( $zoom = $percent )
            <div class="isLimit">Превышен лимит показов в сутки (<?=$limit?>)</div>
        @else
            @php ( $zoom = 1 )
        @endif
        <div id="imageLayout" style="position:absolute;left:0;top:0;width:{{ $width }}px;height:{{ $height }}px">
            <?php /*/?>
            <img src="<?=$imgUrl?>" border="1">
            <?php //*/?>
            <canvas id="canvas" width="{{ $width }}" height="{{ $height }}" style="margin:0;padding:0;"></canvas>
            @php ($prevNamber = FALSE)
            @foreach( $aLabels AS $_v )
                @php
                    $title   = $_v->id;
                    $lLeft   = (float)$_v->cLeft * $zoom - $px * $zoom;
                    $lTop    = (float)$_v->cTop*$zoom - $py*$zoom;
                    $lWidth  = ( (float)$_v->cWidth ) + $px*$zoom*2;
                    $lHeight = ( (float)$_v->cHeight ) + $py*$zoom*2;

                    $currNumber = $_v->cPoint;
                    $number = ( $currNumber==$prevNamber ) ?$currNumber :$currNumber;
                    $prevNamber = $currNumber;
                @endphp
                <div id="l{{ $number }}" class="l{{ $number }} mapLabel" title="{{ $title }}"
                    style="
                    position:absolute;
                    left:<?=$lLeft?>px;
                    top:<?=$lTop?>px;
                    min-width:<?=$lWidth?>px;
                    min-height:<?=$lHeight?>px;
                    padding:<?=$py?>px <?=$px?>px;
                    "
                    onclick="labelClick(this,false)"
                    ondblclick="labelClick(this,true)"
                    >
                </div>
            @endforeach
        </div>
        @php (include(get_theme_root() . "/autos/vendor/autodealer/helpers/zoomer.php") )
    </div>

    <div id="detailsList">
        <table class="simpleTable innerTable">
            <thead>
                <tr>
                    <th class="ETKADetailPosition">№</th>
                    <th class="ETKADetailNumber">Номер</th>
                    <th class="ETKAName">Наименование</th>
                    <th class="ETKAOther">Примечание</th>
                    <th class="ETKADetailQuantity">Кол-во</th>
                    <th class="ETKAInfo">Данные по модели</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //$prevNamber = FALSE;
                $nc = [];
                $countDetails = count($aDetails)-1;
                /*/
                print'<pre>';print_r(array(
                $countDetails
            ));print'</pre>';exit;
            //*/
            for( $i=0; $i<=$countDetails ; ++$i ){ $_v = $aDetails[$i];
                //$i = -1; foreach( $aDetails AS $_v ){ ++$i;

                $currNumber = $_v->btpos;

                /// Number Format
                $_v->teilenummer = preg_replace('/(\s+)/','  ',$_v->teilenummer);
                ///$_v->teilenummer = implode("&ensp;", str_split($_v->teilenummer,3));
                $_v->teilenummer = preg_replace("~((?:.|\n){3})~im","\${1} ",$_v->teilenummer);

                $detailID = ( $percent==1 ) ?$_v->teilenummer :"*******";

                $style = "";
                $other = $_v->tsbem_text;
                if( $_v->ou=="U" ){
                    $style = 'style="background:lightgreen"';
                }
                elseif( $_v->btpos && $_v->tsbem_text && !$_v->ou && !$_v->stuck && !$_v->teilenummer ){
                    $style = 'style="background:lavender"';
                    $gr = $other{0};
                    $sg = substr($other,0,3);
                    $aJump = explode('-',$other);
                    if( count($aJump)==2 ){
                        $p1 = $aJump[0];
                        $p2 = sprintf("%'.03d\n",$aJump[1]);
                        $jump = $p1.$p2;
                        $a1 = "<a href=\"$jumpUrl&group=$gr&subgroup=$sg&graphic=$jump\" target=\"_blank\">";
                        $a2 = "</a>";
                        $other = $a1.$other.$a2;
                    }
                }

                ?>
                <tr id="d<?=$_v->id?>"
                    <?php if( $_v->ou!="U" ){ ?>
                        class="none anime pointer"
                        data-position="<?=str_replace(['(',')'],'',trim($currNumber))?>"
                        onclick = "trClick(this,0)"
                        ondblclick = "trClick(this,1)"
                        <?php }else{?>
                            <?=$style?>
                            <?php }?>
                            >
                            <td class="ETKADetailPosition"><?=$_v->btpos?></td>
                            <td class="ETKADetailNumber detailNumber c2cValue" id="c2cValue_<?=$_v->id?>">
                                <?php if( $detailID ){?>
                                    <?=A2D::callBackLink($detailID,A2D::$callback)?>
                                    &ensp;<img title="Скопировать" id="c2cBttn_<?=$_v->id?>" src="/media/images/copy_20x20.png">
                                    <?php }?>
                                </td>
                                <td class="ETKAName detailName"><?=$_v->tsben_text?></td>
                                <td class="ETKAOther"><?=$other?></td>
                                <td class="ETKADetailQuantity"><?=str_replace(";","</br>",$_v->stuck)?></td>
                                <td class="ETKAInfo"><?=$_v->tsmoa_text?></td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>

            </div>

            <script>
            var offline = '<?=A2D::$offline?>';
            jQueryA2D(document).ready(function(){
                labelsTitle();
            });
            </script>
