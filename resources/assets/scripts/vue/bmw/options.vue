<template>
    <div class="options">

        <input class="instant-search" type="text" name="" v-model="search" placeholder="Выберите / введите опцию">

        <spinner v-if="isLoading"></spinner>
        <ul class="options-list" v-else>
            <li class="options-item" v-for="option in filtered">
                <router-link class="options-link" :to="'/bmw/' + [ series, body, model, market, option.RuleCode, option.GetriebeCode ].join('/')">
                    {{ option.RuleName }} / {{ option.GetriebeName }}
                </router-link>
            </li>
        </ul>
    </div>
</template>

<script>
import Spinner from '../components/Spinner.vue'

export default {
    data() {
        return {
            isLoading: true,
            options: {},
            search: '',
            oid: {
                series: this.series,
                body: this.body,
                model: this.model,
                market: this.market
            }
        }
    },
    props: ['series', 'body', 'model', 'market' ],
    components: { Spinner },
    created() { this.fetchData() },
    methods: {
        fetchData() {
            window.wp.ajax.send('get_bmw_options', { data: { oid: this.oid } }).then( response => {
                this.options = response.options
                this.isLoading = false
            })
        }
    },
    computed: {
        filtered() {
            return this.options.filter( item => {
                let rule = item.RuleName.toLowerCase().indexOf(this.search) > -1
                let transmission = item.GetriebeName.toLowerCase().indexOf(this.search) > -1
                return ( rule || transmission )
            } )
        }
    }
}
</script>
