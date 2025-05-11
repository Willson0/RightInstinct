<script>
import config from "@/config.json"
import {hideOverlay, notify, showOverlay, toLink} from "@/utils.js";
import axios from "axios";
import PostBlock from "@/components/PostBlock.vue";

export default {
    name: "MyPostsView",
    components: {PostBlock},
    methods: {
        hideOverlay,
        showOverlay,
        toLink,
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
}
</script>

<template>
    <div class="myposts margin-all">
        <h1>Мои объявления</h1>
        <div class="myposts_main">
            <post-block :object="object" v-for="object in user.my?.posts" :my="true" />
        </div>
        <div @click="toLink('store', 'post')" class="home_block_button green-bgc button">
            <div>
                <img src="/plus.svg" alt="">
                <div class="button">Добавить объявление</div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>