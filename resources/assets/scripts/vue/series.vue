<template>
    <div class="series">

        <input class="series-search" type="text" name="" value="" placeholder="Выберите / введите модель автомобиля">

        <div class="series-container">
            <div class="series-header">
                <span class=col>Модель</span>
                <span class=col>Серия</span>
            </div>
        </div>

    </div>
</template>

<script>
let wp = window.wp

import { mapGetters } from 'vuex'

export default {
    data() {
        return {
            marks: window.marks
        }
    },
    beforeRouteEnter (to, from, next) {
        wp.ajax.send('get_series').then()
    },
    computed: {
        ...mapGetters([ 'getOid' ]),
    }

}
</script>
