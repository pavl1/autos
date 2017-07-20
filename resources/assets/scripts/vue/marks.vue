<template>
    <div class="marks">
        <h2>Восток</h2>
            <ul class="mark-list">
                <li class="mark-item" v-for="mark in marks.east">
                    <img class="mark-image" :src="'/app/themes/autos/dist/images/' + mark.name.toLowerCase() + '.png'" alt="">
                    <span class="mark-name">{{ mark.name.toLowerCase() }}</span>

                    <ul class="mark-link-list">
                        <router-link v-if="mark.original" tag="li" :to="getOriginalCatalogLink(mark.original)" @click="setModel()" >
                            <a class="mark-link">Оригиналы</a>
                        </router-link>
                        <router-link tag="li" :to="getAftermarketCatalogLink(amark)" v-if="mark.aftermarket" v-for="amark in mark.aftermarket">
                            <a class="mark-link">
                                Заменители {{  amark.mfa_brand }}
                            </a>
                        </router-link>
                    </ul>
                </li>
            </ul>
        <h2>Запад</h2>
        <ul class="mark-list">
            <li class="mark-item" v-for="mark in marks.west">
                <img class="mark-image" :src="'/app/themes/autos/dist/images/' + mark.name.toLowerCase() + '.png'" alt="">
                <span class="mark-name">{{ mark.name.toLowerCase() }}</span>

                <ul class="mark-link-list">
                    <router-link v-if="mark.original" tag="li" :to="getOriginalCatalogLink(mark.original)" >
                        <a class="mark-link">Оригиналы</a>
                    </router-link>
                    <router-link tag="li" :to="getAftermarketCatalogLink(amark)" v-if="mark.aftermarket" v-for="amark in mark.aftermarket">
                        <a class="mark-link">
                            Заменители {{  amark.mfa_brand }}
                        </a>
                    </router-link>
                </ul>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            marks: window.marks
        }
    },
    methods: {
        getOriginalCatalogLink: function(mark) {
            if ( mark.route ) return '/' + mark.route
            return '/adc/' + mark.mark_id.toLowerCase()
        },
        getAftermarketCatalogLink: function(mark) {
            return '/td/' + mark.mfa_id.toLowerCase();
        }
    }
}
</script>
