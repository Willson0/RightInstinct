<script>
import config from "@/config.json"
import {hideOverlay, notify, showOverlay, toLink} from "@/utils.js";
import axios from "axios";

export default {
    name: "MyPostsView",
    methods: {
        hideOverlay,
        showOverlay,
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
            config: config,
            selectedPost: {},
        }
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
        beautifullyPrice () {
            return this.selectedPost.price?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }
    },
}
</script>

<template>
    <div style="display:none" @click="hideOverlay('postOverlay')" class="background postOverlay"></div>
    <div style="display:none" class="overlay postOverlay">
        <div @click="hideOverlay('postOverlay')" class="overlay_button"><div></div></div>
        <div class="postOverlay_main">
            <div class="postOverlay_main_photos" v-if="selectedPost.pictures?.length !== 0">
                <img v-for="img in selectedPost.pictures" :src="config.storage + img.url" alt="">
            </div>
            <div class="postOverlay_mainContainer">
                <h4>{{ selectedPost.title }}</h4>
                <div class="input">{{ selectedPost.breed?.name }}</div>
                <div class="postOverlay_main_info">
                    <div class="postOverlay_main_info_age">
                        <img :src="selectedPost.gender ? '/male.svg' : '/female.svg'" alt="">
                        <div class="input">{{ selectedPost.age }} месяцев</div>
                    </div>
                    <div class="postOverlay_main_info_location">
                        <img src="/location.svg" alt="">
                        <div class="input">{{ selectedPost.city?.name }}</div>
                    </div>
                </div>
            </div>
            <div class="postOverlay_main_description">
                {{ selectedPost.description }}
            </div>
            <div class="postOverlay_main_rewards">
                <img src="/star.svg" alt="">
                <div>{{ selectedPost.rewards }}</div>
            </div>
            <div class="postOverlay_main_buttons my">
                <div class="button"><h3>{{ beautifullyPrice }} ₽</h3></div>
                <button><img src="/share.svg" alt=""></button>
                <button><img src="/edit.svg" alt=""></button>
                <button @click="deletePost(selectedPost.id)"><img src="/trash.svg" alt=""></button>
            </div>
        </div>
    </div>
    <div class="myposts margin-all">
        <h1>Мои объявления</h1>
        <div class="myposts_main">
            <div class="block_post" @click="selectedPost = object; showOverlay('postOverlay')" v-for="object in user.my?.posts">
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