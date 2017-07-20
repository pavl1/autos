import Vue from 'vue'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import Marks from '../vue/marks.vue'
import Series from '../vue/series.vue'

Vue.use(VueRouter)
Vue.use(Vuex)
const router = new VueRouter({
    routes: [
        { path: '/bmw', component: Series },
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
