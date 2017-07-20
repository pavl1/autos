<template>
    <router-link
        v-if="item"
        tag="li"
        :to="getOriginalCatalogLink(item)"
        :class="{ active : active == item.mark_id }"
        @click.native="setModel(item.mark_id)"
    >
        <a class="mark-link"
        >Оригиналы</a>
    </router-link>
</template>

<script>
export default {
    data() {
        return {}
    },
    props: [ 'item', 'active' ],
    methods: {
        setModel(id) {
            console.log(id)
            this.$emit('update:active', id)
        },
        getOriginalCatalogLink: function(mark) {
            if ( mark.route ) return '/' + mark.route
            return '/adc/' + mark.mark_id.toLowerCase()
        },
    }
}
</script>
