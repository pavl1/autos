<template>
    <div class="groups">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите группу">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <div v-else>
                <table v-for="(section, index) in groups" v-if="filteredGroups(section).length">
                    <thead>
                        <tr>
                            <th>{{ sectionName(index) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr v-for="group in filteredGroups(section)" @click="illustration(group)">
                                <td>{{ group.desc_en }}</td>
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
            groups: {},
            search: '',
            url: '',
            getString: '',
            oid: {
                catalog: 'toyota',
                mark: this.mark,
                model: this.model,
                market: this.market,
                compl: this.compl,
                option: this.option,
                code: this.code
            }
        }
    },
    props: [ 'mark', 'model', 'market', 'compl', 'option', 'code' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('toyota_groups', { data: { oid: this.oid } }).then( response => {
                this.groups = response.items
                this.url = response.url
                this.getString = response.getString
                this.isLoading = false
            })
        },
        sectionName(index) {
            let section = ''
            switch (index) {
                case '1':
                    section = 'Двигатель, топливная система и инструменты'
                    break
                case '2':
                    section = 'Трансмиссия и шасси'
                    break
                case '3':
                    section = 'Кузов и салон'
                    break
                case '4':
                    section = 'Электрика'
                    break
            }
            return section
        },
        filteredGroups(section) {
            return section.filter( group => {
                return group.desc_en.toLowerCase().indexOf(this.search) > -1
            } )
        },
        illustration(group) {
            window.location.href = this.url + '&group=' + group.part_group + '&graphic=' + group.pi_code + this.getString
        }
    }

}
</script>
