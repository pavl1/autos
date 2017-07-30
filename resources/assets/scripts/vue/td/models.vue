<template>
    <div class="models">
        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите модель">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Наименование</th>
                        <th>Производство</th>
                    </tr>
                </thead>
                <tbody>
                    <router-link
                    tag="tr"
                    class="series-link"
                    :to="'/td/' + [ mark, item.mod_id ].join('/')"
                    v-for="item in filteredModels">
                        <td>{{ item.mod_name_eng }}</td>
                        <td>
                            {{ item.mod_pcon_start.match( /\d{4}(\d{2})/ )[1] }}.{{ item.mod_pcon_start.match( /\d{4}/ )[0] }}
                            -
                            {{ parseInt(item.mod_pcon_end) ? item.mod_pcon_end.match( /\d{4}(\d{2})/ )[1] : '' }}{{ parseInt(item.mod_pcon_end) ? '.' + item.mod_pcon_end.match( /\d{4}/ )[0] : '...' }}
                        </td>
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
                catalog: 'td',
                mark: this.mark
            }
        }
    },
    props: [ 'mark' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('td_models', { data: { oid: this.oid } }).then( response => {
                this.models = response.items
                this.isLoading = false
            })
        }
    },
    computed: {
        filteredModels() {
            return this.models.filter( model => {
                return [ model.mod_name_eng, model.mod_pcon_start, model.mod_pcon_end ].join().toLowerCase().indexOf(this.search.toLowerCase()) > -1
            } )
        }
    }
}
</script>
