<template>
    <div class="catalog">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите подгруппу">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Подгруппа</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="catalog-link" v-for="subgroup in filtered" @click="illustration(subgroup.code)">
                        <td>{{ subgroup.name }}</td>
                    </tr>
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
            url: '',
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
                group: this.group
            }
        }
    },
    props: [ 'mark', 'series', 'body', 'model', 'market', 'rule', 'transmission', 'production', 'group' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('bmw_subgroups', { data: { oid: this.oid } }).then( response => {
                this.subgroups = response.subgroups
                this.url = response.url
                this.isLoading = false
            })
        },
        illustration(id) {
            window.location.href=this.url + '&graphic=' + id
        }
    },
    computed: {
        filtered() {
            return this.subgroups.filter( (item) => {
                return item.name.toLowerCase().indexOf(this.search.toLowerCase()) > -1
            } )
        }
    }
}
</script>
