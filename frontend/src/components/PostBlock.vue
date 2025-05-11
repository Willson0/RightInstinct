<script>
import {favourite, hideOverlay, notify, showOverlay, toLink} from "@/utils.js";
import config from "@/config.json";
import RatingBlock from "@/components/RatingBlock.vue";
import axios from "axios";

export default {
    name: "PostBlock",
    components: {RatingBlock},
    methods: {
        favourite,
        toLink,
        showOverlay (cl) {
            this.overlay = true;
            requestAnimationFrame(() => {
                document.body.style.overflow = "hidden";

                let el = document.querySelector(`.overlay.${cl}`);
                el.style.display = "";
                el.style.transform = "translateY(100%)";

                let background = document.querySelector(`.background.${cl}`);
                background.style.display = "";
                background.style.opacity = 0;

                requestAnimationFrame(() => {
                    el.style.transform = "";
                    background.style.opacity = "";
                });
            })
        },
        hideOverlay (cl) {
            let el = document.querySelector(`.overlay.${cl}`);
            el.style.transform = "translateY(100%)";

            let background = document.querySelector(`.background.${cl}`);
            background.style.opacity = 0;

            setTimeout(() => {
                el.style.transform = "";
                background.style.opacity = "";
                background.style.display = "none";

                el.style.display = "none";
                document.body.style.overflow = "";
                this.overlay = false;
            }, 200);
        },
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
            selectedID: null,
            overlay: false,
            config: config,
            isLoading: {status: false},
        }
    },
    props: {
        object: {
            type: Object,
            required: true
        },
        clickable: {
            type: Boolean,
            default: true
        },
        type: {
            type: String,
            default: "post",
        },
        my: {
            type: Boolean,
            default: false,
        }
    },
    computed: {
        beautifullyPrice () {
            return this.object.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        },
        user () {
            return this.$store.state.user;
        },
    }
}
</script>

<template>
    <div v-if="overlay" style="display:none" @click="hideOverlay('postOverlay')" class="background postOverlay"></div>
    <div v-if="overlay" style="display:none" class="overlay postOverlay">
        <div @click="hideOverlay('postOverlay')" class="overlay_button"><div></div></div>
        <div class="postOverlay_main">
            <div class="postOverlay_main_photos" v-if="object.pictures?.length !== 0">
                <div>
                    <img :src="config.storage + object.pictures[0]?.url" alt="">
                    <div class="green-bgc">
                        <img src="/star.svg" alt="">
                        <div class="grey-light">{{ object.rating }}</div>
                    </div>
                </div>
                <img v-for="img in object.pictures.slice(1)" :src="config.storage + img.url" alt="">
            </div>
            <div class="postOverlay_mainContainer">
                <h4>{{ object.title }}</h4>
                <div v-if="['post'].includes(type)" class="input">{{ object.breed.name }}</div>
                <div class="postOverlay_main_info">
                    <div v-if="['post'].includes(type)" class="postOverlay_main_info_age">
                        <img :src="object.gender ? '/male.svg' : '/female.svg'" alt="">
                        <div class="input">{{ object.age }} месяцев</div>
                    </div>
                    <h4 v-if="['service'].includes(type)" class="postOverlay_main_info_category">
                        {{ object.category.name }}
                    </h4>
                    <div class="postOverlay_main_info_location">
                        <img src="/location.svg" alt="">
                        <div class="input">{{ object.city.name }}</div>
                    </div>
                </div>
            </div>
            <div class="postOverlay_main_description">
                {{ object.description }}
            </div>
            <div v-if="['post'].includes(type)" class="postOverlay_main_rewards">
                <img src="/star.svg" alt="">
                <div>{{ object.rewards }}</div>
            </div>
            <div v-if="!my" @click="toLink('user', object.user.id)" class="postOverlay_main_user">
                <img :src="object.user.avatar" alt="">
                <div>
                    <h4>Хозяин</h4>
                    <div class="sign">{{ object.user.fullname }}</div>
                </div>
            </div>
            <div v-if="!my" class="postOverlay_main_buttons">
                <div class="button"><h3>{{ beautifullyPrice }} ₽</h3></div>
                <button><img src="/press.svg" alt=""></button>
                <button @click.stop="favourite(!user?.favourites[type]?.includes(object.id), type, object.id, isLoading, user)">
                    <img v-if="user?.favourites[type]?.includes(object.id)"
                         src="/like_active.svg" style="width:24px; height: 24px;" alt="">
                    <img v-else src="/like.svg" alt="">
                </button>
                <button><img style="width: 24px; height: 24px;" src="/share.svg" alt=""></button>
            </div>
            <div v-if="my" class="postOverlay_main_buttons">
                <div class="button"><h3>{{ beautifullyPrice }} ₽</h3></div>
                <button><img style="width: 24px; height: 24px;" src="/share.svg" alt=""></button>
                <button @click="toLink('update', object.id, type)">
                    <img style="width: 24px; height: 24px;" src="/edit.svg" alt="">
                </button>
                <button @click="deletePost(object.id)"><img style="width: 24px; height: 24px;" src="/trash.svg" alt=""></button>
            </div>
            <button v-if="!my" class="button" @click="toLink('dialog', object.user.id)">Связаться с продавцом</button>
            <a v-if="!my" :href="config.complain" class="postOverlay_main_complain">Пожаловаться</a>
        </div>
    </div>
    <div @click="clickable ? showOverlay('postOverlay') : ''" class="block_post">
        <div class="block_post_img">
            <img :src="config.storage + object.pictures[0]?.url" alt="">
            <div v-if="my" class="green-bgc">
                <img src="/star.svg" alt="">
                <div class="grey-light">{{ object.rating }}</div>
            </div>
            <rating-block v-else :id="object.id" :rating="object.rating" :type="type"/>
        </div>
        <div class="block_post_info">
            <div class="sign">{{ object.title }}</div>
            <div class="grey sign">{{ object.category.name }}</div>
        </div>
        <div class="block_post_location">
            <img src="/location.svg" alt="">
            <div class="sign">{{ object.city.name }}</div>
        </div>
        <div v-if="my" class="block_post_buttons">
            <button><img src="/share.svg" alt=""></button>
            <button @click.stop="toLink('update', object.id, type)"><img src="/edit.svg" alt=""></button>
            <button @click.stop="deletePost(object.id)"><img src="/trash.svg" alt=""></button>
        </div>
        <div v-else class="block_post_footer">
            <div class="block_post_price h3">
                <div>
                    {{ beautifullyPrice }} ₽
                </div>
            </div>
            <div v-if="user && user.favourites" class="button" @click.stop="favourite(!user?.favourites[type]?.includes(object.id), type, object.id, isLoading, user)">
                <img v-if="user?.favourites[type]?.includes(object.id)"
                     src="/like_active.svg" style="width:24px; height: 24px;" alt="">
                <img v-else src="/like.svg" alt="">
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>