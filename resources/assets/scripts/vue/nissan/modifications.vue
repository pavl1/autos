<template>
    <div class="catalog">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите модификацию">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else class="table table-sm table-hover">
                <thead>
                    <tr class="model-header">
                        <th>Производство</th>
                        <th>Кузов</th>
                        <th>Двигатель</th>
                        <th>Привод</th>
                        <th>Трансмисия</th>
                        <th>Другое</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link
                    tag="tr"
                    class="catalog-link"
                    :to="'/nissan/' + [ mark, model, market, modification.compl ].join('/')"
                    v-for="modification in filtered">
                        <td>{{ modification.prod }}</td>
                        <td>{{ modification.Кузов }}</td>
                        <td>{{ modification.Двигатель }}</td>
                        <td>{{ modification.Привод }}</td>
                        <td>{{ modification.Трансмисия }}</td>
                        <td>{{ modification.other.join(' ') }}</td>
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
            modifications: {},
            search: '',
            oid: {
                catalog: 'nissan',
                mark: this.mark,
                model: this.model,
                market: this.market
            }
        }
    },
    props: [ 'mark', 'model', 'market' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('nissan_modifications', { data: { oid: this.oid } }).then( response => {
                this.modifications = response.items
                this.isLoading = false
            })
        },
    },
    computed: {
        filtered() {
            return this.modifications.filter( (modification) => {
                return [ modification.prod, modification.Кузов, modification.Двигатель, modification.Привод, modification.Трансмиссия, modification.other ].join().toLowerCase().indexOf(this.search.toLowerCase()) > -1
            } )
        }

    }
}
</script>
