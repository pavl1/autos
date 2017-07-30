<template>
    <div class="subgroups">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите подгруппу">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <table v-else>
                <thead>
                    <tr>
                        <th>№ Фигуры</th>
                        <th>Название</th>
                    </tr>
                </thead>
                <tbody>
                        <tr v-for="subgroup in filtered" @click="illustration(subgroup.figure)">
                            <td>{{ subgroup.figure }}</td>
                            <td>{{ subgroup.PName }}</td>
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
            subgroups: {},
            url: '',
            search: '',
            oid: {
                catalog: 'nissan',
                mark: this.mark,
                model: this.model,
                market: this.market,
                modification: this.modification,
                group: this.group
            }
        }
    },
    props: [ 'mark', 'model', 'market', 'modification', 'group' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('nissan_subgroups', { data: { oid: this.oid } }).then( response => {
                this.subgroups = response.items
                this.url = response.url
                this.isLoading = false
            })
        },
        illustration(figure) {
            window.location.href = this.url + '&figure=' + figure
        }
    },
    computed: {
        filtered() {
            return this.subgroups.filter( (item) => {
                return item.PName.toLowerCase().indexOf(this.search) > -1
            } )
        }
    }
}
</script>
