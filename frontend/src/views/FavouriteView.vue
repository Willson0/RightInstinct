<script>
import UserBlock from "@/components/UserBlock.vue";
import PostBlock from "@/components/PostBlock.vue";
import EventBlock from "@/components/EventBlock.vue";
import config from "@/config.json";
import axios from "axios";
import {endLoading} from "@/utils.js";

export default {
    name: "FavouriteView.vue",
    components: {EventBlock, PostBlock, UserBlock},
    data () {
        return {
            data: {},
        }
    },
    computed: {
        user () {
            return this.$store.state.user;
        },
    },
    async mounted () {
        await axios.post(config.backend + "favourite/index", {
            initData: window.Telegram.WebApp.initData,
        }).then((response) => {
            this.data = response.data;
            endLoading ("loading_favourite");
        }).catch((error) => {
            if (error.response) {
                return alert (`An error occurred: ${error.message}`);
            }
        });
    }
}
</script>

<template>
    <div class="loading loading_favourite"></div>
    <div class="favourite">
        <h1>Избранное</h1>
        <div v-if="!data.user?.length && !data.post?.length && !data.service?.length && !data.event?.length"
             class="posts_main_nothing">Тут пока что ничего нет...</div>
        <div v-if="data.user?.length" class="favourite_users margin-side">
            <h3>Владельцы</h3>
            <div>
                <user-block :user="object" v-for="object in data.user" />
            </div>
        </div>
        <div v-if="data.post?.length" class="favourite_posts">
            <h3 class="margin-side">Объявления</h3>
            <div class="home_block_posts_container">
                <post-block :object="post" v-for="post in data.post"/>
            </div>
        </div>
        <div v-if="data.service?.length" class="favourite_services">
            <h3 class="margin-side">Услуги</h3>
            <div class="home_block_posts_container">
                <post-block :object="post" type="service" v-for="post in data.service"/>
            </div>
        </div>
        <div v-if="data.event?.length" class="favourite_events margin-side">
            <h3>Мероприятия</h3>
            <div class="myevents_main">
<!--                <event-block event="" />-->
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>