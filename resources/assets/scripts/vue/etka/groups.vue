<template>
    <div class="groups">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите группу">

        <transition name="slide-fade" mode="out-in">
            <spinner v-if="isLoading"></spinner>
            <ul v-else class="groups-list">
                <li class="groups-item" v-for="group in filtered">
                    <router-link :to="'/etka/' + [ mark, market, model, production, code, dir, oid.type, group.hg ].join('/')">
                        {{ group.text }}
                    </router-link>
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
            groups: {},
            search: '',
            oid: {
                catalog: 'etka',
                dir: 'R',
                type: 'G',
                mark: this.mark,
                market: this.market,
                model: this.model,
                production: this.production,
                code: this.code,
                dir: this.dir
            }
        }
    },
    props: [ 'mark', 'market', 'model', 'production', 'code', 'dir' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('etka_groups', { data: { oid: this.oid } }).then( response => {
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
