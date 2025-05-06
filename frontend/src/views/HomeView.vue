<script>
import PostBlock from "@/components/PostBlock.vue";
import {hideOverlay, showOverlay, toLink} from "@/utils.js";

export default {
    name: "HomeView",
    methods: {showOverlay, hideOverlay, toLink},
    components: {PostBlock},
    async mounted () {
        window.Telegram.WebApp.disableVerticalSwipes();
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
    data () {
        return {
            selectedId: null,
        }
    }
}
</script>

<template>
    <div style="display:none" @click="hideOverlay('settings')" class="background postOverlay"></div>
    <div style="display:none" class="overlay postOverlay">
        <div @click="hideOverlay('settings')"  class="overlay_button"><div></div></div>
        <div class="profile_settings_main">
            <div class="profile_settings_main_title">Объявление (in progress)</div>
            <div class="profile_settings_main_el">
                <div class="profile_settings_mail_el_title">Пользователь</div>
                <button @click="toLink('dialog', selectedId)"><img src="/edit.svg" alt=""></button>
            </div>
        </div>
    </div>
    <div class="home">
        <div v-if="user.feed?.posts?.length !== 0" class="home_block">
            <div class="home_block_header margin-side">
                <h1>Объявления</h1>
                <div class="button green-bgc">
                    <img src="/arrow.svg" alt="">
                </div>
            </div>
            <div class="home_block_description grey margin-side">
                Продажа, покупка собак, щенки, предложения вязки, анонсы помёта
            </div>
            <div class="home_block_posts_container">
                <post-block @click="showOverlay('postOverlay'); selectedId = post.user_id" :title="post.title" :type="post.category.name" :city="post.city.name"
                    :price="post.price" :rating="post.rating" v-for="post in user.feed?.posts"/>
            </div>
            <div @click="toLink('store_post')" class="home_block_button green-bgc button margin-side">
                <div>
                    <img src="/plus.svg" alt="">
                    <div class="button">Добавить</div>
                </div>
            </div>
        </div>
        <div class="home_block">
            <div class="home_block_header margin-side">
                <h1>Услуги</h1>
                <div class="button green-bgc">
                    <img src="/arrow.svg" alt="">
                </div>
            </div>
            <div v-if="user.feed?.services?.length !== 0" class="home_block_posts_container">
                <post-block :title="service.title" :type="service.category.name" :city="service.city.name"
                    :price="service.price" :rating="service.rating" v-for="service in user.feed?.services"/>
            </div>
            <div @click="toLink('store_service')" class="home_block_button green-bgc button margin-side">
                <div>
                    <img src="/plus.svg" alt="">
                    <div class="button">Добавить</div>
                </div>
            </div>
        </div>
        <div v-if="user.feed?.popular?.length !== 0 && user.feed?.popular !== null" class="home_block">
            <div class="home_block_header margin-side">
                <h1>Популярное</h1>
            </div>
            <div class="home_block_posts_container">
                <post-block :title="popular.title" :type="popular.category.name" :city="popular.city.name"
                            :price="popular.price" :rating="popular.rating" v-for="popular in user.feed?.popular"/>
            </div>
            <div class="home_block_button green-bgc button margin-side">
                <div>
                    <img src="/plus.svg" alt="">
                    <div class="button">Добавить</div>
                </div>
            </div>
        </div>
        <div class="home_block">
            <div class="home_block_header margin-side">
                <h1>Мероприятие</h1>
                <div class="button green-bgc">
                    <img src="/arrow.svg" alt="">
                </div>
            </div>
            <div v-if="user.feed?.events?.length !== 0" class="home_block_posts_container">
                <post-block :title="event.title" :type="event.category.name" :city="event.city.name"
                            :price="event.price" :rating="event.rating" v-for="event in user.feed?.events"/>
            </div>
            <div class="home_block_button green-bgc button margin-side">
                <div>
                    <img src="/plus.svg" alt="">
                    <div class="button">Добавить</div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>