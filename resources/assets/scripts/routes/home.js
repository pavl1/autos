import Vue from 'vue'
import VueRouter from 'vue-router'
import Marks from '../vue/marks.vue'

Vue.use(VueRouter)
const router = new VueRouter({
    routes: [
        { path: '/', component: Marks },
    ],
})
export default {
    init() {
        // JavaScript to be fired on the home page
        new Vue({
            el: "#app",
            router,
        })
    },
}
