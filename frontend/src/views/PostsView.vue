<script>
import axios from "axios";
import config from "@/config.json";
import PostBlock from "@/components/PostBlock.vue";
import {hideList, openList} from "@/utils.js";
import { ElSlider } from "element-plus";
import "element-plus/dist/index.css"

export default {
    name: "PostsView",
    components: {PostBlock, ElSlider},
    data () {
        return {
            data: {},
            feed: [],
            selectedCategory: null,
            isLoading: false,
            isFull: false,
            filter: {},
            offset: 0,
            categories: [],
        }
    },
    async mounted () {
        await axios.get(config.backend + "data").then((response) => {
            this.data = response.data;
            if (this.$route.query.id === 'post')
                this.categories = response.data.categories;
            else if (this.$route.query.id === "service")
                this.categories = response.data.types;
        });
        await axios.get(config.backend + this.$route.query.id).then((response) => {
            this.feed = response.data;
        })

        window.addEventListener("scroll", (ev) => {
            if (this.isFull) return;
            let el = document.querySelector(".posts_main>.block_post:nth-last-of-type(3)");
            if (el.getBoundingClientRect().top < window.innerHeight) this.fetchData();
        })
    },
    methods: {
        openList,
        hideList,
        async fetchData () {
            if (this.isLoading) return;
            this.isLoading = true;
            this.isFull = false;

            let query = config.backend + this.$route.query.id + "?offset=" + this.feed.length;
            if (this.selectedCategory) query += "&category=" + this.selectedCategory;
            if (this.filter.search) query += "&s=" + this.filter.search;
            if (this.filter.age) {
                query += "&age_from=" + this.filter.age[0];
                query += "&age_to=" + this.filter.age[1];
            }
            for (let keyFilter in this.filter)
                query += `&${keyFilter}=` + this.filter[keyFilter];

            await axios.get(query).then((response) => {
                if (response.data.length === 0) return this.isFull = true;
                this.feed = this.feed.concat(response.data);
            }).finally(() => {
                this.hideFilter();
                this.isLoading = false;
            })
        },
        async changeCategory (id) {
            this.selectedCategory = id;
            this.feed = [];

            await this.fetchData();
        },
        async showFilter () {
            this.$refs.filter.style.display='';
        },
        async hideFilter () {
            this.$refs.filter.style.display='none';
        },
    }
}
</script>

<template>
    <div ref="filter" style="display:none" class="filter">
        <h2>Фильтровать</h2>
        <div class="filter_container">
            <div style="z-index:11" class="store_input_select_container">
                <div ref="breed" @click="openList" class="store_input_select">
                    <div class="store_input_select_main">
                        <div v-if="!filter.breed">Порода</div>
                        <div v-else>{{ data.breeds.find(el => el.id === filter.breed).name }}</div>
                        <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                    </div>
                </div>
                <div class="store_input_select_list">
                    <div v-for="br in data.breeds" @click="filter.breed = br.id; hideList($event)">
                        <div>{{ br.name }}</div>
                    </div>
                </div>
            </div>
            <div style="z-index:10" class="store_input_container">
                <div class="store_input_select_container">
                    <div ref="gender" @click="openList" class="store_input_select">
                        <div class="store_input_select_main">
                            <div v-if="filter.gender == null">Пол</div>
                            <div v-else-if="filter.gender === 0">
                                <img src="/female.svg" alt="">
                                <div>Сука</div>
                            </div>
                            <div v-else-if="filter.gender === 1">
                                <img src="/male.svg" alt="">
                                <div>Кобель</div>
                            </div>
                            <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                        </div>
                    </div>
                    <div class="store_input_select_list">
                        <div @click="filter.gender = 0; hideList($event)">
                            <img src="/female.svg" alt="">
                            <div>Сука</div>
                        </div>
                        <div @click="filter.gender = 1; hideList($event)">
                            <img src="/male.svg" alt="">
                            <div>Кобель</div>
                        </div>
                    </div>
                </div>
                <div class="store_input_select_container">
                    <div ref="category" class="store_input_select_container">
                        <div @click="openList" class="store_input_select">
                            <div class="store_input_select_main">
                                <div v-if="!filter.category">Категория</div>
                                <div v-else>{{categories.find(el => el.id === filter.category).name}}</div>
                                <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                            </div>
                        </div>
                        <div class="store_input_select_list">
                            <div v-for="catg in categories" @click="filter.category = catg.id; hideList($event)">
                                <div>{{ catg.name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="z-index:5" class="store_input_select_container">
                <div ref="city" @click="openList" class="store_input_select">
                    <div class="store_input_select_main">
                        <div v-if="!filter.city">Город</div>
                        <div v-else>{{ data.cities.find(el => el.id === filter.city).name }}</div>
                        <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                    </div>
                </div>
                <div class="store_input_select_list">
                    <div v-for="ct in data.cities" @click="filter.city = ct.id; hideList($event)">
                        <div>{{ ct.name }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter_age">
            <div class="filter_age_title">Возраст</div>
            <div class="slider-wrapper">
                <el-slider
                    v-model="filter.age"
                    :min="0"
                    :max="120"
                    :step="1"
                    range
                    :show-tooltip="false"
                    class="custom-slider"
                    height="12px"
                />
            </div>
            <div class="filter_age_sign">
                <div>0</div>
                <div>10 лет</div>
            </div>
        </div>
        <div style="z-index:4" class="store_input_container">
            <input ref="price" v-model="filter.price_from" type="number" placeholder="от">
            <input ref="price" v-model="filter.price_to" type="number" placeholder="до">
        </div>
        <div class="filter_checkbox_container">
            <div @click="filter.rating = !filter.rating ?? true"  class="filter_checkbox">
                <img v-if="filter.rating" src="/check.svg" alt="">
            </div>
            <div @click="filter.rating = !filter.rating ?? true" class="filter_checkbox_text">Рейтинг выше 4</div>
        </div>
        <div class="filter_checkbox_container">
            <div @click="filter.isNew = !filter.isNew ?? true"  class="filter_checkbox">
                <img v-if="filter.isNew" src="/check.svg" alt="">
            </div>
            <div @click="filter.isNew = !filter.isNew ?? true" class="filter_checkbox_text">Сначала новые</div>
        </div>
        <button @click="isFull = false; feed = []; fetchData()" class="button">Применить</button>
    </div>
    <div class="posts">
        <h1 class="margin-all">Объявления</h1>
        <div class="posts_search_container margin-side">
            <div class="posts_search">
                <img src="/search.svg" alt="">
                <input v-model="filter.search" @blur="isFull = false; feed = []; fetchData();" type="text" placeholder="Найти...">
            </div>
            <button @click="showFilter"><img src="/filter.svg" alt=""></button>
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