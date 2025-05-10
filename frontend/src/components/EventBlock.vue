<script>
export default {
    name: "EventBlock",
    props: {
        event: {
            type: Object,
            required: true
        }
    }
}
</script>

<template>
    <div class="event">
        <img :src="event.pictures[0].url" alt="">
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
            <div class="sign">{{ event.date }}</div>
        </div>
    </div>
</template>

<style scoped>

</style>