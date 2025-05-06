<script>
import PostBlock from "@/components/PostBlock.vue";
import {toLink} from "@/utils.js";

export default {
    name: "HomeView",
    methods: {toLink},
    components: {PostBlock},
    async mounted () {
        window.Telegram.WebApp.disableVerticalSwipes();
    }
}
</script>

<template>
    <div class="home">
        <div class="home_block">
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
                <post-block :title="'Кобель на вязку'" :type="'Вязка'"
                :city="'Екатеринбург'" :price="40000" :rating="4.9" v-for="post in 10"/>
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
            <div class="home_block_posts_container">
                <post-block :title="'Кобель на вязку'" :type="'Вязка'"
                            :city="'Екатеринбург'" :price="40000" :rating="4.9" v-for="post in 10"/>
            </div>
            <div @click="toLink('store_service')" class="home_block_button green-bgc button margin-side">
                <div>
                    <img src="/plus.svg" alt="">
                    <div class="button">Добавить</div>
                </div>
            </div>
        </div>
        <div class="home_block">
            <div class="home_block_header margin-side">
                <h1>Популярное</h1>
            </div>
            <div class="home_block_posts_container">
                <post-block :title="'Кобель на вязку'" :type="'Вязка'"
                            :city="'Екатеринбург'" :price="40000" :rating="4.9" v-for="post in 10"/>
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
            <div class="home_block_posts_container">
                <post-block :title="'Кобель на вязку'" :type="'Вязка'"
                            :city="'Екатеринбург'" :price="40000" :rating="4.9" v-for="post in 10"/>
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