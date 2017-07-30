import Vue from 'vue'
import VueRouter from 'vue-router'
import Marks from '../vue/marks.vue'

import BMWSeries from '../vue/bmw/series.vue'
import BMWModels from '../vue/bmw/models.vue'
import BMWOptions from '../vue/bmw/options.vue'
import BMWProduction from '../vue/bmw/production.vue'
import BMWGroups from '../vue/bmw/groups.vue'
import BMWSubgroups from '../vue/bmw/subgroups.vue'

import ETKAModels from '../vue/etka/models.vue'
import ETKAProduction from '../vue/etka/production.vue'
import ETKAGroups from '../vue/etka/groups.vue'
import ETKASubgroups from '../vue/etka/subgroups.vue'

import NissanModels from '../vue/nissan/models.vue'
import NissanModifications from '../vue/nissan/modifications.vue'
import NissanGroups from '../vue/nissan/groups.vue'
import NissanSubgroups from '../vue/nissan/subgroups.vue'

import ToyotaModels from '../vue/toyota/models.vue'
import ToyotaOptions from '../vue/toyota/options.vue'
import ToyotaGroups from '../vue/toyota/groups.vue'

import ADCModels from '../vue/adc/models.vue'
import ADCTree from '../vue/adc/tree.vue'

import TDModels from '../vue/td/models.vue'
import TDEquipment from '../vue/td/equipment.vue'
import TDTree from '../vue/td/tree.vue'
import TDDetails from '../vue/td/details.vue'

Vue.use(VueRouter)
const router = new VueRouter({
    routes: [
        { path: '/td/:mark/:model/:equipment/:tree', component: TDDetails, props: true },
        { path: '/td/:mark/:model/:equipment', component: TDTree, props: true },
        { path: '/td/:mark/:model', component: TDEquipment, props: true },
        { path: '/td/:mark/', component: TDModels, props: true },

        { path: '/adc/:mark/:model', component: ADCTree, props: true },
        { path: '/adc/:mark', component: ADCModels, props: true },

        { path: '/toyota/:mark/:model/:market/:compl/:option/:code', component: ToyotaGroups, props: true },
        { path: '/toyota/:mark/:model/:market', component: ToyotaOptions, props: true },
        { path: '/toyota/:mark', component: ToyotaModels, props: true },

        { path: '/nissan/:mark/:model/:market/:modification/:group', component: NissanSubgroups, props: true },
        { path: '/nissan/:mark/:model/:market/:modification', component: NissanGroups, props: true },
        { path: '/nissan/:mark/:model/:market', component: NissanModifications, props: true },
        { path: '/nissan/:mark', component: NissanModels, props: true },

        { path: '/etka/:mark/:market/:model/:production/:code/:dir/:type/:group', component: ETKASubgroups, props: true },
        { path: '/etka/:mark/:market/:model/:production/:code/:dir', component: ETKAGroups, props: true },
        { path: '/etka/:mark/:market/:model', component: ETKAProduction, props: true },
        { path: '/etka/:mark', component: ETKAModels, props: true },

        { path: '/bmw/:mark/:series/:body/:model/:market/:rule/:transmission/:production/:group', component: BMWSubgroups, props: true },
        { path: '/bmw/:mark/:series/:body/:model/:market/:rule/:transmission/:production', component: BMWGroups, props: true },
        { path: '/bmw/:mark/:series/:body/:model/:market/:rule/:transmission', component: BMWProduction, props: true },
        { path: '/bmw/:mark/:series/:body/:model/:market', component: BMWOptions, props: true },
        { path: '/bmw/:mark/:series', component: BMWModels, props: true },
        { path: '/bmw/:mark', component: BMWSeries, props: true },
        { path: '/', component: Marks },
    ],
})
export default {
    init() {
        new Vue({
            el: "#app",
            router,
        })
    },
}
