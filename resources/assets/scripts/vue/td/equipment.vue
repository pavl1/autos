<template>
    <div class="catalog">
        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите комплектацию">

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
                    class="catalog-link"
                    :to="'/td/' + [ mark, model, item.typ_id ].join('/')"
                    v-for="item in filteredEquipments">
                        <td>{{ item.typ_mmt_cds }}</td>
                        <td>
                            {{ item.typ_pcon_start.match( /\d{4}(\d{2})/ )[1] }}.{{ item.typ_pcon_start.match( /\d{4}/ )[0] }}
                            -
                            {{ parseInt(item.typ_pcon_end) ? item.typ_pcon_end.match( /\d{4}(\d{2})/ )[1] : '' }}{{ parseInt(item.typ_pcon_end) ? '.' + item.typ_pcon_end.match( /\d{4}/ )[0] : '...' }}
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
            equipments: {},
            search: '',
            oid: {
                catalog: 'td',
                mark: this.mark,
                model: this.model
            }
        }
    },
    props: [ 'mark', 'model' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('td_equipments', { data: { oid: this.oid } }).then( response => {
                this.equipments = response.items
                this.isLoading = false
            })
        }
    },
    computed: {
        filteredEquipments() {
            return this.equipments.filter( equipment => {
                return [ equipment.typ_mmt_cds, equipment.typ_pcon_start, equipment.typ_pcon_end ].join().toLowerCase().indexOf(this.search) > -1
            } )
        }
    }
}
</script>
