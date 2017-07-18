<template>
    <div class="sites">
        {{ perPage }}
    </div>
</template>

<script>
import Vue from 'vue'
export default {
    data() {
        return {
            perPage: 20,
            pageNum: 1,
            sites: [],
            totalResults: null,
            numPages: null,
        }
    },
}
</script>
