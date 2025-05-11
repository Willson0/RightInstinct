<script>
import config from "@/config.json";
import axios from "axios";
export default {
    name: "RatingBlock",
    data () {
        return {
            overlay: false,
            isLoading: false,
        }
    },
    methods: {
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
        async rate (rating) {
            if (this.isLoading) return;

            this.isLoading = true;
            await axios.post(config.backend + "rating/rate", {
                initData: window.Telegram.WebApp.initData,
                type: this.type,
                object_id: this.id,
                rating: rating,
            }).then((response) => {
                axios.post(config.backend + "auth/profile", {
                    "initData": window.Telegram.WebApp.initData,
                }).then((response) => {
                    this.$store.dispatch("updateUser", response.data);
                })
            }).catch((error) => {
                if (error.response) {
                    return alert (`An error occurred: ${error.message}`);
                }
            }).finally(() => {
                this.isLoading = false;
            });
        },
        nothing () {},
    },
    props: {
        rating: {
            type: Number,
            default: 0,
        },
        type: {
            type: String,
            default: "post"
        },
        id: {
            type: Number,
            required: true,
        },
    },
    computed: {
        user () {
            return this.$store.state.user;
        },
        ratingNow () {
            if (!Array.isArray(this.user?.reviews[this.type])) this.user.reviews[this.type] = [];
            return this.user?.reviews[this.type].find(el => el.id === this.id)?.rating ?? 0;
        }
    }
}
</script>

<template>
    <div style="background-color:transparent; padding: 0;">
        <div v-if="overlay" style="display:none" @click.stop="hideOverlay('ratingOverlay')" class="background ratingOverlay"></div>
        <div v-if="overlay" @click.stop="nothing" style="display:none" class="overlay ratingOverlay">
            <div @click.stop="hideOverlay('ratingOverlay')" class="overlay_button"><div></div></div>
            <div @click.stop="nothing" class="ratingOverlay_main">
                <div class="ratingOverlay_main_title">Оценить</div>
                <div class="ratingOverlay_main_stars">
                    <img @click.stop="rate(star)" v-for="star in ratingNow" src="/star.svg" alt="">
                    <img @click.stop="rate(star + ratingNow)" v-for="star in (5 - ratingNow)" src="/star_disabled.svg" alt="">
                </div>
            </div>
        </div>
    </div>
    <div @click.stop="showOverlay('ratingOverlay')" class="green-bgc">
        <img src="/star.svg" alt="">
        <div class="grey-light">{{ rating }}</div>
    </div>
</template>

<style scoped>

</style>