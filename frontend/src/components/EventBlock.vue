<script>
export default {
    name: "EventBlock",
    props: {
        title: {
            type: String,
            required: true,
        },
        description: {
            type: String,
            required: true,
        },
        image: {
            type: String,
            required: true,
        },
        location: {
            type: String,
            required: true,
        },
        date: {
            type: String,
            required: true
        }
    }
}
</script>

<template>
    <div class="event">
        <img :src="image" alt="">
        <div class="event_info">
            <h4>{{ title }}</h4>
            <div class="event_description sign">{{ description }}</div>
        </div>
        <div class="event_location">
            <img src="/location.svg" alt="">
            <div class="sign">{{ location }}</div>
        </div>
        <div class="event_calendar">
            <img src="/calendar.svg" alt="">
            <div class="sign">{{ date }}</div>
        </div>
    </div>
</template>

<style scoped>

</style>