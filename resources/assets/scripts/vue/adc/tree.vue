<template>
    <div class="catalog">
        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите раздел">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <div v-else>
                <table v-for="child in tree" class="table table-sm table-hover" v-if="filteredTree(child).length">
                    <thead>
                        <tr>
                            <th>{{ child.tree_name }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="catalog-link" v-for="item in filteredTree(child)" @click="illustration(item)">
                            <td>{{ item.tree_name }}</td>
                        </tr>
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
            url: '',
            oid: {
                catalog: 'adc',
                type: '9',
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
            window.wp.ajax.send('adc_tree', { data: { oid: this.oid } }).then( response => {
                this.tree = response.items
                this.url = response.url
                this.isLoading = false
            })
        },
        filteredTree(leaf) {
            return leaf.childrens.filter( children => {
                return children.tree_name.toLowerCase().indexOf(this.search.toLowerCase()) > -1
            } )
        },
        illustration(item) {
            window.location.href = this.url + '&tree=' + item.id
        }
    }
}
</script>
