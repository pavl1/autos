<template>
    <div class="options">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите дату производства">

        <spinner v-if="isLoading"></spinner>
        <table class="production-table" v-else>
            <tr>
                <td>{{ startYear }}</td>
            </tr>

        </table>
    </div>
</template>

<script>
import Spinner from '../components/Spinner.vue'

export default {
    data() {
        return {
            isLoading: true,
            production: {},
            search: '',
            oid: {
                series: this.series,
                body: this.body,
                model: this.model,
                market: this.market,
                rule: this.rule,
                transmission: this.transmission
            }
        }
    },
    props: ['series', 'body', 'model', 'market', 'rule', 'transmission' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('get_bmw_production', { data: { oid: this.oid } }).then( response => {
                this.production = response.production
                this.isLoading = false
            })
        }
    },
    computed: {
        startYear() { return this.production.startYear },
        endYear() { return this.production.endYear },
        startMonth() { return this.production.startMonth },
        endMonth() { return this.production.endMonth },
        startDay() { return this.production.startDay },
        years() {
            Array.apply(null, Array(5)).map( (_, i) => { return i } )
        }
    }
}
</script>
