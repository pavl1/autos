<template>
    <div class="subgroups">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите подгруппу">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <ul v-else class="subgroups-list">
                <li class="subgroups-item" v-for="subgroup in filtered">
                    <a :href="url + '&subgroup=' + subgroup.hg_ug + '&graphic=' + subgroup.bildtafel2">
                        {{ subgroup.tsben_text }}
                        {{ subgroup.tsmoa_text }}
                    </a>
                </li>
            </ul>
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
                catalog: 'etka',
                mark: this.mark,
                market: this.market,
                model: this.model,
                production: this.production,
                code: this.code,
                dir: this.dir,
                type: this.type,
                group: this.group
            }
        }
    },
    props: [ 'mark', 'market', 'model', 'production', 'code', 'dir', 'type', 'group' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('etka_subgroups', { data: { oid: this.oid } }).then( response => {
                this.subgroups = response.items
                this.url = response.url
                this.isLoading = false
            })
        }
    },
    computed: {
        filtered() {
            return this.subgroups.filter( (item) => {
                return item.tsben_text.toLowerCase().indexOf(this.search) > -1
            } )
        }
    }
}
</script>
