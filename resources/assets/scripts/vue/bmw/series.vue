<template>
    <div class="series">
        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <div class="" v-else>

                <!-- Breads -->

                <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите модель автомобиля">

                <table class="table table-sm table-hover">
                    <thead>
                        <tr class="series-header">
                            <th>Модель</th>
                            <th>Серия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <router-link tag="tr" class="series-link" :to="'/bmw/' + item.id" v-for="item in filteredSeries">
                            <td>{{ item.name.split(' ', 1)[0] }}</td>
                            <td>{{ item.id }}</td>
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
            series: {},
            search: ''
        }
    },
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('get_bmw_series', { data: {} }).then( response => {
                this.series = response.series
                this.isLoading = false

            })
        }
    },
    computed: {
        filteredSeries() {
            return this.series.filter( (item) => {
                let id = item.id.toLowerCase().indexOf(this.search) > -1
                let name = item.name.toLowerCase().indexOf(this.search) > -1
                return ( id || name )
            })
        }
    }
}
</script>
