<template>
    <div class="marks">
        <h2>Восток</h2>
        <ul class="mark-list">
            <li class="mark-item" v-for="mark in marks.east">
                <img class="mark-image" :src="'/app/themes/autos/dist/images/' + mark.name.toLowerCase() + '.png'" alt="">
                <span class="mark-name">{{ mark.name.toLowerCase() }}</span>

                <ul class="mark-link-list">
                    <mark-original :item="mark.original"></mark-original>
                    <mark-aftermarket :item="item" v-for="item in mark.aftermarket"></mark-aftermarket>
                </ul>
            </li>
        </ul>
        <h2>Запад</h2>
        <ul class="mark-list">
            <li class="mark-item" v-for="mark in marks.west">
                <img class="mark-image" :src="'/app/themes/autos/dist/images/' + mark.name.toLowerCase() + '.png'" alt="">
                <span class="mark-name">{{ mark.name.toLowerCase() }}</span>

                <ul class="mark-link-list">
                    <mark-original :item="mark.original"></mark-original>
                    <mark-aftermarket :item="item" v-for="item in mark.aftermarket"></mark-aftermarket>
                </ul>
            </li>
        </ul>
    </div>
</template>

<script>
import markOriginal from './components/mark-original.vue'
import markAftermarket from './components/mark-aftermarket.vue'

export default {
    data() {
        return {
            marks: window.marks
        }
    },
    components: { markOriginal, markAftermarket },
}
</script>
