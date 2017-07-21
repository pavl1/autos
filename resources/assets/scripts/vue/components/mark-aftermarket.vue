<template>
    <router-link tag="li" :to="getAftermarketCatalogLink(item)">
        <a class="mark-link"> Заменители {{  item.mfa_brand }} </a>
    </router-link>
</template>

<script>
export default {
    data() {
        return {}
    },
    props: [ 'item' ],
    methods: {
        getAftermarketCatalogLink: function(mark) {
            return '/td/' + mark.mfa_id.toLowerCase();
        }
    }
}

</script>
