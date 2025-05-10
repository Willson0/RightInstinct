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
import MyPostsView from "@/views/MyPostsView.vue";
import PostsView from "@/views/PostsView.vue";
import UserView from "@/views/UserView.vue"
import FavouriteView from "@/views/FavouriteView.vue"

export default {
    name: "MainView",
    data () {
        return {
            queryHistory: [],
            isGoingBack: false,
        }
    },
    components: {
        FavouriteView,
        PostsView, ChatView, HomeView,
        MyPostsView, StorePost, NavComponent,
        MyEventsView, ProfileView, StoreService,
        MySubscriptionsView, UserView,
        NotificationsView, DialogView,
    },
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

        window.Telegram.WebApp.BackButton.onClick(() => this.backByQuery());
    },
    watch: {
        $route(to, from) {
            clearInterval(this.$store.state.interval);
            this.$store.dispatch("updateInterval", null);
        },
        '$route.query' (to, from) {
            if (this.isGoingBack === true) {
                this.isGoingBack = false;
                return;
            }

            this.queryHistory.push(from);
            console.log(this.queryHistory);

            window.Telegram.WebApp.BackButton.show();
        }
    },
    methods: {
        backByQuery() {
            console.log(this.queryHistory);
            if (this.queryHistory.length > 0) {
                this.isGoingBack = true;

                const prevQuery = this.queryHistory.pop();
                this.$router.push({ query: prevQuery });

                if (this.queryHistory.length === 0) window.Telegram.WebApp.BackButton.hide();
            } else {
                this.$router.push({ query: {s: 'home'} });
            }
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
        <my-posts-view v-if="$route.query.s === 'myposts'" />
        <posts-view v-if="$route.query.s === 'posts'" />
        <user-view v-if="$route.query.s === 'user'" />
        <favourite-view v-if="$route.query.s === 'favourite'" />
    </nav-component>
<!--    123-->
</template>

<style scoped>

</style>