<script>
import UserBlock from "@/components/UserBlock.vue";
import config from "@/config.json";
import axios from "axios"
import PostBlock from "@/components/PostBlock.vue";

export default {
    name: "UserView.vue",
    components: {PostBlock, UserBlock},
    data () {
        return {
            us: null,
        }
    },
    async mounted () {
        await axios.get(config.backend + "user/" + this.$route.query.id).then((response) => {
            this.us = response.data;
        }).catch((error) => {
            if (error.response)
                alert (error.message);
        })
    },
    methods: {
        async subscribe () {

        }
    }
}
</script>

<template>
    <div class="user margin-all">
        <user-block v-if="us" :user="us" />
        <div v-if="us?.posts?.length !== 0" class="user_posts">
            <h2>Объявления</h2>
            <div>
                <post-block v-for="post in us?.posts" :object="post" />
            </div>
        </div>
        <div v-if="us?.services?.length !== 0" class="user_services">
            <h2>Услуги</h2>
            <div>
                <post-block v-for="post in us?.services" :object="post" />
            </div>
        </div>
        <div class="user_buttons">
            <button class="button">Подписаться</button>
            <button><img src="/like.svg" alt=""></button>
            <button><img src="/share.svg" alt=""></button>
        </div>
        <a href="https://t.me/wilflw" class="postOverlay_main_complain">Пожаловаться</a>
    </div>
</template>

<style scoped>

</style>