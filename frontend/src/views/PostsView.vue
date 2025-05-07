<script>
import axios from "axios";
import config from "@/config.json";
import PostBlock from "@/components/PostBlock.vue";

export default {
    name: "PostsView",
    components: {PostBlock},
    data () {
        return {
            categories: null,
            feed: [],
            selectedCategory: null,
            isLoading: false,
            isFull: false,
        }
    },
    async mounted () {
        await axios.get(config.backend + "data/category").then((response) => {
            this.categories = response.data;
        });
        await axios.get(config.backend + "post").then((response) => {
            this.feed = response.data;
        })

        window.addEventListener("scroll", (ev) => {
            if (this.isFull) return;
            let el = document.querySelector(".posts_main>.block_post:nth-last-of-type(3)");
            if (el.getBoundingClientRect().top < window.innerHeight) this.fetchData();
        })
    },
    methods: {
        async fetchData () {
            if (this.isLoading) return;
            this.isLoading = true;
            this.isFull = false;

            let query = config.backend + "post?offset=" + this.feed.length;
            if (this.selectedCategory) query += "&category=" + this.selectedCategory;

            await axios.get(query).then((response) => {
                if (response.data.length === 0) return this.isFull = true;
                this.feed = this.feed.concat(response.data);
            }).finally(() => {
                this.isLoading = false;
            })
        },
        async changeCategory (id) {
            this.selectedCategory = id;
            this.feed = [];

            await this.fetchData();
        }
    }
}
</script>

<template>
    <div class="posts">
        <h1 class="margin-all">Объявления</h1>
        <div class="posts_search_container margin-side">
            <div class="posts_search">
                <img src="/search.svg" alt="">
                <input type="text" placeholder="Найти...">
            </div>
            <button><img src="/filter.svg" alt=""></button>
        </div>
        <div class="posts_categories">
            <h4 @click="changeCategory(category.id)" v-for="category in categories"
                :class="selectedCategory === category.id ? 'active' : ''">
                {{ category.name }}
            </h4>
        </div>
        <div class="posts_main margin-side">
            <post-block v-for="obj in feed" :object="obj" />
        </div>
    </div>
</template>

<style scoped>

</style>