<script>
import config from "@/config.json"
import {notify, toLink} from "@/utils.js";
import axios from "axios";

export default {
    name: "MyPostsView",
    methods: {
        toLink,
        async deletePost (id) {
            const name = this.user.my.posts.find(el => el.id === id).title;
            if (confirm(`Вы уверены, что хотите удалить объявление \"${name}\"?`)) {
                await axios.post(config.backend + "post/" + id + "/delete", {
                    initData: window.Telegram.WebApp.initData,
                }).then((response) => {
                    notify("Объявление успешно удалено!");
                    this.user.my.posts = this.user.my.posts.filter(el => el.id !== id);
                }).catch((error) => {
                    if (error.response) {
                        return alert (`An error occurred: ${error.message}`);
                    }
                });
            }
        }
    },
    data () {
        return {
            config: config
        }
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
            <div class="block_post" v-for="object in user.my?.posts">
                <div class="block_post_img">
                    <img :src="config.storage + object.pictures[0]?.url" alt="">
                    <div class="green-bgc">
                        <img src="/star.svg" alt="">
                        <div class="grey-light">{{ object.rating }}</div>
                    </div>
                </div>
                <div class="block_post_info">
                    <div class="sign">{{ object.title }}</div>
                    <div class="grey sign">{{ object.category.name }}</div>
                </div>
                <div class="block_post_location">
                    <img src="/location.svg" alt="">
                    <div class="sign">{{ object.city.name }}</div>
                </div>
                <div class="block_post_buttons">
                    <button><img src="/share.svg" alt=""></button>
                    <button><img src="/edit.svg" alt=""></button>
                    <button @click="deletePost(object.id)"><img src="/trash.svg" alt=""></button>
                </div>
            </div>
        </div>
        <div @click="toLink('store_post')" class="home_block_button green-bgc button">
            <div>
                <img src="/plus.svg" alt="">
                <div class="button">Добавить объявление</div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>