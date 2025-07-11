<script>
import PostBlock from "@/components/PostBlock.vue";
import {hideOverlay, showOverlay, toLink} from "@/utils.js";
import EventBlock from "@/components/EventBlock.vue";
import PhotoSlider from "@/components/PhotoSlider.vue";

export default {
    name: "HomeView",
    methods: {showOverlay, hideOverlay, toLink},
    components: {PhotoSlider, EventBlock, PostBlock},
    async mounted () {
        // window.Telegram.WebApp.disableVerticalSwipes();
        if (this.user.feed) this.feed = this.user.feed;

        let startX = 0;
        let startY = 0;
        let isHorizontal = false;

        document.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isHorizontal = false;
        }, {passive: false});

        document.addEventListener('touchmove', function(e) {
            const dx = Math.abs(e.touches[0].clientX - startX);
            const dy = Math.abs(e.touches[0].clientY - startY);

            // Если жест явно горизонтальный — предотвратить вертикальный свайп
            if(dx > dy && dx > 10) {
                isHorizontal = true;
                e.stopPropagation(); // или e.preventDefault(), если скролл не нужен
            }
            // Если явно вертикальный, НЕ мешаем
        }, {passive: false});
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
    data () {
        return {
            selectedId: null,
            freeze: false,
            feed: [],
            newFeed: [],
        }
    },
    watch: {
        freeze () {
            this.feed = this.newFeed;
        },
        'user.feed' () {
            this.newFeed = this.user.feed;
            if (!this.freeze) {
                this.feed = this.newFeed;
            }
        }
    }
}
</script>

<template>
    <div class="home">
        <div class="home_block">
            <div class="home_block_header margin-side">
                <h1>Объявления</h1>
                <div @click="toLink('posts', 'post')" class="button green-bgc">
                    <img src="/arrow.svg" alt="">
                </div>
            </div>
            <div class="home_block_description grey margin-side">
                Продажа, покупка собак, щенки, предложения вязки, анонсы помёта
            </div>
            <div v-if="feed?.posts?.length !== 0" class="home_block_posts_container">
                <post-block :object="post" v-for="post in feed?.posts"
                            @freeze="freeze = true" @unfreeze="freeze = false"/>
            </div>
            <div @click="toLink('store', 'post')" class="home_block_button green-bgc button margin-side">
                <div>
                    <img src="/plus.svg" alt="">
                    <div class="button">Добавить</div>
                </div>
            </div>
        </div>
        <div class="home_block">
            <div class="home_block_header margin-side">
                <h1>Услуги</h1>
                <div @click="toLink('posts', 'service')" class="button green-bgc">
                    <img src="/arrow.svg" alt="">
                </div>
            </div>
            <div v-if="feed?.services?.length !== 0" class="home_block_posts_container">
                <post-block :object="service" type="service" v-for="service in feed?.services"
                            @freeze="freeze = true" @unfreeze="freeze = false"/>
            </div>
            <div @click="toLink('store', 'service')" class="home_block_button green-bgc button margin-side">
                <div>
                    <img src="/plus.svg" alt="">
                    <div class="button">Добавить</div>
                </div>
            </div>
        </div>
        <div v-if="feed?.popular?.length !== 0 && feed?.popular !== null" class="home_block">
            <div class="home_block_header margin-side">
                <h1>Популярное</h1>
            </div>
            <div class="home_block_posts_container">
                <post-block :object="pop" :type="pop.breed ? 'post' : 'service'"
                            v-for="pop in feed.popular" @freeze="freeze = true" @unfreeze="freeze = false"/>
            </div>
        </div>
        <div class="home_block">
            <div class="home_block_header margin-side">
                <h1>Мероприятие</h1>
                <div @click="toLink('events')" class="button green-bgc">
                    <img src="/arrow.svg" alt="">
                </div>
            </div>
            <div v-if="feed?.events?.length !== 0" class="home_block_posts_container">
                <event-block :event="event" v-for="event in feed?.events"
                             @freeze="freeze = true" @unfreeze="freeze = false"/>
            </div>
            <div @click="toLink('store', 'event')" class="home_block_button green-bgc button margin-side">
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