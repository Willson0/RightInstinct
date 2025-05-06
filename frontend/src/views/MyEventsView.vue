<script>
import EventBlock from "@/components/EventBlock.vue";
import {toLink} from "@/utils.js";

export default {
    name: "MyEventsView",
    methods: {toLink},
    components: {EventBlock}
}
</script>

<template>
    <div class="myevents margin-all">
        <h1>Мои мероприятия</h1>
        <div class="myevents_accepted">
            <h3>Одобрены</h3>
            <div class="myevents_main">
                <event-block title="Выставка охотничьих собак" date="24 февраля - 2 марта 2025 г."
                             location="Санкт-Петербург" image="/event.png" v-for="el in 5"
                             description="Городская выставка охотничьих собак легавых пород"/>
            </div>
        </div>
        <div class="myevents_moderated">
            <h3>На модерации</h3>
            <div class="myevents_main">
                <event-block title="Выставка охотничьих собак" date="24 февраля - 2 марта 2025 г."
                             location="Санкт-Петербург" image="/event.png" v-for="el in 2"
                             description="Городская выставка охотничьих собак легавых пород"/>
            </div>
        </div>
        <div @click="toLink('store_event')" class="home_block_button green-bgc button margin-side">
            <div>
                <img src="/plus.svg" alt="">
                <div class="button">Добавить мероприятие</div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>