<template>
    <div class="production">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите дату производства">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <ul v-else>
                <li v-for="production in filtered">
                    <router-link :to="'/etka/' + [ mark, market, model, production.einsatz, production.epis_typ, oid.dir ].join('/')">{{ production.einsatz }}</router-link>
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
            productions: {},
            search: '',
            oid: {
                catalog: 'etka',
                dir: 'R',
                mark: this.mark,
                market: this.market,
                model: this.model
            }
        }
    },
    props: [ 'mark', 'market', 'model' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('etka_production', { data: { oid: this.oid } }).then( response => {
                this.productions = response.items
                this.isLoading = false
            })
        },
    },
    computed: {
        filtered() {
            return this.productions.filter( (production) => {
                return production.einsatz.toLowerCase().indexOf(this.search) > -1
            } )
        }

    }
}
</script>
