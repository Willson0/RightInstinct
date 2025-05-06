<script>
export default {
    name: "NotificationsView",
    data () {
        return {
            notifications: [123,123,],
        }
    },
}
</script>

<template>
    <div class="notifications margin-side">
        <h1 class="notifications_title">Уведомления</h1>
        <div v-if="notifications.length === 0" class="notifications_empty">
            <div class="notifications_empty_title">У вас нет уведомлений</div>
            <div class="notifications_empty_img">
                <img src="/dog.png" alt="">
            </div>
        </div>
        <div v-else class="notifications_main">
            <div v-for="el in 5">
                <h3 class="notifications_el_title">Вашу услугу добавили в избранное</h3>
                <div class="notifications_el_text sign">Пользователь Наталья добавил услугу Натаска вашей собаки в избранное</div>
                <div class="notifications_el_alert"></div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>