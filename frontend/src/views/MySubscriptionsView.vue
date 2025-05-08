<script>
import UserBlock from "@/components/UserBlock.vue";
import {toLink} from "@/utils.js";

export default {
    name: "MySubscriptionsView",
    methods: {toLink},
    components: {UserBlock},
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
}
</script>

<template>
    <div class="mysubscriptions margin-all">
        <h1>Мои подписки</h1>
        <div class="mysubscriptions_main">
            <div v-for="us in user.subscriptions">
                <UserBlock :user="us.user_subscription" />
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>