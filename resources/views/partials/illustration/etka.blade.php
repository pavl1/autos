<div class="detailsMap">

    <div class="defBorder imgArea mb30" id="imageArea">
        <div id="{{ $car->zoom }}" style="height: {{ $car->image->height }}px; width: {{ $car->image->width }}px">
            <canvas id="canvas" width="{{ $car->image->width }}" height="{{ $car->image->height }}"></canvas>
            @foreach ($car->labels as $label)
                <div
                    class="mapLabel"
                    title="{{ $label->id }}"
                    onClick="labelClick(this, false)"
                    ondblclick="labelClick(this, true)"
                </div>
            @endforeach
        </div>

    </div>

    <div class="">

    </div>

</div>
