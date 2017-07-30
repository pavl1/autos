<template>
    <div class="models">
        <input readonly class="instant-search" type="text" name="" v-model="search" placeholder="Выберите деталь">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Артикул</th>
                        <th>Производитель</th>
                        <th>Название</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="series-link" v-for="item in filteredDetails">
                        <td>{{ item.art_article_nr }}</td>
                        <td>{{ item.brandName }}</td>
                        <td>{{ item.ga_des }}</td>
                        <td><a :href="'/search/' + item.art_article_nr">Цена</a></td>
                    </tr>
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
            details: {},
            search: '',
            oid: {
                catalog: 'td',
                mark: this.mark,
                model: this.model,
                equipment: this.equipment,
                tree: this.tree
            }
        }
    },
    props: [ 'mark', 'model', 'equipment', 'tree' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('td_details', { data: { oid: this.oid } }).then( response => {
                this.details = response.items
                this.isLoading = false
            })
        },
        illustration(item) {
            window.location.href = '/search/' + item.art_id
        }
    },
    computed: {
        filteredDetails() {
            return this.details.filter( item => {
                return [ item.ga_des, item.art_article_nr, item.brandName ].join().toLowerCase().indexOf(this.search.toLowerCase()) > -1
            } )
        }
    }
}
</script>
