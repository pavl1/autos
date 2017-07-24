<template>
    <div class="groups">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите группу">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <ul v-else class="groups-list">
                <li class="groups-item" v-for="group in filtered">
                    <router-link :to="'/bmw/' + [ series, body, model, market, rule, transmission, production, group.code ].join('/')">
                        {{ group.name }}
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
            }
        }
    },
    props: ['series', 'body', 'model', 'market', 'rule', 'transmission', 'production' ],
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
                return item.name.toLowerCase().indexOf(this.search) > -1
            } )
        }
    }
}
</script>
