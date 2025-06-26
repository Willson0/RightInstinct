<script>
import NavComponent from "@/components/NavComponent.vue";
import HomeView from "@/views/HomeView.vue";
import StorePost from "@/views/StoreView.vue";
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
import MyServicesView from "@/views/MyServicesView.vue";
import MyRatingsView from "@/views/MyRatingsView.vue";
import EventsView from "@/views/EventsView.vue";
import UpdateView from "@/views/UpdateView.vue";
import {endLoading} from "@/utils.js";

export default {
    name: "MainView",
    data () {
        return {
            queryHistory: [],
            isGoingBack: false,
            firstLoading: true,
            touch: false,
        }
    },
    components: {
        UpdateView, EventsView, MyRatingsView,
        MyServicesView, FavouriteView,
        PostsView, ChatView, HomeView,
        MyPostsView, StorePost, NavComponent,
        MyEventsView, ProfileView,
        MySubscriptionsView, UserView,
        NotificationsView, DialogView,
    },
    async mounted () {
        if (!this.$route.query.s) this.$router.push({ query: { s: 'home' }});

        this.fetchData();
        setInterval (() => {
            this.fetchData();
        }, 2000);

        window.Telegram.WebApp.BackButton.onClick(() => this.backByQuery());


        window.addEventListener("touchstart", () => this.touch = true);
        // window.addEventListener("touchend", () => this.touch = false);

        document.querySelectorAll("input").forEach((el) => {
            let footer = document.querySelector(".footer");
            el.addEventListener("focus", () => {
                if (this.touch) footer.style.display = "none";
            });
            el.addEventListener("blur", () => {
                footer.style.display = "";
            });
        })
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

            if (to.needback === "1" || to.needback == null) {
                this.queryHistory.push(from);
            }
            console.log(this.queryHistory);

            window.Telegram.WebApp.BackButton.show();
        }
    },
    methods: {
        async fetchData () {
            axios.post(config.backend + "auth/profile", {
                "initData": window.Telegram.WebApp.initData,
            }).then((response) => {
                this.$store.dispatch("updateUser", response.data);
                if (this.firstLoading) {
                    this.firstLoading = false;
                    endLoading();
                }
            }).catch((error) => {
                if (error.response) {
                    return alert (`An error occurred: ${error.message}`);
                }
            });
        },
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
    <div class="loading"></div>
    <div class="notification_container"></div>
    <nav-component>
        <home-view v-if="$route.query.s === 'home'" />
        <store-post v-if="$route.query.s === 'store'" />
        <profile-view v-if="$route.query.s === 'profile'" />
        <chat-view v-if="$route.query.s === 'chat'" />
        <dialog-view v-if="$route.query.s === 'dialog'" />
        <notifications-view v-if="$route.query.s === 'notifications'" />
        <my-subscriptions-view v-if="$route.query.s === 'mysubscriptions'" />
        <my-events-view v-if="$route.query.s === 'myevents'" />
        <my-posts-view v-if="$route.query.s === 'myposts'" />
        <my-services-view v-if="$route.query.s === 'myservices'" />
        <my-ratings-view v-if="$route.query.s === 'myratings'" />
        <posts-view v-if="$route.query.s === 'posts'" />
        <events-view v-if="$route.query.s === 'events'" />
        <user-view v-if="$route.query.s === 'user'" />
        <favourite-view v-if="$route.query.s === 'favourite'" />
        <update-view v-if="$route.query.s === 'update'" />
    </nav-component>
<!--    123-->
</template>

<style scoped>

</style>