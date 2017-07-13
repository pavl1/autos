<div class="">
    <div class="illustration">
        {!! $car->illustration !!}
    </div>
    <div id="zoomer">
        <div class="ml20">
            <INPUT type="checkbox" checked onclick="showlabels(this.checked);" value="1" style="vertical-align:middle;" id="cl1" title="hide-show">
                <label title="hide-show" for="cl1">метки</label>&nbsp;
                <B style="vertical-align:middle">Масштаб: </B>
                <input type="text" readonly style="vertical-align:middle;width:40px;font-size:10pt;height:16px;background: transparent; border: 0px #000000 Solid;" id="map_info" value="100%">
                <span class="zoomBttn" onclick="izoom(-1);" title="-Zoom-">-</span>&nbsp;
                <span class="zoomBttn" onclick="izoom(0);" title="=Zoom=">100%</span>&nbsp;
                <span class="zoomBttn" onclick="izoom(1);" title="+Zoom+">+</span>
            </div>
        </div>
    </div>
</div>
<table>
    <thead>
        <th>
            <td>№</td>
            <td></td>
            <td>Наименование</td>
            <td>Номер</td>
            <td></td>
        </th>
    </thead>
    <tbody>
        @foreach ($car->details as $detail)
            <tr>
                <td>@if ( $detail->detail_id ) {{ $detail->detail_id }} @endif</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
