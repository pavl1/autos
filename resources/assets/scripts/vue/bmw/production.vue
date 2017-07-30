<template>
    <div class="production">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите дату производства">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table class="production-table" v-else>
                <tr v-for="y in years">
                    <td>{{ y }}</td>
                    <td class="production-item" v-for="m in 12">
                        <span v-if="(y == startYear && m < startMonth) || (y == endYear && m > endYear)">&emsp;</span>
                        <router-link v-else :to="'/bmw/' + [ mark, series, body, model, market, rule, transmission, '' + y + m + d(y, m) ].join('/')">{{ month(m) }}</router-link>
                    </td>
                </tr>
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
            production: {},
            search: '',
            oid: {
                catalog: 'bmw',
                type: 'vt',
                mark: this.mark,
                series: this.series,
                body: this.body,
                model: this.model,
                market: this.market,
                rule: this.rule,
                transmission: this.transmission
            }
        }
    },
    props: [ 'mark', 'series', 'body', 'model', 'market', 'rule', 'transmission' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('bmw_production', { data: { oid: this.oid } }).then( response => {
                this.production = response.production
                this.isLoading = false
            })
        },
        d(y, m) {
            let d = ''
            if ( y == this.startYear && m == this.startMonth ) d = this.startDay
            else if ( y == this.endYear && m == this.endYear ) d = this.endYear
            else d = '00'
            return d
        },
        month(m) {
            return m > 9 ? m : '0' + m
        }
    },
    computed: {
        startYear() { return this.production.startYear },
        endYear() { return this.production.endYear },
        startMonth() { return this.production.startMonth },
        endMonth() { return this.production.endMonth },
        startDay() { return this.production.startDay },
        years() {
            let years = []
            for (var y = parseInt(this.startYear); y <= this.endYear ; y++) years.push(y)
            return years
        }

    }
}
</script>
