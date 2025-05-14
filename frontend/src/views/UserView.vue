<script>
import UserBlock from "@/components/UserBlock.vue";
import config from "@/config.json";
import axios from "axios"
import PostBlock from "@/components/PostBlock.vue";
import {complain, favourite, notify, toLink} from "@/utils.js";

export default {
    name: "UserView.vue",
    components: {PostBlock, UserBlock},
    data () {
        return {
            us: {},
            isLoading: false,
            type: "user",
            isLoadingLike: {status: false},
            config: config,
        }
    },
    computed: {
        user () {
            return this.$store.state.user;
        },
    },
    async mounted () {
        if (Number(this.$route.query.id) === this.user.id) toLink("profile", null, null, 0);
        else
            await axios.post(config.backend + "user/" + this.$route.query.id, {
                "initData": window.Telegram.WebApp.initData,
            }).then((response) => {
                this.us = response.data;
            }).catch((error) => {
                if (error.response)
                    alert (error.message);
            })
    },
    watch: {
        user () {
            if (Number(this.$route.query.id) === this.user.id) toLink("profile", null, null, 0);
        }
    },
    methods: {
        complain,
        favourite,
        async subscribe (status = 1) {
            if (this.isLoading) return;

            this.isLoading = true;
            await axios.post(config.backend + "subscription/" + (status ? 'subscribe' : 'unsubscribe'), {
                "initData": window.Telegram.WebApp.initData,
                "user_subscription_id": this.us.id,
            }).then((response) => {
                this.us.isSubscribe = !this.us.isSubscribe;
                notify(`Вы успешно ${status ? "подписались" : "отписались"} на ${this.us.fullname}!`);

                axios.post(config.backend + "auth/profile", {
                    "initData": window.Telegram.WebApp.initData,
                }).then((resp) => {
                    this.$store.dispatch("updateUser", resp.data);
                })
            }).catch((error) => {
                if (error.response)
                    notify(error.message, 1);
            }).finally(() => {
                this.isLoading = false;
            })
        },
    }
}
</script>

<template>
    <div class="user_index margin-all">
        <user-block v-if="us" :user="us" />
        <div v-if="us.posts && us.posts?.length !== 0" class="user_posts">
            <h2>Объявления</h2>
            <div>
                <post-block :clickable="false" v-for="post in us.posts" :object="post" />
            </div>
        </div>
        <div v-if="us?.services?.length !== 0" class="user_services">
            <h2>Услуги</h2>
            <div>
                <post-block :clickable="false" v-for="post in us?.services" :object="post" />
            </div>
        </div>
        <div class="user_buttons">
            <button v-if="us.isSubscribe" class="button" @click="subscribe(0)">Отписаться</button>
            <button v-else class="button" @click="subscribe(1)">Подписаться</button>
            <button v-if="user" @click.stop="favourite(!user?.favourites[type]?.includes(us.id), type, us.id, isLoadingLike, user)">
                <img v-if="user && user.favourites && user.favourites[type] && user?.favourites[type]?.includes(us.id)"
                     src="/like_active.svg"  alt="">
                <img v-else src="/like.svg" style="width: 32px; height: 32px;" alt="">
            </button>
            <button><img src="/share.svg" alt=""></button>
        </div>
<!--        <a :href="config.complain" class="postOverlay_main_complain">Пожаловаться</a>-->
        <a @click="complain('user', us.id)" class="postOverlay_main_complain">Пожаловаться</a>
    </div>
</template>

<style scoped>

</style>