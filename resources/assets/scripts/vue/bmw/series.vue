<template>
    <div class="catalog">
        <!-- Breads -->

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите модель автомобиля">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Модель</th>
                        <th>Серия</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link tag="tr" class="catalog-link" :to="'/bmw/' + [ mark, item.id].join('/')" v-for="item in filteredSeries">
                        <td>{{ item.name.split(' ', 1)[0] }}</td>
                        <td>{{ item.id }}</td>
                    </router-link>
                </tbody>
            </table>
        </transition>
    </div>
</template>

<script>
import Spinner from '../components/Spinner.vue'

export default {
    data() {
        return {
            isLoading: true,
            series: {},
            search: '',
            oid: {
                catalog: 'bmw',
                type: 'vt',
                mark: this.mark
            }
        }
    },
    props: [ 'mark' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('bmw_series', { data: { oid: this.oid } }).then( response => {
                this.series = response.series
                this.isLoading = false

            })
        }
    },
    computed: {
        filteredSeries() {
            return this.series.filter( (item) => {
                return [ item.id, item.name ].join().toLowerCase().indexOf(this.search.toLowerCase()) > -1
            })
        }
    }
}
</script>
