<template>
    <div class="catalog">
        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите модель">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Наименование</th>
                        <th>Модификация</th>
                        <th>Производство</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link
                    tag="tr"
                    class="catalog-link"
                    :to="'/adc/' + [ mark, item.model_id ].join('/')"
                    v-for="item in filteredModels">
                        <td>{{ item.model_name }}</td>
                        <td>{{ item.model_modification }}</td>
                        <td>{{ item.model_years }}</td>
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
            models: {},
            search: '',
            oid: {
                catalog: 'adc',
                type: '9',
                mark: this.mark
            }
        }
    },
    props: [ 'mark' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('adc_models', { data: { oid: this.oid } }).then( response => {
                this.models = response.items
                this.isLoading = false
            })
        }
    },
    computed: {
        filteredModels() {
            return this.models.filter( model => {
                return [ model.model_name, model.model_years, model.model_modification ].join().toLowerCase().indexOf(this.search) > -1
            } )
        }
    }
}
</script>
