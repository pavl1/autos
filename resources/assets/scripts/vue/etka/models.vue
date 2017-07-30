<template>
    <div class="models">
        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите модель">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <div v-else>

            <table class="table table-sm table-hover" v-for="(market, index) in markets" v-if="filteredModels(market).length">
                <thead>
                    <tr class="model-header">
                        <th>{{ index }}</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link
                    tag="tr"
                    class="series-link"
                    :to="[ mark, index, item.modell ].join('/')"
                    v-for="item in filteredModels(market)">
                        <td>{{ item.bezeichnung }} / {{ item.einsatz }} - {{ item.auslauf > 0 ? item.auslauf : '...' }}</td>
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
                catalog: 'etka',
                mark: this.mark
            }
        }
    },
    props: [ 'mark' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('etka_models', { data: { oid: this.oid } }).then( response => {
                this.markets = response.markets
                this.isLoading = false
            })
        },
        filteredModels(models) {
            return models.filter( model => {
                return model.bezeichnung.toLowerCase().indexOf(this.search) > - 1
            } )
        }
    }
}
</script>
