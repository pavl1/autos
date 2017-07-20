<template>
    <ul class="mark-list">
        <li class="mark-item" v-for="mark in marks">
            <img class="mark-image" :src="'/app/themes/autos/dist/images/' + mark.name.toLowerCase() + '.png'" alt="">
            <span class="mark-name">{{ mark.name.toLowerCase() }}</span>
            <ul class="mark-link-list">
                <router-link v-if="mark.original" tag="li" :to="'/' + mark.original.route" >
                    <a class="mark-link">Оригиналы</a>
                </router-link>
                <router-link tag="li" to="/td" v-if="mark.aftermarket" v-for="cat in mark.aftermarket">
                    <a class="mark-link">
                        Заменители {{  cat.mfa_brand }}
                    </a>
                </router-link>
            </ul>
        </li>
    </ul>
</template>

<script>
export default() {
    data(): {

    }
}

</script>
