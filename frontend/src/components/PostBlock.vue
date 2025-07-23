<script>
import {complain, copy, favourite, notify, toLink} from "@/utils.js";
import config from "@/config.json";
import RatingBlock from "@/components/RatingBlock.vue";
import axios from "axios";
import PhotoSlider from "@/components/PhotoSlider.vue";

export default {
    name: "PostBlock",
    components: {PhotoSlider, RatingBlock},
    methods: {
        copy,
        complain,
        favourite,
        toLink,
        showOverlay (cl) {
            this.$emit('freeze');
            this.overlay = true;
            this.$nextTick(() => {
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
            this.$emit('unfreeze');

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
            const enums = {
                "post": {translate: 'объявление', my: 'posts'},
                "service": {translate: 'услугу', my: 'services'},
            }

            const name = this.user.my[enums[this.type].my].find(el => el.id === id).title;
            if (confirm(`Вы уверены, что хотите удалить ${enums[this.type].translate} \"${name}\"?`)) {
                await axios.post(config.backend + this.type + "/" + id + "/delete", {
                    initData: window.Telegram.WebApp.initData,
                }).then((response) => {
                    notify("Удаление успешно завершено!");
                    this.user.my[enums[this.type].my] =
                        this.user.my[enums[this.type].my].filter(el => el.id !== id);
                }).catch((error) => {
                    if (error.response) {
                        return alert (`An error occurred: ${error.message}`);
                    }
                });
            }
        },
        openFullScreen(ev) {
            this.overlayImage = ev.target.src;
            document.body.style.overflow = 'hidden';
        },
        closeFullScreen() {
            this.overlayImage = null
            document.body.style.overflow = '';
        }
    },
    data () {
        return {
            selectedID: null,
            overlay: false,
            config: config,
            isLoading: {status: false},
            overlayImage: null,
            startIndex: null,
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
    <PhotoSlider @close="startIndex=null" v-if="startIndex !== null" :images="object.pictures.map(item => item.url)" :start-index="startIndex" />
    <div v-if="overlayImage" class="image-overlay" @click="closeFullScreen">
        <img :src="overlayImage" alt="" />
    </div>
    <div v-if="overlay" style="display:none" @click="hideOverlay('postOverlay')" class="background postOverlay"></div>
    <div v-if="overlay" style="display:none" class="overlay postOverlay">
        <div @click="hideOverlay('postOverlay')" class="overlay_button"><div></div></div>
        <div class="postOverlay_main">
            <div class="postOverlay_main_photos" v-if="object.pictures?.length !== 0">
                <div>
                    <rating-block :zid="true" @click.stop="$event.preventDefault()"
                                  :rating="object.rating" :type="type" :id="object.id" />
                    <a target="_blank" :href="object.link">
                        <img @click="object.link ? '' : startIndex = 0" :src="config.storage + object.pictures[0]?.url" alt="">
                        <img v-if="object.link" class="postOverlay_main_photos_video" src="/play.svg">
                    </a>
                </div>
                <img @click="startIndex = index+1" v-for="(img, index) in object.pictures.slice(1)" :src="config.storage + img.url" alt="">
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
            <div class="postOverlay_main_description" lang="ru">
                {{ object.description }}
            </div>
            <div v-if="['post'].includes(type) && object.rewards != null" class="postOverlay_main_rewards">
                <img src="/star.svg" alt="">
                <div>{{ object.rewards }}</div>
            </div>
            <div v-if="!my" @click="toLink('user', object.user.id)" class="postOverlay_main_user">
                <img :src="object.user.avatar" alt="">
                <div>
                    <h4>Владелец</h4>
                    <div class="sign">{{ object.user.fullname }}</div>
                </div>
            </div>
            <div v-if="!my" class="postOverlay_main_buttons">
                <div class="button"><h3>{{ beautifullyPrice }} ₽</h3></div>
<!--                <button><img src="/press.svg" alt=""></button>-->
                <button @click.stop="favourite(!user?.favourites[type]?.includes(object.id), type, object.id, isLoading, user)">
                    <img v-if="user?.favourites[type]?.includes(object.id)"
                         src="/like_active.svg" style="width:24px; height: 24px;" alt="">
                    <img v-else src="/like.svg" alt="">
                </button>
                <button @click.stop="copy(type, object.id)"><img style="width: 24px; height: 24px;" src="/share.svg" alt=""></button>
            </div>
            <div v-if="my" class="postOverlay_main_buttons">
                <div class="button"><h3>{{ beautifullyPrice }} ₽</h3></div>
                <button @click="copy(type, object.id)"><img style="width: 24px; height: 24px;" src="/share.svg" alt=""></button>
                <button @click="toLink('update', object.id, type)">
                    <img style="width: 24px; height: 24px;" src="/edit.svg" alt="">
                </button>
                <button @click="deletePost(object.id)"><img style="width: 24px; height: 24px;" src="/trash.svg" alt=""></button>
            </div>
            <button v-if="!my" class="button" @click="toLink('dialog', object.user.id)">Связаться с продавцом</button>
<!--            <a v-if="!my" :href="config.complain" class="postOverlay_main_complain">Пожаловаться</a>-->
            <a v-if="!my" @click="complain(type, object.id)" class="postOverlay_main_complain">Пожаловаться</a>
        </div>
    </div>
    <div @click="clickable ? showOverlay('postOverlay') : ''" class="block_post">
        <div class="block_post_img">
            <img :src="config.storage + object.pictures[0]?.url" alt="">
            <div v-if="my" class="green-bgc">
                <img src="/star.svg" alt="">
                <div class="grey-light">{{ object.rating }}</div>
            </div>
            <rating-block v-else :id="object.id" @unfreeze="$emit('unfreeze')"
                          @freeze="$emit('freeze')" :rating="object.rating" :type="type"/>
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
            <button @click="copy(type, object.id)"><img src="/share.svg" alt=""></button>
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