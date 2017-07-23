import Vue from 'vue'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import Marks from '../vue/marks.vue'

import BMWSeries from '../vue/bmw/series.vue'
import BMWModels from '../vue/bmw/models.vue'
import BMWOptions from '../vue/bmw/options.vue'
import BMWProduction from '../vue/bmw/production.vue'

Vue.use(VueRouter)
Vue.use(Vuex)
const router = new VueRouter({
    routes: [
        // { path: '/adc/:cat', component: Models },
        // { path: '/td/:cat', component: Models },
        { path: '/bmw/:series/:body/:model/:market/:rule/:transmission', component: BMWProduction, props: true },
        { path: '/bmw/:series/:body/:model/:market', component: BMWOptions, props: true },
        { path: '/bmw/:series', component: BMWModels, props: true },
        { path: '/bmw', component: BMWSeries },
        { path: '/', component: Marks },
    ],
})
import state from '../vue/vuex/state.js'
import getters from '../vue/vuex/getters.js'
import actions from '../vue/vuex/actions.js'
import mutations from '../vue/vuex/mutations.js'

const store = new Vuex.Store({
    state,
    getters,
    actions,
    mutations,
})
export default {
    init() {
        // JavaScript to be fired on the home page
        new Vue({
            el: "#app",
            router,
            store,
        })
    },
}
