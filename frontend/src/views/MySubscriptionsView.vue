<script>
import UserBlock from "@/components/UserBlock.vue";

export default {
    name: "MySubscriptionsView",
    components: {UserBlock}
}
</script>

<template>
    <div class="mysubscriptions margin-all">
        <h1>Мои подписки</h1>
        <div class="mysubscriptions_main">
            <div v-for="el in 10">
                <UserBlock name="Василий" city="Москва" rating="4,6" avatar="/avatar_1.png" />
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>