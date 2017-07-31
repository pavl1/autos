<template>
    <div class="catalog">
        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите модель">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <div v-else>

            <table class="table table-sm table-hover" v-for="(market, index) in markets" v-if="filteredModels(market).length">
                <thead>
                    <tr>
                        <th>{{ market.name }}</th>
                    </tr>
                    <tr>
                        <th>Модель</th>
                        <th>Модификация</th>
                        <th>Производство</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link
                    tag="tr"
                    class="catalog-link"
                    :to="'/toyota/' + [ mark, item.modelCode, index ].join('/')"
                    v-for="item in filteredModels(market)">
                        <td>{{ item.modelName }}</td>
                        <td>{{ item.modifications }}</td>
                        <td>{{ item.prodaction }})</td>
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
                catalog: 'toyota',
                mark: this.mark
            }
        }
    },
    props: [ 'mark' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('toyota_models', { data: { oid: this.oid } }).then( response => {
                this.markets = response.items
                this.isLoading = false
            })
        },
        filteredModels(market) {
            return market.models.filter( model => {
                return model.modelName.toLowerCase().indexOf(this.search.toLowerCase()) > - 1
            } )
        }
    }
}
</script>
