<template>
    <div class="groups">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите группу">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else>
                <thead>
                    <tr>
                        <th>Группа</th>
                        <th>Название</th>
                    </tr>
                </thead>
                <tbody>
                        <router-link
                            tag="tr"
                            :to="'/nissan/' + [ mark, model, market, modification, group.Group ].join('/')"
                            v-for="group in groups"
                        >
                            <td>{{ group.Group }}</td>
                            <td>{{ group.GroupName }}</td>
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
            groups: {},
            search: '',
            oid: {
                catalog: 'nissan',
                mark: this.mark,
                model: this.model,
                market: this.market,
                modification: this.modification,
            }
        }
    },
    props: [ 'mark', 'model', 'market', 'modification' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('nissan_groups', { data: { oid: this.oid } }).then( response => {
                this.groups = response.items
                this.isLoading = false
            })
        }
    },
    computed: {
        filtered() {
            return this.groups.filter( (item) => {
                return item.text.toLowerCase().indexOf(this.search) > -1
            } )
        }
    }
}
</script>
