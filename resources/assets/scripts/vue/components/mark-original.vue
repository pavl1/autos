<template>
    <router-link v-if="item" tag="li" :to="getOriginalCatalogLink(item)">
        <a class="mark-link">Оригиналы</a>
    </router-link>
</template>

<script>
export default {
    data() {
        return {}
    },
    props: [ 'item' ],
    methods: {
        getOriginalCatalogLink: function(mark) {
            if ( mark.catalog == 'adc' ) return '/adc/' + mark.mark_id.toLowerCase()
            return '/' + [ mark.catalog, mark.route].join('/')

        },
    }
}
</script>
