<script>
import EventBlock from "@/components/EventBlock.vue";
import {toLink} from "@/utils.js";

export default {
    name: "MyEventsView",
    methods: {toLink},
    components: {EventBlock},
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
}
</script>

<template>
    <div class="myevents margin-all">
        <h1>Мои мероприятия</h1>
        <div v-if="user.my?.events.filter(el => el.moderated).length" class="myevents_accepted">
            <h3>Одобрены</h3>
            <div class="myevents_main">
                <event-block :my="true" :event="event" v-for="event in user.my?.events.filter(el => el.moderated)"/>
            </div>
        </div>
        <div v-if="user.my?.events.filter(el => !el.moderated).length" class="myevents_moderated">
            <h3>На модерации</h3>
            <div class="myevents_main">
                <event-block :my="true" :event="event" v-for="event in user.my?.events.filter(el => !el.moderated)"/>
            </div>
        </div>
        <div @click="toLink('store', 'event')" class="home_block_button green-bgc button">
            <div>
                <img src="/plus.svg" alt="">
                <div class="button">Добавить мероприятие</div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>