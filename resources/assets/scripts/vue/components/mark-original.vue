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
            if ( mark.route ) return '/' + mark.route
            return '/adc/' + mark.mark_id.toLowerCase()
        },
    }
}
</script>
