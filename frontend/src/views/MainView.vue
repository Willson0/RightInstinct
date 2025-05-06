<script>
import NavComponent from "@/components/NavComponent.vue";
import HomeView from "@/views/HomeView.vue";
import StorePost from "@/views/StorePost.vue";
import StoreService from "@/views/StoreService.vue";
import ProfileView from "@/views/ProfileView.vue";
import ChatView from "@/views/ChatView.vue";
import DialogView from "@/views/DialogView.vue";
import NotificationsView from "@/views/NotificationsView.vue";
import MySubscriptionsView from "@/views/MySubscriptionsView.vue";
import MyEventsView from "@/views/MyEventsView.vue";
import axios from 'axios';
import config from "@/config.json"

export default {
    name: "MainView",
    components: {
        MyEventsView,
        MySubscriptionsView,
        NotificationsView, DialogView, ChatView, ProfileView, StoreService, StorePost, NavComponent, HomeView},
    async mounted () {
        if (!this.$route.query.s) this.$router.push({ query: { s: 'home' }});

        await axios.post(config.backend + "auth/profile", {
            "initData": window.Telegram.WebApp.initData,
        }).then((response) => {
            this.$store.dispatch("updateUser", response.data);
        }).catch((error) => {
            if (error.response) {
                return alert (`An error occurred: ${error.message}`);
            }
        });
    },
    watch: {
        $route(to, from) {
            clearInterval(this.$store.state.interval);
            this.$store.dispatch("updateInterval", null);
        }
    }
}
</script>

<template>
    <div class="notification_container"></div>
    <nav-component>
        <home-view v-if="$route.query.s === 'home'" />
        <store-post v-if="$route.query.s === 'store_post'" />
        <store-service v-if="$route.query.s === 'store_service'" />
        <profile-view v-if="$route.query.s === 'profile'" />
        <chat-view v-if="$route.query.s === 'chat'" />
        <dialog-view v-if="$route.query.s === 'dialog'" />
        <notifications-view v-if="$route.query.s === 'notifications'" />
        <my-subscriptions-view v-if="$route.query.s === 'mysubscriptions'" />
        <my-events-view v-if="$route.query.s === 'myevents'" />
    </nav-component>
<!--    123-->
</template>

<style scoped>

</style>