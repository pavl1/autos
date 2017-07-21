<template>
    <div class="series">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <div v-else>
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
        </transition>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Spinner from './components/Spinner.vue'

export default {
    data() {
        return {
            isLoading: true,
            series: {}
        }
    },
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('get_series', { data: {} }).then( response => {
                this.isLoading = false
                console.log(response)
            })
        }
    },
    computed: {
        ...mapGetters([ 'getOid' ]),
    }

}
</script>
