<template>
    <div class="catalog">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите группу">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Группа</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link tag="tr" class="catalog-link" v-for="group in filtered" :to="'/bmw/' + [ mark, series, body, model, market, rule, transmission, production, group.code ].join('/')">
                        <td>{{ group.name }}</td>
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
            groups: {},
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
                transmission: this.transmission,
                production: this.production,
            }
        }
    },
    props: [ 'mark', 'series', 'body', 'model', 'market', 'rule', 'transmission', 'production' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('bmw_groups', { data: { oid: this.oid } }).then( response => {
                this.groups = response.groups
                this.isLoading = false
            })
        }
    },
    computed: {
        filtered() {
            return this.groups.filter( (item) => {
                return item.name.toLowerCase().indexOf(this.search.toLowerCase()) > -1
            } )
        }
    }
}
</script>
