<script>
import config from "@/config.json"
export default {
    name: "EventBlock",
    data () {
        return {
            config: config,
        }
    },
    props: {
        event: {
            type: Object,
            required: true
        }
    },
    computed: {
        beautifullyDate () {
            let startDate = new Date(this.event.start_date);
            let endDate = new Date(this.event.end_date);

            const months = ["января","февраля","марта","апреля","мая","июня","июля","августа",
                "сентября","октября","ноября","декабря"]

            if (startDate.getYear() === endDate.getYear())
                return (String(startDate.getDate()).padStart(2, "0") + " "
                    + months[startDate.getMonth()] + " - "
                    + String(endDate.getDate()).padStart(2, "0")
                    + " " + months[startDate.getMonth()]
                    + " " + String(endDate.getFullYear()).padStart(2,"0")) + " г."
        },
    }
}
</script>

<template>
    <div class="event">
        <img v-if="event.pictures[0]" :src="config.storage + event?.pictures[0].url" alt="">
        <div class="event_info">
            <h4>{{ event.title }}</h4>
            <div class="event_description sign">{{ event.description }}</div>
        </div>
        <div class="event_location">
            <img src="/location.svg" alt="">
            <div class="sign">{{ event.city.name }}</div>
        </div>
        <div class="event_calendar">
            <img src="/calendar.svg" alt="">
            <div class="sign">{{ beautifullyDate }}</div>
        </div>
    </div>
</template>

<style scoped>

</style>