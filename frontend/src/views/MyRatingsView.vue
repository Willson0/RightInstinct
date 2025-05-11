<script>
import axios from "axios";
import config from "@/config.json"
import RatingBlock from "@/components/RatingBlock.vue";
export default {
    name: "MyRatingsView",
    components: {RatingBlock},
    data () {
        return {
            reviews: [],
            config: config,
        }
    },
    async mounted () {
        await this.fetchData();
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
    methods: {
        async fetchData () {
            await axios.post(config.backend + "rating", {
                initData: window.Telegram.WebApp.initData,
            }).then((response) => {
                this.reviews = response.data;
            }).catch((error) => {
                if (error.response)
                    alert (error.message);
            })
        },
        async rate (rating, type, id) {
            if (this.isLoading) return;

            this.isLoading = true;
            await axios.post(config.backend + "rating/rate", {
                initData: window.Telegram.WebApp.initData,
                type: type,
                object_id: id,
                rating: rating,
            }).then((response) => {
                this.user.reviews[type].find(el => el.id === id).rating = rating;
                this.$store.dispatch("updateUser", this.user);

                this.fetchData();
            }).catch((error) => {
                if (error.response) {
                    return alert (`An error occurred: ${error.message}`);
                }
            }).finally(() => {
                this.isLoading = false;
            });
        },
        ratingNow (type, id) {
            if (!Array.isArray(this.user?.reviews[type])) this.user.reviews[type] = [];
            return this.user?.reviews[type].find(el => el.id === id)?.rating ?? 0;
        }
    }
}
</script>

<template>
    <div class="myratings margin-all">
        <h1>Мои оценки</h1>
        <div class="myratings_main">
            <div v-for="review in reviews">
                <div class="block_post_img">
                    <img :src="config.storage + review.object.pictures[0].url" alt="">
                    <div class="green-bgc">
                        <img src="/star.svg" alt="">
                        <div class="grey-light">{{ review.object.rating }}</div>
                    </div>
                </div>
                <div class="myratings_el_info">
                    <div class="myratings_el_info_text">
                        <div class="myratings_el_info_title sign">{{ review.object.title }}</div>
                        <div class="myratings_el_info_category sign">{{ review.object.category.name }}</div>
                    </div>
                    <div class="ratingOverlay_main_stars">
                        <img @click.stop="rate(star, review.type, review.object_id)" v-for="star in ratingNow(review.type, review.object_id)" src="/star.svg" alt="">
                        <img @click.stop="rate(star + ratingNow(review.type, review.object_id), review.type, review.object_id)" v-for="star in (5 - ratingNow(review.type, review.object_id))" src="/star_disabled.svg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>