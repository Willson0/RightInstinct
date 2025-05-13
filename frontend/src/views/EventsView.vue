<script>
import EventBlock from "@/components/EventBlock.vue";
import {toLink} from "@/utils.js";
import config from "@/config.json";
import axios from "axios";

export default {
    name: "EventsView",
    methods: {toLink},
    components: {EventBlock},
    data () {
        return {
            events: [],
        }
    },
    async mounted () {
        await axios.get(config.backend + "event").then((response) => {
            this.events = response.data;
        }).catch((error) => {
            if (error.response)
                alert (error.message);
        });
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
}
</script>

<template>
    <div class="myevents margin-all">
        <h1>Мероприятия</h1>
        <div class="myevents_accepted">
            <div class="myevents_main">
                <event-block :event="event" v-for="event in events"/>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>