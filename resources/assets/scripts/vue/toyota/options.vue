<template>
    <div class="production">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите модификацию">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else class="table table-sm table-hover">
                <thead>
                    <tr class="model-header">
                        <th>Комплектация</th>
                        <th>Производство</th>
                        <th>Двигатель</th>
                        <th>Кузов</th>
                        <th>Класс</th>
                        <th>КПП</th>
                        <th>Другое</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link
                    tag="tr"
                    class="series-link"
                    :to="'/toyota/' + [ mark, model, market, option.compl, option.sysopt, option.code ].join('/')"
                    v-for="option in filtered">
                        <td>{{ option.compl }}</td>
                        <td>{{ option.prod }}</td>
                        <td>{{ option.engine }}</td>
                        <td>{{ option.body }}</td>
                        <td>{{ option.grade }}</td>
                        <td>{{ option.kpp }}</td>
                        <td>{{ option.other }}</td>
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
            options: {},
            search: '',
            oid: {
                catalog: 'toyota',
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
            window.wp.ajax.send('toyota_options', { data: { oid: this.oid } }).then( response => {
                this.options = response.items
                this.isLoading = false
            })
        },
    },
    computed: {
        filtered() {
            return this.options.filter( (option) => {
                let compl = option.compl ? option.compl.toLowerCase().indexOf(this.search) > -1 : false
                let prod = option.prod ? option.prod.toLowerCase().indexOf(this.search) > -1 : false
                let engine = option.engine ? option.engine.toLowerCase().indexOf(this.search) > -1 : false
                let body = option.boddy ? option.body.toLowerCase().indexOf(this.search) > -1 : false
                let grade = option.grade ? option.grade.toLowerCase().indexOf(this.search) > -1 : false
                let kpp = option.kpp ? option.kpp.toLowerCase().indexOf(this.search) > -1 : false
                let other = option.other ? option.other.toLowerCase().indexOf(this.search) > -1 : false
                return ( compl || engine || body || grade || kpp || other )
            } )
        }

    }
}
</script>
