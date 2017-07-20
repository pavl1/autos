<template>
    <router-link
        tag="li"
        :to="getAftermarketCatalogLink(item)"
        :class="{ active : active == item.mfa_id }"
        @click.native="setModel(item.mfa_id)"
        >
        <a class="mark-link">
            Заменители {{  item.mfa_brand }}
        </a>
    </router-link>
</template>

<script>

export default {
    data() {
        return {}
    },
    props: [ 'item', 'active' ],
    methods: {
        setModel(id) { this.$emit('update:active', id) },
        getAftermarketCatalogLink: function(mark) {
            return '/td/' + mark.mfa_id.toLowerCase();
        }
    }
}

</script>
