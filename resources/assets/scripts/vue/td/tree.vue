<template>
    <div class="models">
        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите комплектацию">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <div v-else>
                <table v-for="leaf in tree" class="table table-sm table-hover" v-if="filteredTree(leaf).length">
                    <thead>
                        <tr>
                            <th>{{ leaf.str_des }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <router-link
                        tag="tr"
                        class="series-link"
                        :to="'/td/' + [ mark, model, equipment, item.str_id ].join('/')"
                        v-for="item in filteredTree(leaf)">
                            <td>{{ item.str_des }}</td>
                        </router-link>
                    </tbody>
                </table>
            </div>
    </transition>
</div>
</template>

<script>
import Spinner from '../components/Spinner.vue'

export default {
    data() {
        return {
            isLoading: true,
            tree: {},
            search: '',
            oid: {
                catalog: 'td',
                mark: this.mark,
                model: this.model,
                equipment: this.equipment
            }
        }
    },
    props: [ 'mark', 'model', 'equipment' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('td_tree', { data: { oid: this.oid } }).then( response => {
                this.tree = response.items
                this.isLoading = false
            })
        },
        filteredTree(leaf) {
            return leaf.childrens.filter( item => {
                return item.str_des.toLowerCase().indexOf(this.search.toLowerCase()) > -1
            } )
        }
    }
}
</script>
