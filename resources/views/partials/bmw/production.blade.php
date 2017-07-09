<div>

    <h1 class="mb0">Выберите дату производства</h1>

    <table class="dataTable">
        @php
        $aData = $car->production;
        $startYear  = $aData['startYear'];
        $startMonth = $aData['startMonth'];
        $startDay   = sprintf("%02d",$aData['startDay']);
        $endYear    = $aData['endYear'];
        $endMonth   = $aData['endMonth'];
        $endDay     = sprintf("%02d",$aData['endDay']);

        $a = [
            "startYear"  => $aData['startYear'],
            "endYear"    => $aData['endYear'],
            "startMohth" => $aData['startYear'],
            "endMonth"   => $aData['endYear'],
        ];
        @endphp

        @for( $y = $startYear; $y <= $endYear; $y++ )
            <tr>
                <td class="yearsTD">{{ $y }}</td>
                @for( $m = 1; $m <= 12; $m++ )
                    <td class="pd2">

                        @php
                        if( $y==$startYear && $m==$startMonth ){
                            $d = $startDay;
                        }
                        elseif( $y==$endYear && $m==$endMonth ){
                            $d = $endDay;
                        }
                        else{
                            $d = "00";
                        }
                        @endphp

                        @if( ( $y==$startYear && $m<$startMonth ) || ( $y==$endYear && $m>$endMonth) )
                            &emsp;
                        @else
                            <a class="bttnGB anime" href="{{ $car->url }}&production={{ $y.sprintf("%02d",$m).$d }}">{{ sprintf("%02d",$m) }}</a>
                        @endif

                    </td>
                @endfor
            </tr>
        @endfor
    </table>

    <a class="bttnGB anime inlineBlock mt20" href="{{ $car->url }}&production=any" class="anyYear">Не важно</a>
</div>
