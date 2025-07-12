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
import {endLoading, toLink} from "@/utils.js";
import ShareView from "@/views/ShareView.vue";

export default {
    name: "MainView",
    data () {
        return {
            queryHistory: [],
            isGoingBack: false,
            firstLoading: true,
            touch: false,
            backFuntion: false,
        }
    },
    components: {
        ShareView,
        UpdateView, EventsView, MyRatingsView,
        MyServicesView, FavouriteView,
        PostsView, ChatView, HomeView,
        MyPostsView, StorePost, NavComponent,
        MyEventsView, ProfileView,
        MySubscriptionsView, UserView,
        NotificationsView, DialogView,
    },
    async mounted () {
        window.Telegram.WebApp.expand();
        if (window.Telegram.WebApp.initDataUnsafe.start_param) {
            const params = window.Telegram.WebApp.initDataUnsafe.start_param.split("_");
            window.Telegram.WebApp.initDataUnsafe.start_param = undefined;

            if (/^[0-9]+$/.test(params[1]) && Number(params[1]) >= 0)  {
                if (params[0] === "user") toLink("user", params[1])
                else if (["post", "event", "service"].includes(params[0])) toLink("share", params[1], params[0])
            } else this.$router.push({ query: { s: 'home' }});
        }
        else if (!this.$route.query.s) this.$router.push({ query: { s: 'home' }});

        this.fetchData();
        setInterval (() => {
            this.fetchData();
        }, 2000);

        window.Telegram.WebApp.BackButton.onClick(this.backByQuery);
        window.backByQueryFunction = this.backByQuery;

        window.addEventListener("touchstart", () => this.touch = true);
        // window.addEventListener("touchend", () => this.touch = false);

        this.hideFooter();
    },
    watch: {
        $route(to, from) {
            clearInterval(this.$store.state.interval);
            this.$store.dispatch("updateInterval", null);

            this.$nextTick(() => this.hideFooter())
        },
        '$route.query' (to, from) {
            this.$nextTick(() => this.hideFooter())

            console.log(to);
            if (to.backfunction === '1') {
                this.backFuntion = true;

                let query = {...this.$route.query};
                delete query.backfunction;
                return this.$router.push({ query: query});
            }
            if (this.backFuntion === true) {
                window.Telegram.WebApp.BackButton.offClick();

                window.Telegram.WebApp.BackButton.onClick(this.backByQuery);
                return this.backFuntion = false;
            }

            document.body.style.overflow = "";
            if (this.isGoingBack === true) {
                this.isGoingBack = false;
                return;
            }
            if (from.s === undefined) return;

            if (to.needback === "1" || to.needback == undefined || to.needback == null) {
                console.log("ZAPISANO");
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
            console.log("VOZVRASHENIE");
            console.log(this.queryHistory);
            if (this.queryHistory.length > 0) {
                this.isGoingBack = true;

                const prevQuery = this.queryHistory.pop();
                this.$router.push({ query: prevQuery });

                if (this.queryHistory.length === 0) window.Telegram.WebApp.BackButton.hide();
            } else {
                this.$router.push({ query: {s: 'home'} });
            }
        },
        hideFooter () {
            console.log("hideFooter function")
            document.querySelectorAll("input").forEach((el) => {
                let footer = document.querySelector(".footer");
                el.addEventListener("focus", () => {
                    console.log("focus input")
                    if (this.touch) {
                        console.log("touch input")
                        footer.style.display = "none";

                        let dialog = document.querySelector(".dialog")
                        if (dialog) dialog.style.height = "calc(100vh - 10px)";
                        document.querySelector(".nav").style.paddingBottom = "0px"
                    }
                });
                el.addEventListener("blur", () => {
                    console.log("blur input")
                    footer.style.display = "";
                    document.querySelector(".dialog").style.height = "";
                    document.querySelector(".nav").style.paddingBottom = "";
                });
            })
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
        <share-view v-if="$route.query.s === 'share'" />
    </nav-component>
</template>

<style scoped>

</style>