<template>
    <div class="subgroups">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите подгруппу">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <ul v-else class="subgroups-list">
                <li class="subgroups-item" v-for="subgroup in filtered">
                    <router-link :to="'/bmw/' + [ series, body, model, market, rule, transmission, production, group, subgroup.code ].join('/')">
                        {{ subgroup.name }}
                    </router-link>
                </li>
            </ul>
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
    props: ['series', 'body', 'model', 'market', 'rule', 'transmission', 'production', 'group' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('bmw_subgroups', { data: { oid: this.oid } }).then( response => {
                this.subgroups = response.subgroups
                this.isLoading = false
            })
        }
    },
    computed: {
        filtered() {
            return this.subgroups.filter( (item) => {
                return item.name.toLowerCase().indexOf(this.search) > -1
            } )
        }
    }
}
</script>
