<template>
    <div class="catalog">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите дату производства">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Дата производства</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link
                        tag="tr"
                        class="catalog-link"
                        :to="'/etka/' + [ mark, market, model, production.einsatz, production.epis_typ, oid.dir ].join('/')"
                        v-for="production in filtered"
                    >
                        <td>{{ production.einsatz }}</td>
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
                return production.einsatz.toLowerCase().indexOf(this.search.toLowerCase()) > -1
            } )
        }

    }
}
</script>
