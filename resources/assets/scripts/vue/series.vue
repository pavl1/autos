<template>
    <div class="series">

        <input class="series-search" type="text" name="" value="" placeholder="Выберите / введите модель автомобиля">

        <div class="series-container">
            <div class="series-header">
                <span class=col>Модель</span>
                <span class=col>Серия</span>
            </div>
        </div>

        <div class="series-container" v-for="serie in series">
            <a class="series-link" :href="'#' + serie.Baureihe" aria-expanded="false" data-toggle="collapse">
                <span class="col">{{ serie.ExtBaureihe.split(' ', 1) }}</span>
                <span class="col">{{ serie.Baureihe }}</span>
            </a>
            <div class="model collapse" :id="serie.Baureihe">

            </div>
        </div>

    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    data() {
        return {
            series: {}
        }
    },
    beforeRouteEnter (to, from, next) {
        window.wp.ajax.send('get_series').then( (response) => {
            next( vm => vm.series = response )
        })
    },
    computed: {
        ...mapGetters([ 'getOid' ]),
    }

}
</script>
