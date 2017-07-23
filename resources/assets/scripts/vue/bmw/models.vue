<template>
    <div class="models">
        <spinner v-if="isLoading"></spinner>
        <div class="" v-else>
            <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите комплектацию">

            <table class="table table-sm table-hover" v-for="market in markets.models" v-if="filteredModels(market).length">
                <thead>
                    <tr class="series-header">
                        <th>{{ market.MarketName }}</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link
                        tag="tr"
                        class="series-link"
                        :to="'/bmw/' + [ series, markets.code, item.ModelID, market.MarketCode ].join('/')"
                        v-for="item in filteredModels(market)">
                        <td>{{ item.ModelCode }}</td>
                    </router-link>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import Spinner from '../components/Spinner.vue'

export default {
    data() {
        return {
            isLoading: true,
            markets: {},
            search: ''
        }
    },
    props: ['series'],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('get_bmw_models', { data: { series: this.series } }).then( response => {
                this.markets = response.markets
                this.isLoading = false
            })
        },
        filteredModels(market) {
            return market.ModelInfo.filter( model => {
                return model.ModelCode.toLowerCase().indexOf(this.search) > - 1
            } )
        }
    }
}
</script>
