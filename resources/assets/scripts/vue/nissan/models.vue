<template>
    <div class="models">
        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите модель">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <div v-else>

            <table class="table table-sm table-hover" v-for="(market, index) in markets" v-if="filteredModels(market).length">
                <thead>
                    <tr class="model-header">
                        <th>{{ market.name }}</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link
                    tag="tr"
                    class="series-link"
                    :to="'/nissan/' + [ mark, item.series, index ].join('/')"
                    v-for="item in filteredModels(market)">
                        <td>{{ item.model }} / {{ item.series }} ({{ item.date }})</td>
                    </router-link>
                </tbody>
            </table>
        </div>
    </transition>
</div>
</template>

<script>
import Spinner from '../components/Spinner.vue'

export default {
    data() {
        return {
            isLoading: true,
            markets: {},
            search: '',
            oid: {
                catalog: 'nissan',
                mark: this.mark
            }
        }
    },
    props: [ 'mark' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('nissan_models', { data: { oid: this.oid } }).then( response => {
                this.markets = response.items
                this.isLoading = false
            })
        },
        filteredModels(market) {
            return market.models.filter( model => {
                return model.model.toLowerCase().indexOf(this.search) > - 1
            } )
        }
    }
}
</script>
